<?php

namespace App\Http\Controllers\Gui;

use App\Http\Controllers\Controller;
use App\Http\Requests\SquidUser\CreateRequest;
use App\Http\Requests\SquidUser\DestroyRequest;
use App\Http\Requests\SquidUser\ModifyRequest;
use App\Http\Requests\SquidUser\ReadRequest;
use App\Http\Requests\SquidUser\SearchRequest;
use App\Models\ProxySubscription;
use App\Models\ProxyType;
use App\Services\SquidUserService;
use App\UseCases\SquidUser\CreateAction;
use App\UseCases\SquidUser\DestroyAction;
use App\UseCases\SquidUser\ModifyAction;
use App\UseCases\SquidUser\SearchAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SquidUserController extends Controller
{
    private $squidUserService;

    public function __construct(SquidUserService $squidUserService)
    {
        $this->squidUserService = $squidUserService;
    }

    public function search(SearchRequest $request, SearchAction $action): View
    {
        return view('squidusers.search', [
            'users'=>$action($request->searchSquidUser()),
        ]);
    }

    public function creator(Request $request): View
    {
        $user = Auth::user();

        if ($user->is_administrator) {
            // Admin can assign any proxy type
            $availableTypes = ProxyType::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'available_gb' => null]);
        } else {
            // Regular user: only types they have active subscriptions for
            $availableTypes = ProxySubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->with('proxyType')
                ->get()
                ->groupBy('proxy_type_id')
                ->map(function ($subs) {
                    $type = $subs->first()->proxyType;
                    $purchasedGb  = $subs->sum('bandwidth_total_gb');
                    $assignedGb   = \App\Models\SquidUser::where('user_id', $subs->first()->user_id)
                        ->where('proxy_type_id', $type->id)
                        ->where('enabled', 1)
                        ->sum('bandwidth_limit_gb');
                    return [
                        'id'           => $type->id,
                        'name'         => $type->name,
                        'available_gb' => max(0, round($purchasedGb - $assignedGb, 3)),
                    ];
                })
                ->values();
        }

        return view('squidusers.creator', compact('availableTypes'));
    }

    public function editor(ReadRequest $request): View
    {
        $squidUser = $this->squidUserService->getById($request->route()->parameter('id'));

        return view('squidusers.editor', [
            'id'=>$squidUser->id,
            'user'=>$squidUser->user,
            'password'=>$squidUser->decrypted_password,
            'fullname'=>$squidUser->fullname,
            'comment'=>$squidUser->comment,
            'enabled'=>$squidUser->enabled,
            'bandwidth_limit_gb'=>$squidUser->bandwidth_limit_gb,
        ]);
    }

    public function modify(ModifyRequest $request, ModifyAction $action): RedirectResponse
    {
        $action($request->modifySquidUser());

        if ($request->user()->is_administrator) {
            return redirect()->route('squiduser.search', $request->user()->id);
        }
        return redirect()->route('user.squiduser.search');
    }

    public function create(CreateRequest $request, CreateAction $action): RedirectResponse
    {
        $action($request->createSquidUser());

        if ($request->user()->is_administrator) {
            return redirect()->route('squiduser.search', $request->user()->id);
        }
        return redirect()->route('user.squiduser.search');
    }

    public function destroy(DestroyRequest $request, DestroyAction $action): RedirectResponse
    {
        $action($request->destroySquidUser());

        if ($request->user()->is_administrator) {
            return redirect()->route('squiduser.search', $request->user()->id);
        }
        return redirect()->route('user.squiduser.search');
    }
}
