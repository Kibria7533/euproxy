<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProxyOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'proxy_plan_id',
        'proxy_type_id',
        'bandwidth_gb',
        'amount_paid',
        'payment_status',
        'payment_method',
        'payment_transaction_id',
        'payment_details',
        'refunded_by',
        'refunded_at',
        'refund_amount',
        'refund_reason',
    ];

    protected $casts = [
        'bandwidth_gb' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the user who placed this order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan associated with this order
     */
    public function plan()
    {
        return $this->belongsTo(ProxyPlan::class, 'proxy_plan_id');
    }

    /**
     * Get the proxy type for this order
     */
    public function proxyType()
    {
        return $this->belongsTo(ProxyType::class);
    }

    /**
     * Get the subscription created from this order
     */
    public function subscription()
    {
        return $this->hasOne(ProxySubscription::class);
    }

    /**
     * Get payment transactions for this order
     */
    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Get the user who refunded this order
     */
    public function refundedBy()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    /**
     * Check if order is pending
     */
    public function isPending()
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if order is completed
     */
    public function isCompleted()
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if order is refunded
     */
    public function isRefunded()
    {
        return $this->payment_status === 'refunded';
    }

    /**
     * Scope to get completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope to get pending orders
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }
}
