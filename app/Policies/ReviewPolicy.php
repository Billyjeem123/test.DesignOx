<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
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
        if ($user->hasRole('client') || $user->hasRole('talent')) {
            #  If the user has the 'client' or 'talent' role, allow access
            return true;
        }

        # If the user does not have the 'client' or 'talent' role, deny access
        return false;
    }
}
