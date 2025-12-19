<?php

namespace App\Http\Controllers;

use App\Models\ProxyType;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Show the landing page
     */
    public function index()
    {
        // Get active proxy types with their plans
        $proxyTypes = ProxyType::active()
            ->with(['plans' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        return view('landing', [
            'proxyTypes' => $proxyTypes,
        ]);
    }
}
