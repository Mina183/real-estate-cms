<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorController extends Controller
{
    /**
     * Show 2FA setup page
     */
    public function setup()
    {
        $user = Auth::user();
        
        $google2fa = app('pragmarx.google2fa');
        
        // Generate secret if not exists
        if (!$user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => encrypt($secret)]);
        } else {
            $secret = decrypt($user->two_factor_secret);
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        return view('auth.2fa.setup', compact('secret', 'qrCodeUrl'));
    }

    /**
     * Enable 2FA after scanning QR code
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $secret = decrypt($user->two_factor_secret);
        $google2fa = app('pragmarx.google2fa');

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_enabled_at' => now(),
        ]);

        $request->session()->put('2fa_verified', true);

        return redirect()->route('dashboard')
            ->with('success', 'Two-factor authentication enabled successfully!');
    }

    /**
     * Show 2FA verify page
     */
    public function verify()
    {
        return view('auth.2fa.verify');
    }

    /**
     * Check 2FA code
     */
    public function check(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $secret = decrypt($user->two_factor_secret);
        $google2fa = app('pragmarx.google2fa');

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $request->session()->put('2fa_verified', true);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $secret = decrypt($user->two_factor_secret);
        $google2fa = app('pragmarx.google2fa');

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_enabled' => false,
            'two_factor_enabled_at' => null,
        ]);

        $request->session()->forget('2fa_verified');

        return redirect()->route('profile.edit')
            ->with('success', 'Two-factor authentication disabled.');
    }
}
