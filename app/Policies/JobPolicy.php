<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobPolicy
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
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {

        if (!$user->hasRole('client')) {
            # If the user does not have the 'client' role, deny access
            return false;
        }

        #  If the user has the 'client' role, allow access
        return true;
    }


    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @return bool
     */
    public function view(User $user, Job $job): bool
    {
        # Check if the user is the owner of the job
        return $user->id == $job->client_id;
    }

    /**
     * Determine whether the user can delete the resource.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user, Job $job): bool
    {
        # Check if the user is the owner of the job
        return $user->id == $job->client_id;
    }


}
