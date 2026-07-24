<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public string $invitation = '';

    public function mount(): void
    {
        $this->invitation = (string) request()->query('invitation', '');
    }

    public function login(): void
    {
        $credentials = $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'email' => __('Demasiados intentos. Intenta nuevamente en :seconds segundos.', [
                    'seconds' => $seconds,
                ]),
            ]);
        }

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => __('Las credenciales no son correctas.'),
            ]);
        }

        RateLimiter::clear($key);
        session()->regenerate();

        if ($this->invitation !== '') {
            $this->redirect(route('invitations.show', $this->invitation, absolute: false), navigate: true);

            return;
        }

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-8">
        <flux:heading size="xl">{{ __('Bienvenido de nuevo') }}</flux:heading>
        <flux:text class="mt-2">{{ __('Ingresa para continuar con tus proyectos.') }}</flux:text>
    </div>

    <form wire:submit="login" class="space-y-6">
        <flux:input
            wire:model="email"
            type="email"
            :label="__('Correo electrónico')"
            autocomplete="email"
            required
            autofocus
        />

        <flux:input
            wire:model="password"
            type="password"
            :label="__('Contraseña')"
            autocomplete="current-password"
            required
            viewable
        />

        <div class="flex items-center justify-between gap-4">
            <flux:checkbox wire:model="remember" :label="__('Recordarme')" />
            <flux:link href="{{ route('password.request') }}" wire:navigate>
                {{ __('¿Olvidaste tu contraseña?') }}
            </flux:link>
        </div>

        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('Iniciar sesión') }}
        </flux:button>
    </form>

    <flux:text class="mt-8 text-center">
        {{ __('¿Aún no tienes cuenta?') }}
        <flux:link href="{{ route('register', array_filter(['invitation' => $invitation])) }}" wire:navigate>
            {{ __('Crear cuenta') }}
        </flux:link>
    </flux:text>
</div>
