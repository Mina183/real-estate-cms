<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class InvestorAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('investor')->check()) {
            return redirect()->route('investor.login')
                ->with('error', 'Please log in to access the investor portal.');
        }

        $investorUser = Auth::guard('investor')->user();
        
        if (!$investorUser->is_active) {
            Auth::guard('investor')->logout();
            return redirect()->route('investor.login')
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        // 2FA provjera
        if (!$investorUser->two_factor_enabled) {
            if (!$request->routeIs('investor.2fa.setup') && !$request->routeIs('investor.2fa.enable')) {
                return redirect()->route('investor.2fa.setup');
            }
            return $next($request);
        }

        if (!$request->session()->get('investor_2fa_verified')) {
            if (!$request->routeIs('investor.2fa.verify') && !$request->routeIs('investor.2fa.check')) {
                return redirect()->route('investor.2fa.verify');
            }
            return $next($request);
        }

        return $next($request);
    }
}