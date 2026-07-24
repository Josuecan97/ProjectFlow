<?php

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfile(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $emailChanged = $user->email !== $validated['email'];

        $user->fill($validated);

        if ($emailChanged && $user instanceof MustVerifyEmail) {
            $user->email_verified_at = null;
        }

        $user->save();

        if (! $user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        session()->flash('status', __('Perfil actualizado.'));
    }
}; ?>

<div class="max-w-2xl">
    <flux:heading size="xl">{{ __('Perfil') }}</flux:heading>
    <flux:text class="mt-2">{{ __('Actualiza tu nombre y correo de acceso.') }}</flux:text>

    @if (session('status'))
        <flux:callout variant="success" class="mt-6">{{ session('status') }}</flux:callout>
    @endif

    <form wire:submit="updateProfile" class="mt-8 space-y-6">
        <flux:input wire:model="name" :label="__('Nombre')" autocomplete="name" required />
        <flux:input wire:model="email" type="email" :label="__('Correo electrónico')" autocomplete="email" required />
        <flux:button type="submit" variant="primary">{{ __('Guardar cambios') }}</flux:button>
    </form>
</div>
