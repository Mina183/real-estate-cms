<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Http\Requests\Auth\RegisterRequest;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $supervisors = \App\Models\User::where('is_approved', true)
        ->whereIn('role', ['channel_partner'])
        ->get();

return view('auth.register', compact('supervisors'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role, // ✅ role comes from the form
            'requested_role' => $request->role, // ✅ store selected choice
            'is_approved' => false,
        ]);
        \Log::info('Register controller reached before event');
        event(new Registered($user));
        return redirect()->route('register')->with('pendingApproval', true);
    }
}
