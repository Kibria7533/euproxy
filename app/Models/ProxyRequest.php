<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
