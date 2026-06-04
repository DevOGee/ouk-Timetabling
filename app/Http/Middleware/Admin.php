<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if the user has the admin role
        if (!$request->user()->hasRole('admin')) {
            // Check if the admin role exists in the database
            $adminRoleExists = Role::where('name', 'admin')->exists();
            
            if (!$adminRoleExists) {
                // Log the error for debugging
                Log::error("Admin role does not exist in the database");
                abort(500, 'The admin role is not properly configured in the system.');
            }
            
            abort(403, 'You do not have administrator privileges to access this section.');
        }

        return $next($request);
    }
}
