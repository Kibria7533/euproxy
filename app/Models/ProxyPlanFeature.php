<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProxyPlanFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'proxy_plan_id',
        'feature_key',
        'feature_value',
        'display_label',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the plan this feature belongs to
     */
    public function plan()
    {
        return $this->belongsTo(ProxyPlan::class, 'proxy_plan_id');
    }
}
