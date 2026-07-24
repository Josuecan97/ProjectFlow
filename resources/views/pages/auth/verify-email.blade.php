<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {}; ?>

<div>
    <div class="mb-8">
        <flux:heading size="xl">{{ __('Verifica tu correo') }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Enviamos un enlace a tu correo. Ábrelo para activar tu cuenta.') }}
        </flux:text>
    </div>

    @if (session('status') === 'verification-link-sent')
        <flux:callout variant="success" class="mb-6">
            {{ __('Enviamos un nuevo enlace de verificación.') }}
        </flux:callout>
    @endif

    <div class="flex flex-col gap-3 sm:flex-row">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
            @csrf
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Reenviar enlace') }}
            </flux:button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="flex-1">
            @csrf
            <flux:button type="submit" variant="ghost" class="w-full">
                {{ __('Cerrar sesión') }}
            </flux:button>
        </form>
    </div>
</div>
