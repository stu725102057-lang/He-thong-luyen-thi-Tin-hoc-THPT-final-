<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticateWithQueryToken
{
    /**
     * Handle an incoming request.
     * 
     * Accept token from:
     * 1. Authorization header (default)
     * 2. Query parameter ?token=xxx (for file downloads)
     */
    public function handle(Request $request, Closure $next)
    {
        // Nếu có token trong query string
        if ($request->has('token') && !$request->bearerToken()) {
            $token = $request->query('token');
            
            // Validate token với Sanctum
            $accessToken = PersonalAccessToken::findToken($token);
            
            if ($accessToken) {
                // QUAN TRỌNG: Set user vào Auth facade
                Auth::setUser($accessToken->tokenable);
                
                // Cũng set vào request resolver để đảm bảo
                $request->setUserResolver(function () use ($accessToken) {
                    return $accessToken->tokenable;
                });
            }
        }
        
        // Tiếp tục với Sanctum middleware
        return $next($request);
    }
}
