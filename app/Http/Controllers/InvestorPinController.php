<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class InvestorPinController extends Controller
{
    public function showSetup()
    {
        $investorUser = Auth::guard('investor')->user();

        if ($investorUser->portal_pin) {
            return redirect()->route('investor.pin.verify');
        }

        return view('investor.auth.pin.setup');
    }

    public function storeSetup(Request $request)
    {
        $investorUser = Auth::guard('investor')->user();

        $request->validate([
            'pin'              => ['required', 'string', 'digits:6'],
            'pin_confirmation' => ['required', 'same:pin'],
        ], [
            'pin.digits'              => 'The PIN must be exactly 6 digits.',
            'pin_confirmation.same'   => 'The PIN confirmation does not match.',
        ]);

        $investorUser->update([
            'portal_pin' => Hash::make($request->pin),
        ]);

        session(['investor_pin_verified' => true]);

        return redirect()->intended(route('investor.dashboard'))
            ->with('success', 'PIN set successfully. You are now logged in.');
    }

    public function showVerify()
    {
        $investorUser = Auth::guard('investor')->user();

        if (! $investorUser->portal_pin) {
            return redirect()->route('investor.pin.setup');
        }

        return view('investor.auth.pin.verify');
    }

    public function checkPin(Request $request)
    {
        $investorUser = Auth::guard('investor')->user();

        $key = 'investor-pin:' . $investorUser->id;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'pin' => 'Too many incorrect attempts. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
            ]);
        }

        $request->validate([
            'pin' => ['required', 'string', 'digits:6'],
        ], [
            'pin.digits' => 'The PIN must be exactly 6 digits.',
        ]);

        if (! Hash::check($request->pin, $investorUser->portal_pin)) {
            RateLimiter::hit($key, 300); // 5-minute window

            throw ValidationException::withMessages([
                'pin' => 'Incorrect PIN. Please try again.',
            ]);
        }

        RateLimiter::clear($key);
        session(['investor_pin_verified' => true]);

        return redirect()->intended(route('investor.dashboard'));
    }
}
