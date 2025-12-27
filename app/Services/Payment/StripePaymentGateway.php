<?php

namespace App\Services\Payment;

use App\Models\ProxyOrder;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Refund;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripePaymentGateway extends AbstractPaymentGateway
{
    protected string $apiKey;
    protected string $webhookSecret;
    protected bool $testMode;

    public function __construct()
    {
        $this->apiKey = config('payment.stripe.secret_key');
        $this->webhookSecret = config('payment.stripe.webhook_secret');
        $this->testMode = config('payment.stripe.test_mode', true);

        Stripe::setApiKey($this->apiKey);
    }

    /**
     * {@inheritDoc}
     */
    public function createPaymentIntent(ProxyOrder $order, array $options = []): array
    {
        try {
            $successUrl = $options['success_url'] ?? route('user.checkout.success', ['order' => $order->id]);
            $cancelUrl = $options['cancel_url'] ?? route('user.checkout.cancel', ['order' => $order->id]);

            $metadata = [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'user_id' => $order->user_id,
                'proxy_plan_id' => $order->proxy_plan_id,
            ];

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $order->plan->name,
                            'description' => "{$order->bandwidth_gb} GB - {$order->proxyType->name}",
                        ],
                        'unit_amount' => $this->formatAmount($order->amount_paid),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
                'client_reference_id' => $order->id,
                'metadata' => $metadata,
                'payment_intent_data' => [
                    'metadata' => $metadata,
                ],
            ]);

            $this->log('info', 'Payment intent created', [
                'order_id' => $order->id,
                'session_id' => $session->id,
                'amount' => $order->amount_paid,
            ]);

            return [
                'session_id' => $session->id,
                'redirect_url' => $session->url,
                'client_secret' => null, // Checkout Session doesn't expose client_secret
            ];
        } catch (\Exception $e) {
            $this->log('error', 'Failed to create payment intent', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function capturePayment(string $transactionId, float $amount): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($transactionId);

            if ($paymentIntent->status === 'requires_capture') {
                $paymentIntent->capture(['amount_to_capture' => $this->formatAmount($amount)]);
            }

            $this->log('info', 'Payment captured', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);

            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'message' => 'Payment captured successfully',
            ];
        } catch (\Exception $e) {
            $this->log('error', 'Failed to capture payment', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function refundPayment(string $transactionId, float $amount, ?string $reason = null): array
    {
        try {
            $refund = Refund::create([
                'payment_intent' => $transactionId,
                'amount' => $this->formatAmount($amount),
                'reason' => $reason ? 'requested_by_customer' : null,
                'metadata' => [
                    'reason' => $reason ?? 'No reason provided',
                ],
            ]);

            $this->log('info', 'Refund processed', [
                'transaction_id' => $transactionId,
                'refund_id' => $refund->id,
                'amount' => $amount,
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'message' => 'Refund processed successfully',
            ];
        } catch (\Exception $e) {
            $this->log('error', 'Failed to process refund', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'refund_id' => null,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function verifyWebhookSignature(string $payload, string $signature): bool
    {
        try {
            Webhook::constructEvent($payload, $signature, $this->webhookSecret);
            return true;
        } catch (SignatureVerificationException $e) {
            $this->log('warning', 'Webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function parseWebhookPayload(string $payload): array
    {
        $event = json_decode($payload, true);

        $eventType = $event['type'] ?? 'unknown';
        $data = $event['data']['object'] ?? [];

        // Extract order ID from metadata
        $orderId = $data['metadata']['order_id'] ?? null;

        // For charge events, metadata is directly on the charge object
        // For payment_intent events, metadata is on the payment_intent
        // For checkout.session events, metadata is on the session

        $transactionId = $data['payment_intent'] ?? $data['id'] ?? null;

        // Map Stripe status to our internal status
        $status = $this->mapStripeStatus($eventType, $data);

        // Determine amount based on event type
        $amount = 0;
        if (isset($data['amount_total'])) {
            // Checkout session
            $amount = $this->parseAmount($data['amount_total']);
        } elseif (isset($data['amount'])) {
            // Payment intent or charge
            $amount = $this->parseAmount($data['amount']);
        }

        return [
            'event_type' => $eventType,
            'event_id' => $event['id'] ?? null,
            'transaction_id' => $transactionId,
            'order_id' => $orderId,
            'status' => $status,
            'amount' => $amount,
            'metadata' => $data['metadata'] ?? [],
            'raw_data' => $data,
        ];
    }

    /**
     * Map Stripe event types to internal payment statuses
     *
     * @param string $eventType
     * @param array $data
     * @return string
     */
    protected function mapStripeStatus(string $eventType, array $data): string
    {
        return match ($eventType) {
            'checkout.session.completed' => 'completed',
            'payment_intent.succeeded' => 'completed',
            'payment_intent.payment_failed' => 'failed',
            'charge.refunded' => 'refunded',
            'charge.succeeded' => 'completed',
            'charge.failed' => 'failed',
            default => 'pending',
        };
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'stripe';
    }

    /**
     * {@inheritDoc}
     */
    public function isTestMode(): bool
    {
        return $this->testMode;
    }
}
