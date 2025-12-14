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
        'quota_bytes',
        'used_bytes',
    ];

    protected $casts = [
        'bandwidth_limit_gb' => 'decimal:2',
        'quota_bytes' => 'integer',
        'used_bytes' => 'integer',
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

    // Mutator: Automatically sync bandwidth_limit_gb to quota_bytes
    public function setBandwidthLimitGbAttribute($value)
    {
        $this->attributes['bandwidth_limit_gb'] = $value;

        // Automatically set quota_bytes when bandwidth_limit_gb is set
        // 1 GB = 1,073,741,824 bytes
        if (!is_null($value) && $value > 0) {
            $this->attributes['quota_bytes'] = (int) ($value * 1073741824);
        } else {
            $this->attributes['quota_bytes'] = 0;
        }
    }

    // Mutator: Automatically sync quota_bytes to bandwidth_limit_gb
    public function setQuotaBytesAttribute($value)
    {
        $this->attributes['quota_bytes'] = $value;

        // Automatically update bandwidth_limit_gb when quota_bytes is set
        // Only if bandwidth_limit_gb is not already being set
        if (!is_null($value) && $value > 0 && !isset($this->attributes['bandwidth_limit_gb'])) {
            $this->attributes['bandwidth_limit_gb'] = round($value / 1073741824, 2);
        }
    }
}
