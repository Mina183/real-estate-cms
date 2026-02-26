<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestorTwoFactorController extends Controller
{
    public function setup()
    {
        $user = Auth::guard('investor')->user();
        
        $google2fa = app('pragmarx.google2fa');
        
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

        return view('investor.auth.2fa.setup', compact('secret', 'qrCodeUrl'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::guard('investor')->user();
        $secret = decrypt($user->two_factor_secret);
        $google2fa = app('pragmarx.google2fa');

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_enabled_at' => now(),
        ]);

        $request->session()->put('investor_2fa_verified', true);

        return redirect()->route('investor.dashboard')
            ->with('success', 'Two-factor authentication enabled successfully!');
    }

    public function verify()
    {
        return view('investor.auth.2fa.verify');
    }

    public function check(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::guard('investor')->user();
        $secret = decrypt($user->two_factor_secret);
        $google2fa = app('pragmarx.google2fa');

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $request->session()->put('investor_2fa_verified', true);

        return redirect()->intended(route('investor.dashboard'));
    }
}