<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\ProxyOrder;
use App\Services\Payment\PayPalPaymentGateway;
use App\UseCases\ProxyOrder\ProcessPaymentAction;
use App\UseCases\ProxyOrder\FulfillOrderAction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PayPalWebhookController extends Controller
{
    protected PayPalPaymentGateway $gateway;
    protected ProcessPaymentAction $processPayment;
    protected FulfillOrderAction $fulfillOrder;

    public function __construct(
        PayPalPaymentGateway $gateway,
        ProcessPaymentAction $processPayment,
        FulfillOrderAction $fulfillOrder
    ) {
        $this->gateway = $gateway;
        $this->processPayment = $processPayment;
        $this->fulfillOrder = $fulfillOrder;
    }

    /**
     * Handle incoming PayPal webhook
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('PAYPAL-TRANSMISSION-SIG');

        // Verify webhook signature (basic verification)
        if (!$this->gateway->verifyWebhookSignature($payload, $signature)) {
            Log::warning('PayPal webhook verification failed', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Parse webhook payload
        $webhookData = $this->gateway->parseWebhookPayload($payload);

        Log::info('PayPal webhook received', [
            'event_type' => $webhookData['event_type'],
            'event_id' => $webhookData['event_id'],
            'order_id' => $webhookData['order_id'],
        ]);

        try {
            // Find the order
            $orderId = $webhookData['order_id'];
            if (!$orderId) {
                Log::warning('PayPal webhook missing order_id', [
                    'event_id' => $webhookData['event_id'],
                ]);
                return response()->json(['error' => 'Missing order_id'], 400);
            }

            $order = ProxyOrder::find($orderId);
            if (!$order) {
                Log::error('Order not found for PayPal webhook', [
                    'order_id' => $orderId,
                    'event_id' => $webhookData['event_id'],
                ]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Add gateway info to webhook data
            $webhookData['gateway'] = 'paypal';

            // Process payment based on event type
            $eventType = $webhookData['event_type'];

            switch ($eventType) {
                case 'CHECKOUT.ORDER.APPROVED':
                    // Order approved, but not yet captured
                    // You may want to capture payment here or wait for PAYMENT.CAPTURE.COMPLETED
                    Log::info('PayPal order approved (pending capture)', [
                        'order_id' => $order->id,
                    ]);
                    break;

                case 'PAYMENT.CAPTURE.COMPLETED':
                    // Process successful payment
                    $processed = $this->processPayment->execute($order, $webhookData);

                    if ($processed && $order->payment_status === 'completed') {
                        // Fulfill the order (create subscription)
                        try {
                            $subscription = $this->fulfillOrder->execute($order);

                            Log::info('Order fulfilled from PayPal webhook', [
                                'order_id' => $order->id,
                                'subscription_id' => $subscription->id,
                            ]);

                            // TODO: Send confirmation email to user
                        } catch (\Exception $e) {
                            Log::error('Failed to fulfill order from PayPal webhook', [
                                'order_id' => $order->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                    break;

                case 'PAYMENT.CAPTURE.DENIED':
                    // Process failed payment
                    $this->processPayment->execute($order, $webhookData);
                    break;

                case 'PAYMENT.CAPTURE.REFUNDED':
                    // Handle refund (initiated from PayPal dashboard)
                    $this->processPayment->execute($order, $webhookData);
                    break;

                default:
                    Log::info('Unhandled PayPal webhook event type', [
                        'event_type' => $eventType,
                    ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error processing PayPal webhook', [
                'event_id' => $webhookData['event_id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }
}
