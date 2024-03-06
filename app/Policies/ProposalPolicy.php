<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProposalPolicy
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

    /**
     * Determine whether the user can create proposal
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {

        if (!$user->hasRole('talent')) {
            # If the user does not have the 'client' role, deny access
            return false;
        }

        #  If the user has the 'client' role, allow access
        return true;
    }
}
