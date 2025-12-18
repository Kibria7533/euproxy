<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProxyPlan;
use App\Models\ProxyType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProxyPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of proxy plans
     */
    public function search(Request $request): View
    {
        $query = ProxyPlan::with('proxyType');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('proxy_type_id')) {
            $query->where('proxy_type_id', $request->proxy_type_id);
        }

        $plans = $query->orderBy('proxy_type_id')->orderBy('sort_order')->paginate(15);
        $proxyTypes = ProxyType::orderBy('sort_order')->get();

        return view('admin.proxy-plans.search', [
            'plans' => $plans,
            'proxyTypes' => $proxyTypes,
            'search' => $request->search,
            'selectedType' => $request->proxy_type_id,
        ]);
    }

    /**
     * Show the form for creating a new proxy plan
     */
    public function creator(): View
    {
        $proxyTypes = ProxyType::active()->get();

        return view('admin.proxy-plans.creator', [
            'proxyTypes' => $proxyTypes,
        ]);
    }

    /**
     * Store a newly created proxy plan
     */
    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'proxy_type_id' => 'required|exists:proxy_types,id',
            'name' => 'required|string|max:100',
            'bandwidth_gb' => 'required|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'is_popular' => 'boolean',
            'is_free_trial' => 'boolean',
            'is_renewable' => 'boolean',
            'validity_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'features' => 'nullable|array',
            'features.*.feature_key' => 'required_with:features|string|max:50',
            'features.*.feature_value' => 'required_with:features|string|max:100',
            'features.*.display_label' => 'required_with:features|string|max:200',
        ]);

        $plan = ProxyPlan::create($validated);

        // Create features if provided
        if ($request->filled('features')) {
            foreach ($request->features as $index => $feature) {
                if (!empty($feature['feature_key'])) {
                    $plan->features()->create([
                        'feature_key' => $feature['feature_key'],
                        'feature_value' => $feature['feature_value'],
                        'display_label' => $feature['display_label'],
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('admin.proxy-plans.search')
            ->with('success', 'Proxy plan created successfully');
    }

    /**
     * Show the form for editing a proxy plan
     */
    public function editor(int $id): View
    {
        $plan = ProxyPlan::with('features', 'proxyType')->findOrFail($id);
        $proxyTypes = ProxyType::active()->get();

        return view('admin.proxy-plans.editor', [
            'plan' => $plan,
            'proxyTypes' => $proxyTypes,
        ]);
    }

    /**
     * Update the specified proxy plan
     */
    public function modify(Request $request, int $id): RedirectResponse
    {
        $plan = ProxyPlan::findOrFail($id);

        $validated = $request->validate([
            'proxy_type_id' => 'required|exists:proxy_types,id',
            'name' => 'required|string|max:100',
            'bandwidth_gb' => 'required|numeric|min:0',
            'base_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'is_popular' => 'boolean',
            'is_free_trial' => 'boolean',
            'is_renewable' => 'boolean',
            'validity_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'features' => 'nullable|array',
            'features.*.feature_key' => 'required_with:features|string|max:50',
            'features.*.feature_value' => 'required_with:features|string|max:100',
            'features.*.display_label' => 'required_with:features|string|max:200',
        ]);

        $plan->update($validated);

        // Update features - delete old and create new
        $plan->features()->delete();
        if ($request->filled('features')) {
            foreach ($request->features as $index => $feature) {
                if (!empty($feature['feature_key'])) {
                    $plan->features()->create([
                        'feature_key' => $feature['feature_key'],
                        'feature_value' => $feature['feature_value'],
                        'display_label' => $feature['display_label'],
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        return redirect()->route('admin.proxy-plans.search')
            ->with('success', 'Proxy plan updated successfully');
    }

    /**
     * Remove the specified proxy plan
     */
    public function destroy(int $id): RedirectResponse
    {
        $plan = ProxyPlan::findOrFail($id);

        // Check if plan has orders
        if ($plan->orders()->count() > 0) {
            return redirect()->route('admin.proxy-plans.search')
                ->with('error', 'Cannot delete plan with existing orders');
        }

        $plan->delete();

        return redirect()->route('admin.proxy-plans.search')
            ->with('success', 'Proxy plan deleted successfully');
    }
}
