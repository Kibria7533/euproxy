<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProxyType;
use App\Models\SquidServer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SquidServerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function search(Request $request): View
    {
        $query = SquidServer::with('proxyType');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('ip', 'like', "%{$term}%")
                  ->orWhere('hostname', 'like', "%{$term}%")
                  ->orWhere('location', 'like', "%{$term}%");
            });
        }

        if ($request->filled('proxy_type_id')) {
            $query->where('proxy_type_id', $request->proxy_type_id);
        }

        $servers    = $query->orderBy('proxy_type_id')->orderBy('location')->paginate(20);
        $proxyTypes = ProxyType::orderBy('sort_order')->get();

        return view('admin.squid-servers.search', [
            'servers'          => $servers,
            'proxyTypes'       => $proxyTypes,
            'search'           => $request->search,
            'filterTypeId'     => $request->proxy_type_id,
        ]);
    }

    public function creator(): View
    {
        $proxyTypes = ProxyType::orderBy('sort_order')->get();

        return view('admin.squid-servers.creator', compact('proxyTypes'));
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'proxy_type_id' => 'required|exists:proxy_types,id',
            'ip'            => 'required|string|max:45',
            'hostname'      => 'nullable|string|max:255',
            'port'          => 'required|integer|min:1|max:65535',
            'location'      => 'nullable|string|max:100',
            'is_active'     => 'boolean',
            'notes'         => 'nullable|string',
        ]);

        SquidServer::create($validated);

        return redirect()->route('admin.squid-servers.search')
            ->with('success', 'Proxy server added successfully.');
    }

    public function editor(int $id): View
    {
        $server     = SquidServer::findOrFail($id);
        $proxyTypes = ProxyType::orderBy('sort_order')->get();

        return view('admin.squid-servers.editor', compact('server', 'proxyTypes'));
    }

    public function modify(Request $request, int $id): RedirectResponse
    {
        $server = SquidServer::findOrFail($id);

        $validated = $request->validate([
            'proxy_type_id' => 'required|exists:proxy_types,id',
            'ip'            => 'required|string|max:45',
            'hostname'      => 'nullable|string|max:255',
            'port'          => 'required|integer|min:1|max:65535',
            'location'      => 'nullable|string|max:100',
            'is_active'     => 'boolean',
            'notes'         => 'nullable|string',
        ]);

        $server->update($validated);

        return redirect()->route('admin.squid-servers.search')
            ->with('success', 'Proxy server updated successfully.');
    }

    public function destroy(int $id): RedirectResponse
    {
        SquidServer::findOrFail($id)->delete();

        return redirect()->route('admin.squid-servers.search')
            ->with('success', 'Proxy server deleted.');
    }
}
