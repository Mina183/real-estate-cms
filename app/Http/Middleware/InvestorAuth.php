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
        \Log::info('InvestorAuth middleware triggered', [
            'authenticated' => Auth::guard('investor')->check(),
            'path' => $request->path()
        ]);

        if (!Auth::guard('investor')->check()) {
            \Log::error('InvestorAuth: Not authenticated, redirecting to login');
            return redirect()->route('investor.login')
                ->with('error', 'Please log in to access the investor portal.');
        }

        $investorUser = Auth::guard('investor')->user();
        
        \Log::info('InvestorAuth: User found', [
            'user_id' => $investorUser->id,
            'is_active' => $investorUser->is_active
        ]);
        
        if (!$investorUser->is_active) {
            Auth::guard('investor')->logout();
            \Log::error('InvestorAuth: Account inactive, logged out');
            return redirect()->route('investor.login')
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        \Log::info('InvestorAuth: Passed, continuing to controller');
        return $next($request);
    }
}
