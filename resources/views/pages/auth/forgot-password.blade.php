<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component
{
    public string $email = '';

    public function sendResetLink(): void
    {
        $this->validate(['email' => ['required', 'email']]);

        Password::sendResetLink(['email' => $this->email]);

        session()->flash('status', __('Si existe una cuenta con ese correo, enviaremos las instrucciones.'));
    }
}; ?>

<div>
    <div class="mb-8">
        <flux:heading size="xl">{{ __('Recupera tu contraseña') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Escribe tu correo y te enviaremos un enlace de recuperación.') }}
        </flux:text>
    </div>

    @if (session('status'))
        <flux:callout variant="success" class="mb-6">
            {{ session('status') }}
        </flux:callout>
    @endif

    <form wire:submit="sendResetLink" class="space-y-6">
        <flux:input wire:model="email" type="email" :label="__('Correo electrónico')" autocomplete="email" required autofocus />
        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('Enviar enlace') }}
        </flux:button>
    </form>

    <flux:link href="{{ route('login') }}" wire:navigate class="mt-8 inline-block">
        {{ __('Volver al inicio de sesión') }}
    </flux:link>
</div>
