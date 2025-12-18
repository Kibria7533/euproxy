<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ProxyType;
use App\Models\ProxySubscription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserProxyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('user');
    }

    /**
     * Show proxy type page with tabs
     *
     * @param string $slug
     * @param string $tab
     * @return View
     */
    public function show(string $slug, string $tab = 'buy'): View
    {
        $proxyType = ProxyType::where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Load active plans for this proxy type
        $plans = $proxyType->activePlans()
            ->with('features')
            ->orderBy('sort_order')
            ->orderBy('bandwidth_gb')
            ->get();

        // Load user's subscriptions for this proxy type
        $subscriptions = ProxySubscription::where('user_id', auth()->id())
            ->where('proxy_type_id', $proxyType->id)
            ->with(['order', 'usageRecords'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.proxies.show', [
            'proxyType' => $proxyType,
            'plans' => $plans,
            'subscriptions' => $subscriptions,
            'activeTab' => $tab,
        ]);
    }

    /**
     * Buy plans tab
     */
    public function buyPlans(string $slug): View
    {
        return $this->show($slug, 'buy');
    }

    /**
     * Configuration tab
     */
    public function configuration(string $slug): View
    {
        return $this->show($slug, 'configuration');
    }

    /**
     * My subscriptions tab
     */
    public function subscriptions(string $slug): View
    {
        return $this->show($slug, 'subscriptions');
    }

    /**
     * Documentation tab
     */
    public function documentation(string $slug): View
    {
        return $this->show($slug, 'documentation');
    }
}
