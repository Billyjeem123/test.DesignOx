<?php

namespace App\Http\Middleware;

use App\Helpers\Utility;
use App\Models\Role;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class checkTalentRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            # Find the user details by user token
            $user = Auth::user();

            # Retrieve the 'client' role
            $clientRole = Role::where('role_name', 'talent')->first();

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
