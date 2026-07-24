<?php

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\People\Actions\UpdatePerson;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRole;
use App\Domain\People\Services\PersonDataNormalizer;
use App\Domain\People\Services\PersonDuplicateDetector;
use App\Http\Requests\People\UpdatePersonRequest;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public Person $person;
    public string $type = 'individual';
    public string $display_name = '';
    public string $legal_name = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $tax_id = '';
    public string $curp = '';
    public string $primary_email = '';
    public string $primary_phone = '';
    public string $website = '';
    public array $address = [];
    public string $notes = '';
    public array $role_ids = [];
    public array $duplicate_names = [];

    public function mount(
        Person $person,
        CurrentOrganization $currentOrganization,
        SubscriptionAccess $subscriptionAccess,
    ): void
    {
        abort_unless($person->organization_id === $currentOrganization->id(), 404);
        Gate::authorize('update', $person);
        $subscriptionAccess->authorizeWrites($currentOrganization->get());
        $this->person = $person->load('roles');
        $this->type = $person->type->value;
        $this->display_name = $person->display_name;
        $this->legal_name = $person->legal_name ?? '';
        $this->first_name = $person->first_name ?? '';
        $this->last_name = $person->last_name ?? '';
        $this->tax_id = $person->tax_id ?? '';
        $this->curp = $person->curp ?? '';
        $this->primary_email = $person->primary_email ?? '';
        $this->primary_phone = $person->primary_phone ?? '';
        $this->website = $person->website ?? '';
        $this->address = $person->address ?? ['country' => 'MX'];
        $this->notes = $person->notes ?? '';
        $this->role_ids = $person->roles->modelKeys();
    }

    public function checkDuplicates(
        CurrentOrganization $currentOrganization,
        PersonDataNormalizer $normalizer,
        PersonDuplicateDetector $detector,
    ): void {
        $normalized = $normalizer->normalize($this->attributes());
        $this->duplicate_names = $detector->find(
            $currentOrganization->get(),
            $normalized['primary_email'],
            $normalized['primary_phone'],
            $this->person->id,
        )->pluck('display_name')->all();
    }

    public function save(
        CurrentOrganization $currentOrganization,
        PersonDataNormalizer $normalizer,
        UpdatePerson $updatePerson,
    ): void {
        $organization = $currentOrganization->get();
        $normalized = $normalizer->normalize($this->attributes());
        $this->applyNormalizedIdentifiers($normalized);
        $validated = $this->validate(
            UpdatePersonRequest::rulesFor($organization->id, $this->person->id),
        );

        $person = $updatePerson->handle(
            auth()->user(),
            $this->person,
            $validated,
            $validated['role_ids'] ?? [],
        );

        session()->flash('status', __('Persona actualizada.'));
        $this->redirect(route('people.show', $person, absolute: false), navigate: true);
    }

    public function with(): array
    {
        return ['roles' => PersonRole::query()->orderBy('id')->get()];
    }

    private function attributes(): array
    {
        return [
            'type' => $this->type,
            'display_name' => $this->display_name,
            'legal_name' => $this->legal_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'tax_id' => $this->tax_id,
            'curp' => $this->curp,
            'primary_email' => $this->primary_email,
            'primary_phone' => $this->primary_phone,
            'website' => $this->website,
            'address' => $this->address,
            'notes' => $this->notes,
            'role_ids' => $this->role_ids,
        ];
    }

    private function applyNormalizedIdentifiers(array $normalized): void
    {
        $this->tax_id = $normalized['tax_id'] ?? '';
        $this->curp = $normalized['curp'] ?? '';
        $this->primary_email = $normalized['primary_email'] ?? '';
        $this->primary_phone = $normalized['primary_phone'] ?? '';
    }
}; ?>

<div class="max-w-4xl">
    <flux:heading size="xl">{{ __('Editar Persona') }}</flux:heading>
    <flux:text class="mt-2">{{ $person->display_name }}</flux:text>

    <form wire:submit="save" class="mt-8 space-y-8">
        <x-people.form-fields :roles="$roles" :type="$type" :duplicate-names="$duplicate_names" />
        <div class="flex gap-3">
            <flux:button type="submit" variant="primary">{{ __('Guardar cambios') }}</flux:button>
            <flux:button href="{{ route('people.show', $person) }}" variant="ghost">{{ __('Cancelar') }}</flux:button>
        </div>
    </form>
</div>
