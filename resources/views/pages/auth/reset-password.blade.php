<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->query('email');
    }

    public function resetPassword(): void
    {
        $validated = $this->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        session()->flash('status', __($status));
        $this->redirect(route('login', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-8">
        <flux:heading size="xl">{{ __('Define una nueva contraseña') }}</flux:heading>
        <flux:text class="mt-2">{{ __('Utiliza una contraseña segura que no uses en otros servicios.') }}</flux:text>
    </div>

    <form wire:submit="resetPassword" class="space-y-6">
        <flux:input wire:model="email" type="email" :label="__('Correo electrónico')" autocomplete="email" required />
        <flux:input wire:model="password" type="password" :label="__('Nueva contraseña')" autocomplete="new-password" required viewable />
        <flux:input wire:model="password_confirmation" type="password" :label="__('Confirmar contraseña')" autocomplete="new-password" required viewable />
        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('Actualizar contraseña') }}
        </flux:button>
    </form>
</div>
