<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProxyType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProxyTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of proxy types
     */
    public function search(Request $request): View
    {
        $query = ProxyType::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
        }

        $proxyTypes = $query->orderBy('sort_order')->paginate(15);

        return view('admin.proxy-types.search', [
            'proxyTypes' => $proxyTypes,
            'search' => $request->search,
        ]);
    }

    /**
     * Show the form for creating a new proxy type
     */
    public function creator(): View
    {
        return view('admin.proxy-types.creator');
    }

    /**
     * Store a newly created proxy type
     */
    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:proxy_types,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        ProxyType::create($validated);

        return redirect()->route('admin.proxy-types.search')
            ->with('success', 'Proxy type created successfully');
    }

    /**
     * Show the form for editing a proxy type
     */
    public function editor(int $id): View
    {
        $proxyType = ProxyType::findOrFail($id);

        return view('admin.proxy-types.editor', [
            'proxyType' => $proxyType,
        ]);
    }

    /**
     * Update the specified proxy type
     */
    public function modify(Request $request, int $id): RedirectResponse
    {
        $proxyType = ProxyType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:proxy_types,slug,' . $id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $proxyType->update($validated);

        return redirect()->route('admin.proxy-types.search')
            ->with('success', 'Proxy type updated successfully');
    }

    /**
     * Remove the specified proxy type
     */
    public function destroy(int $id): RedirectResponse
    {
        $proxyType = ProxyType::findOrFail($id);

        // Check if type has plans
        if ($proxyType->plans()->count() > 0) {
            return redirect()->route('admin.proxy-types.search')
                ->with('error', 'Cannot delete proxy type with existing plans');
        }

        $proxyType->delete();

        return redirect()->route('admin.proxy-types.search')
            ->with('success', 'Proxy type deleted successfully');
    }
}
