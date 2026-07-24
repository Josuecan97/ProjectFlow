<?php

use App\Models\User;
use App\Domain\Organizations\Actions\AcceptOrganizationInvitation;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $invitation = '';

    public function mount(): void
    {
        $this->invitation = (string) request()->query('invitation', '');
    }

    public function register(AcceptOrganizationInvitation $acceptInvitation): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = User::query()->create($validated);

        event(new Registered($user));
        Auth::login($user);
        session()->regenerate();

        if ($this->invitation !== '') {
            $member = $acceptInvitation->handle($user, $this->invitation);
            session(['organization_id' => $member->organization_id]);
        }

        $this->redirect(route('verification.notice', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-8">
        <flux:heading size="xl">{{ __('Crea tu cuenta') }}</flux:heading>
        <flux:text class="mt-2">{{ __('Comienza a organizar el trabajo de tu empresa.') }}</flux:text>
    </div>

    <form wire:submit="register" class="space-y-6">
        <flux:input wire:model="name" :label="__('Nombre')" autocomplete="name" required autofocus />
        <flux:input wire:model="email" type="email" :label="__('Correo electrónico')" autocomplete="email" required />
        <flux:input wire:model="password" type="password" :label="__('Contraseña')" autocomplete="new-password" required viewable />
        <flux:input wire:model="password_confirmation" type="password" :label="__('Confirmar contraseña')" autocomplete="new-password" required viewable />

        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('Crear cuenta') }}
        </flux:button>
    </form>

    <flux:text class="mt-8 text-center">
        {{ __('¿Ya tienes cuenta?') }}
        <flux:link href="{{ route('login', array_filter(['invitation' => $invitation])) }}" wire:navigate>
            {{ __('Iniciar sesión') }}
        </flux:link>
    </flux:text>
</div>
