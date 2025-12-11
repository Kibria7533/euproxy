<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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

    public function create(User $user): bool
    {
        return (bool) $user->is_administrator;
    }

    public function modify(User $user, string $updateUserId)
    {
        if ((bool) $user->is_administrator) {
            return true;
        }

        if (strcmp((string) $user->id, $updateUserId) === 0) {
            return true;
        }

        return false;
    }

    public function destroy(User $user)
    {
        return (bool) $user->is_administrator;
    }

    public function search(User $user)
    {
        return (bool) $user->is_administrator;
    }
}
