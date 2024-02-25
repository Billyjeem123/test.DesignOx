<?php

namespace App\Http\Middleware;

use App\Helpers\Utility;
use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckClientRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle($request, Closure $next)
    {
        try {
            # Find the user details by user token
            $user = User::findOrFail($request->usertoken);

            # Retrieve the 'client' role
            $clientRole = Role::where('role_name', 'client')->first();

            # Check if the user has the 'client' role
            if ($user->roles->contains($clientRole)) {
                return $next($request);
            }

            # If not, return a 403 Forbidden response
            return Utility::outputData(false, "Unauthorized Access", [], 403);
        } catch (ModelNotFoundException $exception) {
            # If user is not found, return a 404 Not Found response
            return Utility::outputData(false, "User not found", [], 404);
        }
    }

}
