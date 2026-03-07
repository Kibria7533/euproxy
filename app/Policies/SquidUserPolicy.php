<?php

namespace App\Policies;

use App\Models\ProxySubscription;
use App\Models\SquidUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class SquidUserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function search(User $user, string $toSpecifiedUserId)
    {
        if ((bool) $user->is_administrator) {
            return true;
        }

        if (strcmp((string) $user->id, $toSpecifiedUserId) === 0) {
            return true;
        }

        return false;
    }

    public function create(User $user, string $toSpecifiedUserId): Response|bool
    {
        if ((bool) $user->is_administrator) {
            return true;
        }

        if (strcmp((string) $user->id, $toSpecifiedUserId) !== 0) {
            return false;
        }

        // Sum max_sub_accounts across all active subscriptions
        $maxAllowed = ProxySubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['order.proxyPlan.features'])
            ->get()
            ->sum(function ($sub) {
                $feature = optional($sub->order?->proxyPlan)->features
                    ?->firstWhere('feature_key', 'max_sub_accounts');
                return $feature ? (int) $feature->feature_value : 0;
            });

        if ($maxAllowed === 0) {
            return Response::deny('You need an active subscription to create proxy users.');
        }

        $activeCount = SquidUser::where('user_id', $user->id)
            ->where('enabled', 1)
            ->count();

        if ($activeCount >= $maxAllowed) {
            return Response::deny("You have reached your limit of {$maxAllowed} active proxy user(s). Upgrade your plan to add more.");
        }

        return true;
    }

    public function modify(User $user, SquidUser $squidUser)
    {
        if ((bool) $user->is_administrator) {
            return true;
        }

        if (strcmp((string) $user->id, (string) $squidUser->user_id) === 0) {
            return true;
        }

        return false;
    }

    public function destroy(User $user, SquidUser $squidUser)
    {
        if ((bool) $user->is_administrator) {
            return true;
        }

        if (strcmp((string) $user->id, (string) $squidUser->user_id) === 0) {
            return true;
        }

        return false;
    }
}
