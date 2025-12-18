<?php

namespace App\Models;

use App\Observers\ProxyRequestObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ProxyRequestObserver::class])]
class ProxyRequest extends Model
{
    use HasFactory;

    protected $table = 'proxy_requests';

    // The proxy_requests table only has created_at, no updated_at
    const UPDATED_AT = null;

    protected $casts = [
        'bytes' => 'integer',
        'ts' => 'double',
        'status' => 'integer',
    ];

    // Relationship to SquidUser via username field
    public function squidUser()
    {
        return $this->belongsTo(SquidUser::class, 'username', 'user');
    }

    // Relationship to ProxySubscriptionUsage
    public function subscriptionUsage()
    {
        return $this->hasOne(ProxySubscriptionUsage::class, 'proxy_request_id');
    }
}
