<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user is not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }
        
        // If 2FA is not enabled for this user, continue
        if (!$user->is_2fa_enabled) {
            return $next($request);
        }
        
        // If already verified in this session, continue
        if (session('2fa_verified')) {
            return $next($request);
        }
        
        // If trying to access 2FA routes, allow
        if ($request->routeIs('2fa.*')) {
            return $next($request);
        }
        
        // If trying to logout, allow
        if ($request->routeIs('logout')) {
            return $next($request);
        }
        
        // Redirect to 2FA verification
        return redirect()->route('2fa.verify');
    }
}
