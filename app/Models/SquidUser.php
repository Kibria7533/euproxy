<?php

namespace App\Models;

use App\Models\Scopes\SquidUserScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SquidUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'password',
        'enabled',
        'fullname',
        'comment',
        'bandwidth_limit_gb',
    ];

    protected $casts = [
        'bandwidth_limit_gb' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new SquidUserScope());
    }

    public function laravel_user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function proxyRequests()
    {
        return $this->hasMany(ProxyRequest::class, 'username', 'user');
    }

    // Accessor: Returns total bandwidth used in GB
    public function getTotalBandwidthUsedAttribute()
    {
        $bytes = $this->proxyRequests()->sum('bytes');
        return round($bytes / 1073741824, 2);
    }

    // Accessor: Returns usage percentage (0-100)
    public function getBandwidthUsagePercentageAttribute()
    {
        if (is_null($this->bandwidth_limit_gb) || $this->bandwidth_limit_gb == 0) {
            return 0;
        }
        return min(round(($this->total_bandwidth_used / $this->bandwidth_limit_gb) * 100, 1), 100);
    }

    // Check if over bandwidth limit
    public function isOverBandwidthLimit()
    {
        if (is_null($this->bandwidth_limit_gb)) {
            return false;
        }
        return $this->total_bandwidth_used > $this->bandwidth_limit_gb;
    }
}
