<?php

namespace App\Http\Controllers;

use App\Models\InvestorUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    $credentials = $request->only('email', 'password');
    $remember = $request->filled('remember');

    // DEBUG
    \Log::info('Investor login attempt', ['email' => $credentials['email']]);

    if (Auth::guard('investor')->attempt($credentials, $remember)) {
        $request->session()->regenerate();

        $investorUser = Auth::guard('investor')->user();
        
        // DEBUG
        \Log::info('Investor login SUCCESS', [
            'user_id' => $investorUser->id,
            'investor_id' => $investorUser->investor_id
        ]);

        $investorUser->updateLastLogin($request->ip());

        return redirect()->intended(route('investor.dashboard'))
            ->with('success', 'Welcome back, ' . $investorUser->name . '!');
    }

    // DEBUG
    \Log::error('Investor login FAILED', ['email' => $credentials['email']]);

    throw ValidationException::withMessages([
        'email' => 'The provided credentials do not match our records.',
    ]);
}

    /**
     * Handle investor logout
     */
    public function logout(Request $request)
    {
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
