<?php

use App\Domain\Organizations\Support\CurrentOrganization;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Gate;
use App\Domain\Organizations\Support\SubscriptionAccess;

new #[Layout('components.layouts.app')] class extends Component
{
    public string $name = '';

    public ?string $legal_name = null;

    public ?string $tax_id = null;

    public ?string $email = null;

    public ?string $phone = null;

    public string $timezone = '';

    public string $currency = '';

    public function mount(CurrentOrganization $currentOrganization): void
    {
        $organization = $currentOrganization->get();
        Gate::authorize('update', $organization);

        $this->fill($organization->only([
            'name',
            'legal_name',
            'tax_id',
            'email',
            'phone',
            'timezone',
            'currency',
        ]));
    }

    public function updateOrganization(CurrentOrganization $currentOrganization, SubscriptionAccess $subscriptionAccess): void
    {
        $organization = $currentOrganization->get();
        Gate::authorize('update', $organization);
        $subscriptionAccess->authorizeWrites($organization);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'tax_id' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'timezone' => ['required', 'timezone:all'],
            'currency' => ['required', 'in:MXN,USD,EUR'],
        ]);

        $organization->update($validated);

        session()->flash('status', __('Organización actualizada.'));
    }
}; ?>

<div class="max-w-3xl">
    <flux:heading size="xl">{{ __('Configuración de la organización') }}</flux:heading>
    <flux:text class="mt-2">{{ __('Mantén actualizada la información general de tu empresa.') }}</flux:text>

    @if (session('status'))
        <flux:callout variant="success" class="mt-6">{{ session('status') }}</flux:callout>
    @endif

    <form wire:submit="updateOrganization" class="mt-8 space-y-6">
        <div class="grid gap-6 sm:grid-cols-2">
            <flux:input wire:model="name" :label="__('Nombre comercial')" required />
            <flux:input wire:model="legal_name" :label="__('Razón social')" />
            <flux:input wire:model="tax_id" :label="__('RFC / Identificador fiscal')" />
            <flux:input wire:model="email" type="email" :label="__('Correo')" />
            <flux:input wire:model="phone" :label="__('Teléfono')" />
            <flux:select wire:model="currency" :label="__('Moneda')">
                <flux:select.option value="MXN">MXN</flux:select.option>
                <flux:select.option value="USD">USD</flux:select.option>
                <flux:select.option value="EUR">EUR</flux:select.option>
            </flux:select>
        </div>

        <flux:select wire:model="timezone" :label="__('Zona horaria')">
            <flux:select.option value="America/Merida">Mérida</flux:select.option>
            <flux:select.option value="America/Mexico_City">Ciudad de México</flux:select.option>
            <flux:select.option value="America/Cancun">Cancún</flux:select.option>
            <flux:select.option value="America/Tijuana">Tijuana</flux:select.option>
        </flux:select>

        <flux:button type="submit" variant="primary">{{ __('Guardar cambios') }}</flux:button>
    </form>
</div>
