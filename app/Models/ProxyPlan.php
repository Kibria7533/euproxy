<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProxyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'proxy_type_id',
        'name',
        'bandwidth_gb',
        'base_price',
        'discount_percentage',
        'is_popular',
        'is_free_trial',
        'is_renewable',
        'validity_days',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'bandwidth_gb' => 'decimal:2',
        'base_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_free_trial' => 'boolean',
        'is_renewable' => 'boolean',
        'validity_days' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the proxy type this plan belongs to
     */
    public function proxyType()
    {
        return $this->belongsTo(ProxyType::class);
    }

    /**
     * Get all features for this plan
     */
    public function features()
    {
        return $this->hasMany(ProxyPlanFeature::class)->orderBy('sort_order');
    }

    /**
     * Get all orders for this plan
     */
    public function orders()
    {
        return $this->hasMany(ProxyOrder::class);
    }

    /**
     * Accessor: Calculate final price after discount
     */
    public function getFinalPriceAttribute()
    {
        if ($this->discount_percentage > 0) {
            return round($this->base_price * (1 - ($this->discount_percentage / 100)), 2);
        }
        return $this->base_price;
    }

    /**
     * Accessor: Calculate price per GB
     */
    public function getPricePerGbAttribute()
    {
        if ($this->bandwidth_gb > 0) {
            return round($this->final_price / $this->bandwidth_gb, 2);
        }
        return 0;
    }

    /**
     * Accessor: Get discount amount
     */
    public function getDiscountAmountAttribute()
    {
        if ($this->discount_percentage > 0) {
            return round($this->base_price - $this->final_price, 2);
        }
        return 0;
    }

    /**
     * Scope to get only active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Scope to get only popular plans
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope to get free trial plans
     */
    public function scopeFreeTrial($query)
    {
        return $query->where('is_free_trial', true);
    }
}
