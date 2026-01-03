<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProxyPlan;
use App\Models\ProxyOrder;
use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\StripePaymentGateway;
use App\UseCases\ProxyOrder\CreateOrderAction;
use App\UseCases\ProxyOrder\ProcessPaymentAction;
use App\UseCases\ProxyOrder\FulfillOrderAction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserCheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('user');
    }

    /**
     * Show order review page
     *
     * @param int $planId
     * @return View
     */
    public function review(int $planId): View
    {
        $plan = ProxyPlan::with(['proxyType', 'features'])
            ->active()
            ->findOrFail($planId);

        return view('user.checkout.review', [
            'plan' => $plan,
        ]);
    }

    /**
     * Process checkout and redirect to payment gateway
     *
     * @param Request $request
     * @param CreateOrderAction $createOrder
     * @return RedirectResponse
     */
    public function process(Request $request, CreateOrderAction $createOrder): RedirectResponse
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:proxy_plans,id',
            'payment_method' => 'required|in:stripe',
            'terms_accepted' => 'accepted',
        ]);

        $plan = ProxyPlan::with('proxyType')->findOrFail($validated['plan_id']);

        // Create pending order
        $order = $createOrder->execute(
            auth()->user(),
            $plan,
            ['payment_method' => $validated['payment_method']]
        );

        // Load relationships needed for payment gateway
        $order->load(['plan', 'proxyType']);

        // Check if payment bypass is enabled (for development)
        if (config('payment.bypass_enabled', false)) {
            return $this->processBypassPayment($order);
        }

        // Get appropriate payment gateway
        $gateway = $this->getPaymentGateway($validated['payment_method']);

        try {
            // Create payment intent/session
            $paymentData = $gateway->createPaymentIntent($order, [
                'success_url' => route('user.checkout.success'),
                'cancel_url' => route('user.checkout.cancel', ['order' => $order->id]),
            ]);

            // Update order with transaction details
            $order->update([
                'payment_transaction_id' => $paymentData['session_id'],
            ]);

            // Redirect to payment gateway
            return redirect($paymentData['redirect_url']);
        } catch (\Exception $e) {
            // Log error
            \Log::error('Checkout failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('user.checkout.review', $plan->id)
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Payment success page
     *
     * @param Request $request
     * @return View
     */
    public function success(Request $request): View
    {
        $sessionId = $request->query('session_id');
        $orderId = $request->query('order');

        // Find order by session_id or order_id
        $order = ProxyOrder::query()
            ->when($sessionId, function ($query) use ($sessionId) {
                $query->where('payment_transaction_id', $sessionId);
            })
            ->when($orderId && !$sessionId, function ($query) use ($orderId) {
                $query->where('id', $orderId);
            })
            ->with(['plan', 'proxyType', 'subscription'])
            ->first();

        if (!$order || $order->user_id !== auth()->id()) {
            return view('user.checkout.success', [
                'order' => null,
                'message' => 'Processing your payment...',
            ]);
        }

        return view('user.checkout.success', [
            'order' => $order,
            'message' => null,
        ]);
    }

    /**
     * Payment cancelled page
     *
     * @param int|null $orderId
     * @return View
     */
    public function cancel(?int $orderId = null): View
    {
        $order = null;

        if ($orderId) {
            $order = ProxyOrder::where('id', $orderId)
                ->where('user_id', auth()->id())
                ->with(['plan', 'proxyType'])
                ->first();
        }

        return view('user.checkout.cancel', [
            'order' => $order,
        ]);
    }

    /**
     * Process bypass payment for development (auto-success)
     *
     * @param ProxyOrder $order
     * @return RedirectResponse
     */
    protected function processBypassPayment(ProxyOrder $order): RedirectResponse
    {
        try {
            // Generate a fake transaction ID
            $transactionId = 'bypass_' . uniqid() . '_' . $order->id;

            // Update order with fake transaction ID
            $order->update([
                'payment_transaction_id' => $transactionId,
            ]);

            // Process payment as completed
            $processPayment = app(ProcessPaymentAction::class);
            $processPayment->execute($order, [
                'status' => 'completed',
                'transaction_id' => $transactionId,
                'amount' => $order->amount_paid,
                'gateway' => 'bypass',
                'event_id' => 'bypass_' . uniqid(),
                'raw_data' => ['bypass' => true, 'note' => 'Development bypass payment'],
            ]);

            // Fulfill order (create subscription)
            $fulfillOrder = app(FulfillOrderAction::class);
            $subscription = $fulfillOrder->execute($order);

            \Log::info('Bypass payment processed', [
                'order_id' => $order->id,
                'subscription_id' => $subscription->id,
                'user_id' => $order->user_id,
            ]);

            // Redirect to success page with order info
            return redirect()
                ->route('user.checkout.success', ['order' => $order->id])
                ->with('success', 'Payment processed successfully!');
        } catch (\Exception $e) {
            \Log::error('Bypass payment failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('user.checkout.cancel', ['order' => $order->id])
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Get payment gateway instance
     *
     * @param string $method
     * @return PaymentGatewayInterface
     */
    protected function getPaymentGateway(string $method): PaymentGatewayInterface
    {
        return app(StripePaymentGateway::class);
    }
}
