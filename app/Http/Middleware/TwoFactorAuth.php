<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Roleovi koji zahtevaju MFA
        $mfaRoles = [
            'superadmin',
            'admin',
            'operations',
            'compliance_officer',
            'data_room_administrator',
            'auditor',
        ];

        if (!in_array($user->role, $mfaRoles)) {
            return $next($request);
        }

        // Ako MFA nije postavljen, preusmeri na setup
        if (!$user->two_factor_enabled) {
            if (!$request->routeIs('2fa.setup') && !$request->routeIs('2fa.enable')) {
                return redirect()->route('2fa.setup');
            }
            return $next($request);
        }

        // Ako MFA je postavljen ali nije verifikovan u ovoj sesiji
        if (!$request->session()->get('2fa_verified')) {
            if (!$request->routeIs('2fa.verify') && !$request->routeIs('2fa.check')) {
                return redirect()->route('2fa.verify');
            }
            return $next($request);
        }

        return $next($request);
    }
}