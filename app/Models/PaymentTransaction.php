<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'proxy_order_id',
        'amount',
        'currency',
        'payment_gateway',
        'transaction_id',
        'webhook_id',
        'webhook_payload',
        'type',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user associated with this transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with this transaction
     */
    public function order()
    {
        return $this->belongsTo(ProxyOrder::class, 'proxy_order_id');
    }

    /**
     * Check if transaction is successful
     */
    public function isSuccess()
    {
        return $this->status === 'success';
    }

    /**
     * Check if transaction is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is failed
     */
    public function isFailed()
    {
        return $this->status === 'failed';
    }

    /**
     * Check if transaction is refunded
     */
    public function isRefunded()
    {
        return $this->status === 'refunded';
    }

    /**
     * Scope to get successful transactions
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope to get transactions by gateway
     */
    public function scopeByGateway($query, $gateway)
    {
        return $query->where('payment_gateway', $gateway);
    }
}
