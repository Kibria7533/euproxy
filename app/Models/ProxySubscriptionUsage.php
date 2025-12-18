<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProxySubscriptionUsage extends Model
{
    use HasFactory;

    protected $table = 'proxy_subscription_usage';

    protected $fillable = [
        'proxy_subscription_id',
        'proxy_request_id',
        'bytes_consumed',
        'consumed_at',
    ];

    protected $casts = [
        'bytes_consumed' => 'integer',
        'consumed_at' => 'datetime',
    ];

    /**
     * Get the subscription this usage belongs to
     */
    public function subscription()
    {
        return $this->belongsTo(ProxySubscription::class, 'proxy_subscription_id');
    }

    /**
     * Get the proxy request associated with this usage
     */
    public function proxyRequest()
    {
        return $this->belongsTo(ProxyRequest::class, 'proxy_request_id');
    }

    /**
     * Accessor: Get bytes consumed in GB
     */
    public function getGbConsumedAttribute()
    {
        return round($this->bytes_consumed / 1073741824, 2);
    }

    /**
     * Accessor: Get bytes consumed in MB
     */
    public function getMbConsumedAttribute()
    {
        return round($this->bytes_consumed / 1048576, 2);
    }
}
