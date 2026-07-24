<?php

use App\Domain\Organizations\Actions\CreateOrganization;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public string $name = '';

    public ?string $legal_name = null;

    public string $timezone = 'America/Merida';

    public string $currency = 'MXN';

    public function createOrganization(CreateOrganization $createOrganization): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'timezone' => ['required', 'timezone:all'],
            'currency' => ['required', 'in:MXN,USD,EUR'],
        ]);

        $organization = $createOrganization->handle(auth()->user(), $validated);

        session()->put('organization_id', $organization->id);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="mx-auto max-w-2xl">
    <flux:heading size="xl">{{ __('Crea tu organización') }}</flux:heading>
    <flux:text class="mt-2">
        {{ __('Esta será la empresa propietaria de los proyectos y recibirá una prueba completa de 14 días.') }}
    </flux:text>

    <form wire:submit="createOrganization" class="mt-8 space-y-6">
        <flux:input wire:model="name" :label="__('Nombre comercial')" required autofocus />
        <flux:input wire:model="legal_name" :label="__('Razón social')" />

        <div class="grid gap-6 sm:grid-cols-2">
            <flux:select wire:model="timezone" :label="__('Zona horaria')">
                <flux:select.option value="America/Merida">Mérida</flux:select.option>
                <flux:select.option value="America/Mexico_City">Ciudad de México</flux:select.option>
                <flux:select.option value="America/Cancun">Cancún</flux:select.option>
                <flux:select.option value="America/Tijuana">Tijuana</flux:select.option>
            </flux:select>

            <flux:select wire:model="currency" :label="__('Moneda')">
                <flux:select.option value="MXN">MXN — Peso mexicano</flux:select.option>
                <flux:select.option value="USD">USD — Dólar</flux:select.option>
                <flux:select.option value="EUR">EUR — Euro</flux:select.option>
            </flux:select>
        </div>

        <flux:button type="submit" variant="primary">{{ __('Crear organización') }}</flux:button>
    </form>
</div>
