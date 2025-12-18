<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProxyType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all plans for this proxy type
     */
    public function plans()
    {
        return $this->hasMany(ProxyPlan::class);
    }

    /**
     * Get active plans for this proxy type
     */
    public function activePlans()
    {
        return $this->hasMany(ProxyPlan::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get all orders for this proxy type
     */
    public function orders()
    {
        return $this->hasMany(ProxyOrder::class);
    }

    /**
     * Get all subscriptions for this proxy type
     */
    public function subscriptions()
    {
        return $this->hasMany(ProxySubscription::class);
    }

    /**
     * Scope to get only active proxy types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
