<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckChangePasswordMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Redirect to the login page if not authenticated
            return redirect('/login');
        }
    
        // Allow access to the change password page and the update password route
        if ($request->is('change-password') || $request->is('update-password')) {
            return $next($request);
        }
    
        // Redirect if 'changepassword_at' is null
        if (Auth::user()->change_password_at === null) {
            return redirect('/change-password');
        }
    
        // If all checks pass, proceed with the request
        return $next($request);
    }
}
