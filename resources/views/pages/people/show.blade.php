<?php

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\People\Actions\RemovePersonContact;
use App\Domain\People\Actions\SavePersonContact;
use App\Domain\People\Enums\PersonType;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRelationship;
use App\Http\Requests\People\StorePersonContactRequest;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public Person $person;
    public string $related_person_id = '';
    public string $job_title = '';
    public bool $is_primary = false;
    public string $notes = '';

    public function mount(Person $person, CurrentOrganization $currentOrganization): void
    {
        abort_unless($person->organization_id === $currentOrganization->id(), 404);
        Gate::authorize('view', $person);
        $this->person = $person->load(['roles', 'contacts.related', 'contactFor.parent']);
    }

    public function saveContact(
        CurrentOrganization $currentOrganization,
        SavePersonContact $savePersonContact,
    ): void {
        abort_unless($this->person->organization_id === $currentOrganization->id(), 404);

        $validated = $this->validate(
            StorePersonContactRequest::rulesFor($currentOrganization->id()),
        );

        $contact = Person::query()
            ->forOrganization($currentOrganization->get())
            ->findOrFail($validated['related_person_id']);

        $savePersonContact->handle(auth()->user(), $this->person, $contact, $validated);
        $this->reset('related_person_id', 'job_title', 'is_primary', 'notes');
        $this->person->load(['contacts.related', 'contactFor.parent']);
        session()->flash('status', __('Contacto relacionado.'));
    }

    public function removeContact(
        int $relationshipId,
        CurrentOrganization $currentOrganization,
        RemovePersonContact $removePersonContact,
    ): void {
        $relationship = PersonRelationship::query()
            ->where('organization_id', $currentOrganization->id())
            ->findOrFail($relationshipId);

        $removePersonContact->handle(auth()->user(), $this->person, $relationship);
        $this->person->load('contacts.related');
        session()->flash('status', __('Contacto retirado.'));
    }

    public function with(
        CurrentOrganization $currentOrganization,
        SubscriptionAccess $subscriptionAccess,
    ): array
    {
        $organization = $currentOrganization->get();
        $canWrite = $subscriptionAccess->allowsWrites($organization);
        $canUpdate = $canWrite && Gate::allows('update', $this->person);

        return [
            'availableContacts' => $this->person->type === PersonType::Organization && $canUpdate
                ? Person::query()
                    ->forOrganization($organization)
                    ->whereKeyNot($this->person->id)
                    ->orderBy('display_name')
                    ->get(['id', 'display_name'])
                : collect(),
            'canWrite' => $canWrite,
        ];
    }
}; ?>

<div>
    @if (session('status'))
        <flux:callout variant="success" class="mb-6">{{ session('status') }}</flux:callout>
    @endif

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <flux:heading size="xl">{{ $person->display_name }}</flux:heading>
                <flux:badge>{{ $person->type->label() }}</flux:badge>
            </div>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($person->roles as $role)
                    <flux:badge color="indigo">{{ $role->name }}</flux:badge>
                @endforeach
            </div>
        </div>
        <div class="flex gap-3">
            @if ($canWrite)
                @can('update', $person)
                    <flux:button href="{{ route('people.edit', $person) }}" variant="primary">{{ __('Editar') }}</flux:button>
                @endcan
            @endif
            <flux:button href="{{ route('people.index') }}" variant="ghost">{{ __('Volver') }}</flux:button>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <section class="rounded-xl border border-zinc-200 bg-white p-6 lg:col-span-2 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:heading size="lg">{{ __('Información general') }}</flux:heading>
            <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                @foreach ([
                    __('Razón social') => $person->legal_name,
                    __('RFC') => $person->tax_id,
                    __('CURP') => $person->curp,
                    __('Correo') => $person->primary_email,
                    __('Teléfono') => $person->primary_phone,
                    __('Sitio web') => $person->website,
                ] as $label => $value)
                    @if ($value)
                        <div>
                            <dt class="text-sm text-zinc-500">{{ $label }}</dt>
                            <dd class="mt-1 font-medium">{{ $value }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>
            @if ($person->notes)
                <div class="mt-6 border-t border-zinc-200 pt-6 dark:border-zinc-800">
                    <div class="text-sm text-zinc-500">{{ __('Notas') }}</div>
                    <p class="mt-2 whitespace-pre-line">{{ $person->notes }}</p>
                </div>
            @endif
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:heading size="lg">{{ __('Dirección') }}</flux:heading>
            @if ($person->address)
                <div class="mt-4 space-y-1 text-sm">
                    @foreach ($person->address as $value)
                        <div>{{ $value }}</div>
                    @endforeach
                </div>
            @else
                <flux:text class="mt-4">{{ __('Sin dirección registrada.') }}</flux:text>
            @endif
        </section>
    </div>

    <section class="mt-6 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex items-center justify-between gap-4">
            <flux:heading size="lg">{{ __('Contactos relacionados') }}</flux:heading>
            @if ($person->type === PersonType::Organization)
                <flux:badge>{{ $person->contacts->count() }}</flux:badge>
            @endif
        </div>

        @if ($person->type === PersonType::Organization)
            @if ($canWrite)
                @can('update', $person)
                    <form wire:submit="saveContact" class="mt-5 grid gap-4 rounded-lg bg-zinc-50 p-4 md:grid-cols-2 dark:bg-zinc-950">
                    <flux:select wire:model="related_person_id" :label="__('Persona de contacto')" required>
                        <flux:select.option value="">{{ __('Selecciona una Persona') }}</flux:select.option>
                        @foreach ($availableContacts as $availableContact)
                            <flux:select.option value="{{ $availableContact->id }}">{{ $availableContact->display_name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="job_title" :label="__('Cargo')" />
                    <flux:textarea wire:model="notes" :label="__('Observaciones')" rows="2" />
                    <div class="flex items-end justify-between gap-4">
                        <flux:checkbox wire:model="is_primary" :label="__('Contacto principal')" />
                        <flux:button type="submit" variant="primary">{{ __('Relacionar') }}</flux:button>
                    </div>
                    </form>
                @endcan
            @endif
        @endif

        @forelse ($person->contacts as $relationship)
            <div class="mt-4 flex items-center justify-between rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                <div>
                    @if ($relationship->related->trashed())
                        <span class="font-medium">{{ $relationship->related->display_name }}</span>
                        <flux:badge size="sm">{{ __('Archivada') }}</flux:badge>
                    @else
                        <a href="{{ route('people.show', $relationship->related) }}" class="font-medium text-indigo-700 dark:text-indigo-300">
                            {{ $relationship->related->display_name }}
                        </a>
                    @endif
                    <div class="text-sm text-zinc-500">{{ $relationship->job_title }}</div>
                </div>
                @if ($relationship->is_primary)
                    <flux:badge color="amber">{{ __('Principal') }}</flux:badge>
                @endif
                @if ($canWrite)
                    @can('update', $person)
                        <flux:button wire:click="removeContact({{ $relationship->id }})" wire:confirm="{{ __('¿Retirar esta relación?') }}" size="sm" variant="danger">
                            {{ __('Retirar') }}
                        </flux:button>
                    @endcan
                @endif
            </div>
        @empty
            <flux:text class="mt-4">{{ __('Aún no hay contactos relacionados.') }}</flux:text>
        @endforelse
    </section>

    @if ($person->contactFor->isNotEmpty())
        <section class="mt-6 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:heading size="lg">{{ __('Es contacto de') }}</flux:heading>
            @foreach ($person->contactFor as $relationship)
                @if ($relationship->parent->trashed())
                    <div class="mt-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                        <span class="font-medium">{{ $relationship->parent->display_name }}</span>
                        <flux:badge size="sm">{{ __('Archivada') }}</flux:badge>
                    </div>
                @else
                    <a href="{{ route('people.show', $relationship->parent) }}" class="mt-4 block rounded-lg border border-zinc-200 p-4 font-medium text-indigo-700 dark:border-zinc-800 dark:text-indigo-300">
                        {{ $relationship->parent->display_name }}
                    </a>
                @endif
            @endforeach
        </section>
    @endif
</div>
