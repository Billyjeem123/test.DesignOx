<?php

namespace App\Http\Middleware;

use App\Helpers\Utility;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckClientRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle($request, Closure $next)
    {
        #   Check if the authenticated user exists and has the 'client' role
        $user = Auth::user();

        #   Retrieve the 'client' role
        $clientRole = Role::where('role_name', 'client')->first();

        #   Check if the user has the 'client' role
        if ($user->roles->contains($clientRole)) {
            return $next($request);
        }

        #   If not, return a 403 Forbidden response
        return Utility::outputData(false, "Unauthorized Access", [], 403);
    }
}
