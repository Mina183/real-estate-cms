<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class InvestorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if investor is authenticated using 'investor' guard
        if (!Auth::guard('investor')->check()) {
            return redirect()->route('investor.login')
                ->with('error', 'Please log in to access the investor portal.');
        }

        // Check if investor account is active
        $investorUser = Auth::guard('investor')->user();
        
        if (!$investorUser->is_active) {
            Auth::guard('investor')->logout();
            return redirect()->route('investor.login')
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        return $next($request);
    }
}
