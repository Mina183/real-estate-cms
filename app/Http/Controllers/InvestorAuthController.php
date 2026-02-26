<?php

namespace App\Http\Controllers;

use App\Models\InvestorUser;
use App\Models\AuthLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvestorAuthController extends Controller
{
    /**
     * Show investor login form
     */
    public function showLoginForm()
    {
        return view('investor.auth.login');
    }

    /**
     * Handle investor login
     */
public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Rate limiting
    $key = Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    
    if (RateLimiter::tooManyAttempts($key, 5)) {
        $seconds = RateLimiter::availableIn($key);
        throw ValidationException::withMessages([
            'email' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.',
        ]);
    }

    $credentials = $request->only('email', 'password');
    $remember = $request->filled('remember');

    if (Auth::guard('investor')->attempt($credentials, $remember)) {
        RateLimiter::clear($key);
        $request->session()->regenerate();

        $investorUser = Auth::guard('investor')->user();
        $investorUser->updateLastLogin($request->ip());

        AuthLog::create([
            'guard' => 'investor',
            'user_id' => $investorUser->id,
            'email' => $investorUser->email,
            'event' => 'login_success',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->intended(route('investor.dashboard'))
            ->with('success', 'Welcome back, ' . $investorUser->name . '!');
    }

    RateLimiter::hit($key);

        AuthLog::create([
            'guard' => 'investor',
            'user_id' => null,
            'email' => $request->input('email'),
            'event' => 'login_failed',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

    throw ValidationException::withMessages([
        'email' => 'The provided credentials do not match our records.',
    ]);
}

    /**
     * Handle investor logout
     */
    public function logout(Request $request)
    {

    $investorUser = Auth::guard('investor')->user();
    
    if ($investorUser) {
        AuthLog::create([
            'guard' => 'investor',
            'user_id' => $investorUser->id,
            'email' => $investorUser->email,
            'event' => 'logout',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
        Auth::guard('investor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('investor.login')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show investor registration form (invite-only in future)
     */
    public function showRegistrationForm()
    {
        // For now, registration is disabled
        // In Phase 7, this will be invite-only with token verification
        abort(404);
    }

    /**
     * Handle investor registration (invite-only in future)
     */
    public function register(Request $request)
    {
        // For now, registration is disabled
        // In Phase 7, this will be invite-only with token verification
        abort(404);
    }
}
