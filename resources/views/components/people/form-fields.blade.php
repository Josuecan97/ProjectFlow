@props(['roles', 'type', 'duplicateNames' => []])

<div class="grid gap-6 sm:grid-cols-2">
    <flux:select wire:model.live="type" :label="__('Tipo de Persona')">
        <flux:select.option value="individual">{{ __('Persona física') }}</flux:select.option>
        <flux:select.option value="organization">{{ __('Persona moral') }}</flux:select.option>
    </flux:select>

    @if ($type === 'individual')
        <div></div>
        <flux:input wire:model="first_name" :label="__('Nombre')" required />
        <flux:input wire:model="last_name" :label="__('Apellidos')" />
    @else
        <flux:input wire:model="display_name" :label="__('Nombre comercial')" />
        <flux:input wire:model="legal_name" :label="__('Razón social')" required />
    @endif

    <flux:input wire:model="tax_id" :label="__('RFC')" />
    @if ($type === 'individual')
        <flux:input wire:model="curp" :label="__('CURP')" maxlength="18" />
    @else
        <div></div>
    @endif

    <flux:input wire:model.blur="primary_email" wire:blur="checkDuplicates" type="email" :label="__('Correo principal')" />
    <flux:input wire:model.blur="primary_phone" wire:blur="checkDuplicates" :label="__('Teléfono principal')" />
    <flux:input wire:model="website" type="url" :label="__('Sitio web')" />
</div>

@if ($duplicateNames !== [])
    <flux:callout variant="warning">
        <flux:heading>{{ __('Posible duplicado') }}</flux:heading>
        <flux:text class="mt-1">
            {{ __('El correo o teléfono coincide con: :names. Puedes guardar si se trata de otra Persona.', [
                'names' => implode(', ', $duplicateNames),
            ]) }}
        </flux:text>
    </flux:callout>
@endif

<fieldset>
    <legend class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Roles comerciales') }}</legend>
    <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($roles as $role)
            <label class="flex items-center gap-3 rounded-lg border border-zinc-200 px-4 py-3 text-sm dark:border-zinc-800">
                <input wire:model="role_ids" type="checkbox" value="{{ $role->id }}" class="rounded border-zinc-300 text-indigo-600">
                <span>{{ $role->name }}</span>
            </label>
        @endforeach
    </div>
</fieldset>

<fieldset class="space-y-4">
    <legend class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Dirección') }}</legend>
    <div class="grid gap-6 sm:grid-cols-2">
        <flux:input wire:model="address.street" :label="__('Calle y número')" class="sm:col-span-2" />
        <flux:input wire:model="address.city" :label="__('Ciudad')" />
        <flux:input wire:model="address.state" :label="__('Estado')" />
        <flux:input wire:model="address.postal_code" :label="__('Código postal')" />
        <flux:input wire:model="address.country" :label="__('País (ISO)')" maxlength="2" />
    </div>
</fieldset>

<flux:textarea wire:model="notes" :label="__('Notas generales')" rows="5" />
