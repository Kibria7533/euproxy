<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProxySubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'proxy_order_id',
        'proxy_type_id',
        'bandwidth_total_gb',
        'bandwidth_remaining_bytes',
        'bandwidth_used_gb',
        'status',
        'auto_renew',
        'suspended_at',
        'suspension_reason',
        'started_at',
        'expires_at',
        'last_used_at',
        'notify_low_bandwidth',
    ];

    protected $casts = [
        'bandwidth_total_gb' => 'decimal:2',
        'bandwidth_remaining_bytes' => 'integer',
        'bandwidth_used_gb' => 'decimal:2',
        'notify_low_bandwidth' => 'boolean',
        'suspended_at' => 'datetime',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user who owns this subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order that created this subscription
     */
    public function order()
    {
        return $this->belongsTo(ProxyOrder::class, 'proxy_order_id');
    }

    /**
     * Get the proxy type for this subscription
     */
    public function proxyType()
    {
        return $this->belongsTo(ProxyType::class);
    }

    /**
     * Get all usage records for this subscription
     */
    public function usageRecords()
    {
        return $this->hasMany(ProxySubscriptionUsage::class);
    }

    /**
     * Get associated squid users
     */
    public function squidUsers()
    {
        return $this->hasMany(SquidUser::class);
    }

    /**
     * Accessor: Get bandwidth remaining in GB
     */
    public function getBandwidthRemainingGbAttribute()
    {
        return round($this->bandwidth_remaining_bytes / 1073741824, 2);
    }

    /**
     * Accessor: Get bandwidth usage percentage
     */
    public function getBandwidthUsagePercentageAttribute()
    {
        if ($this->bandwidth_total_gb <= 0) {
            return 0;
        }
        return round(($this->bandwidth_used_gb / $this->bandwidth_total_gb) * 100, 1);
    }

    /**
     * Check if subscription is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if subscription is depleted (no bandwidth remaining)
     */
    public function isDepleted()
    {
        return $this->status === 'depleted' || $this->bandwidth_remaining_bytes <= 0;
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired()
    {
        return in_array($this->status, ['expired', 'expired_with_balance']);
    }

    /**
     * Check if subscription has low bandwidth (less than 10%)
     */
    public function hasLowBandwidth()
    {
        return $this->bandwidth_usage_percentage >= 90;
    }

    /**
     * Deduct bandwidth from subscription
     */
    public function deductBandwidth($bytes)
    {
        $this->bandwidth_remaining_bytes -= $bytes;
        $this->bandwidth_used_gb += round($bytes / 1073741824, 2);
        $this->last_used_at = now();

        // Auto-suspend if depleted
        if ($this->bandwidth_remaining_bytes <= 0) {
            $this->status = 'depleted';
            $this->suspended_at = now();
            $this->suspension_reason = 'Bandwidth quota exhausted';
        }

        $this->save();
    }

    /**
     * Scope to get active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get depleted subscriptions
     */
    public function scopeDepleted($query)
    {
        return $query->where('status', 'depleted');
    }

    /**
     * Scope to get expiring subscriptions
     */
    public function scopeExpiring($query, $days = 7)
    {
        return $query->where('status', 'active')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }
}
