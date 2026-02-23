<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * If user is already authenticated, redirect them to their dashboard.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Investor guard - redirect to investor dashboard
                if ($guard === 'investor') {
                    return redirect('/investor/dashboard');
                }
                
                // Default web guard - redirect to staff dashboard
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}