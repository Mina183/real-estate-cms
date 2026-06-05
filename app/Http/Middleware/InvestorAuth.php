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
        if (! Auth::guard('investor')->check()) {
            return redirect()->route('investor.login')
                ->with('error', 'Please log in to access the investor portal.');
        }

        $investorUser = Auth::guard('investor')->user();

        if (! $investorUser->is_active) {
            Auth::guard('investor')->logout();
            return redirect()->route('investor.login')
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        // Viewer accounts bypass PIN entirely
        if ($investorUser->investor?->data_room_access_level === 'viewer') {
            return $next($request);
        }

        // PIN not set yet — redirect to setup
        if (! $investorUser->portal_pin) {
            if (! $request->routeIs('investor.pin.setup') && ! $request->routeIs('investor.pin.store')) {
                return redirect()->route('investor.pin.setup');
            }
            return $next($request);
        }

        // PIN set but not yet verified this session — redirect to verify
        if (! $request->session()->get('investor_pin_verified')) {
            if (! $request->routeIs('investor.pin.verify') && ! $request->routeIs('investor.pin.check')) {
                return redirect()->route('investor.pin.verify');
            }
            return $next($request);
        }

        return $next($request);
    }
}
