<?php

namespace App\UseCases\ProxyOrder;

use App\Models\ProxyOrder;
use App\Models\ProxyPlan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    /**
     * Create a new proxy order
     *
     * @param User $user
     * @param ProxyPlan $plan
     * @param array $additionalData
     * @return ProxyOrder
     */
    public function execute(User $user, ProxyPlan $plan, array $additionalData = []): ProxyOrder
    {
        return DB::transaction(function () use ($user, $plan, $additionalData) {
            // Calculate final price with discount
            $finalPrice = $plan->final_price;

            // Generate unique invoice number
            $invoiceNumber = $this->generateInvoiceNumber();

            // Create order
            $order = ProxyOrder::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => $user->id,
                'proxy_plan_id' => $plan->id,
                'proxy_type_id' => $plan->proxy_type_id,
                'bandwidth_gb' => $plan->bandwidth_gb,
                'amount_paid' => $finalPrice,
                'payment_status' => 'pending',
                'payment_method' => $additionalData['payment_method'] ?? null,
                'payment_details' => json_encode([
                    'plan_name' => $plan->name,
                    'base_price' => $plan->base_price,
                    'discount_percentage' => $plan->discount_percentage,
                    'final_price' => $finalPrice,
                    'price_per_gb' => $plan->price_per_gb,
                    'created_at' => now()->toDateTimeString(),
                ]),
            ]);

            return $order;
        });
    }

    /**
     * Generate unique invoice number
     * Format: EUPROXY-YYYY-NNNN
     *
     * @return string
     */
    protected function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $prefix = "EUPROXY-{$year}-";

        // Get the last invoice number for this year
        $lastOrder = ProxyOrder::where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder) {
            // Extract the sequence number and increment
            $lastNumber = (int) substr($lastOrder->invoice_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
