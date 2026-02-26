<?php

namespace App\Http\Controllers\Auth;

use App\Models\AuthLog;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    $request->session()->regenerate();

    $user = $request->user();

    AuthLog::create([
        'guard' => 'web',
        'user_id' => $user->id,
        'email' => $user->email,
        'event' => 'login_success',
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ]);

    if (! $user->is_approved) {
        Auth::logout();

        return redirect()
            ->route('login')
            ->withErrors(['email' => 'Your account is pending approval.']);
    }

    // ✅ Redirect to the original intended route or fallback
    return redirect()->intended(route('dashboard'));
}

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {

        $user = Auth::guard('web')->user();
        
        if ($user) {
            AuthLog::create([
                'guard' => 'web',
                'user_id' => $user->id,
                'email' => $user->email,
                'event' => 'logout',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
