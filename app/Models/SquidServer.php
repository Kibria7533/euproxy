<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SquidServer extends Model
{
    use HasFactory;

    protected $fillable = [
        'proxy_type_id',
        'hostname',
        'ip',
        'port',
        'location',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'port'      => 'integer',
    ];

    public function proxyType()
    {
        return $this->belongsTo(ProxyType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Returns "ip:port" string (falls back to hostname:port if ip empty)
     */
    public function getAddressAttribute(): string
    {
        return ($this->hostname ?: $this->ip) . ':' . $this->port;
    }
}
