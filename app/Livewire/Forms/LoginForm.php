<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Support\Auth\LoginLockout;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * UC-19: pesan kesalahan generik, akun nonaktif ditolak dengan pesan yang sama,
     * dan penguncian akun (5x gagal/10 menit -> terkunci 15 menit).
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $lockout = app(LoginLockout::class);

        if ($lockout->isLocked($this->email)) {
            throw ValidationException::withMessages([
                'form.email' => 'Akun terkunci sementara karena terlalu banyak percobaan gagal. Coba lagi dalam '.$lockout->minutesRemaining($this->email).' menit.',
            ]);
        }

        $user = User::query()->where('email', $this->email)->first();

        if ($user === null || ! $user->isActive() || ! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());
            $lockout->recordFailure($this->email);

            throw ValidationException::withMessages([
                'form.email' => 'Surel atau kata sandi tidak sesuai.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        $lockout->clear($this->email);
    }

    /**
     * Lapis per-IP terhadap brute force massal (20/menit); penguncian per akun ditangani LoginLockout.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 20)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return 'login-ip:'.(request()->ip() ?? 'unknown');
    }
}
