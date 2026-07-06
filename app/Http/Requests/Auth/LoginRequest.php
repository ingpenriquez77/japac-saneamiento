<?php

namespace App\Http\Requests\Auth;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'usuario' => ['required', 'string'], // 👈 CORREGIDO: Cambiado de 'email' a 'usuario' y removida la regla de formato de correo
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

        $user = \App\Models\User::where('usuario', $this->input('usuario'))->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($this->input('password'), $user->password)) {

            // 🔒 NUEVO: Verificar si el usuario está dado de Baja o Inactivo
            if (($user->estado_operativo ?? 'Activo') !== 'Activo') {
                \Illuminate\Support\Facades\RateLimiter::hit($this->throttleKey());

                throw \Illuminate\Validation\ValidationException::withMessages([
                    'cuenta_inactiva' => 'Tu usuario se encuentra inactivo en el sistema.',
                ]);
            }

            // Si está activo, inicia sesión normalmente
            \Illuminate\Support\Facades\Auth::login($user, $this->boolean('remember'));
            \Illuminate\Support\Facades\RateLimiter::clear($this->throttleKey());
            return;
        }

        \Illuminate\Support\Facades\RateLimiter::hit($this->throttleKey());

        throw \Illuminate\Validation\ValidationException::withMessages([
            'usuario' => __('auth.failed'),
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
            'usuario' => trans('auth.throttle', [ // 👈 CORREGIDO: El error de bloqueo ahora se ancla al campo 'usuario'
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
        // 👈 CORREGIDO: Cambiado $this->string('email') por 'usuario'
        return Str::transliterate(Str::lower($this->string('usuario')).'|'.$this->ip());
    }
}
