<?php

namespace App\Policies;

use App\Models\Reviews;
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


    public function create(): bool
    {
        return true;
    }


    /**
     * Determine whether the user can delete the resource.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user, Reviews $review): bool
    {
        # Check if the user is the owner of the job
        return $user->id == $review->user_id;
    }
}
