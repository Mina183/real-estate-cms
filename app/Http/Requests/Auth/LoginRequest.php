<?php

namespace App\Http\Requests\Auth;

use App\Models\AuthLog;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'                 => ['required', 'string', 'email'],
            'password'              => ['required', 'string'],
            'cf-turnstile-response' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'cf-turnstile-response.required' => 'Security check failed. Please try again.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Turnstile server-side verification
        $turnstile = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret'   => config('services.turnstile.secret_key'),
            'response' => $this->input('cf-turnstile-response'),
            'remoteip' => $this->ip(),
        ]);

        if (! ($turnstile->json('success') ?? false)) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => 'Security check failed. Please refresh the page and try again.',
            ]);
        }

        $user = \App\Models\User::where('email', $this->input('email'))->first();

        if (! $user || ! \Hash::check($this->input('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());

        AuthLog::create([
            'guard' => 'web',
            'user_id' => null,
            'email' => $this->input('email'),
            'event' => 'login_failed',
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
        ]);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // ✅ Block unapproved users
        if (! $user->is_approved) {
            throw ValidationException::withMessages([
                'email' => 'Your account is pending approval by an administrator.',
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
