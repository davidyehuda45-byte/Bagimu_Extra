<?php

namespace App\Http\Requests\Auth;

use App\Enums\LoginMethod;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
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
     * Kolom `email` menerima alamat email ATAU username.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = $this->string('email')->toString();
        $credentials = ['password' => $this->string('password')->toString()];

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $login;
            $user = User::where('email', $login)->first();
        } else {
            $user = User::where('username', $login)->first();

            if (! $user) {
                RateLimiter::hit($this->throttleKey());
                $this->recordHistory(null, $login, false, 'Username tidak ditemukan');

                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }

            $credentials['email'] = $user->email;
        }

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            $this->recordHistory($user?->getAuthIdentifier(), $login, false, 'Password salah');

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        $authed = Auth::user();
        session(['session_version' => $authed->getAttribute('session_version')]);
        $this->recordHistory($authed->getAuthIdentifier(), $authed->email, true, null);
    }

    private function recordHistory(
        string|int|null $userId,
        ?string $email,
        bool $success,
        ?string $reason,
    ): void {
        LoginHistory::create([
            'user_id' => $userId,
            'email' => $email,
            'ip_hash' => hash('sha256', $this->ip().' '.config('app.key')),
            'method' => LoginMethod::USERNAME,
            'success' => $success,
            'reason' => $reason,
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
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
