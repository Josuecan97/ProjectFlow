<?php

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\People\Actions\ArchivePerson;
use App\Domain\People\Actions\RestorePerson;
use App\Domain\People\Enums\PersonStatus;
use App\Domain\People\Enums\PersonType;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRole;
use App\Domain\People\Queries\PersonIndexQuery;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $type = '';

    #[Url]
    public string $role = '';

    #[Url]
    public string $status = 'active';

    public function mount(CurrentOrganization $currentOrganization): void
    {
        Gate::authorize('viewAny', [Person::class, $currentOrganization->get()]);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'type', 'role', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function archive(int $personId, CurrentOrganization $currentOrganization, ArchivePerson $archivePerson): void
    {
        $person = Person::query()
            ->forOrganization($currentOrganization->get())
            ->findOrFail($personId);

        $archivePerson->handle(auth()->user(), $person);
        session()->flash('status', __('Persona archivada.'));
    }

    public function restore(int $personId, CurrentOrganization $currentOrganization, RestorePerson $restorePerson): void
    {
        $person = Person::withTrashed()
            ->forOrganization($currentOrganization->get())
            ->findOrFail($personId);

        $restorePerson->handle(auth()->user(), $person);
        session()->flash('status', __('Persona restaurada.'));
    }

    public function with(
        CurrentOrganization $currentOrganization,
        SubscriptionAccess $subscriptionAccess,
        PersonIndexQuery $query,
    ): array
    {
        $organization = $currentOrganization->get();
        $canWrite = $subscriptionAccess->allowsWrites($organization);

        return [
            'people' => $query->paginate(
                $organization,
                $this->search,
                PersonType::tryFrom($this->type),
                $this->role !== '' ? $this->role : null,
                PersonStatus::tryFrom($this->status) ?? PersonStatus::Active,
            ),
            'roles' => PersonRole::query()->orderBy('id')->get(),
            'canCreate' => $canWrite && Gate::allows('create', [Person::class, $organization]),
            'canUpdate' => $canWrite && Gate::allows('updateAny', [Person::class, $organization]),
            'canArchive' => $canWrite && Gate::allows('archiveAny', [Person::class, $organization]),
        ];
    }
}; ?>

<div>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Personas') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Expedientes de clientes, prospectos, proveedores y contactos.') }}</flux:text>
        </div>
        @if ($canCreate)
            <flux:button href="{{ route('people.create') }}" variant="primary">{{ __('Nueva Persona') }}</flux:button>
        @endif
    </div>

    @if (session('status'))
        <flux:callout variant="success" class="mt-6">{{ session('status') }}</flux:callout>
    @endif

    <div class="mt-8 grid gap-4 rounded-xl border border-zinc-200 bg-white p-5 md:grid-cols-4 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input wire:model.live.debounce.300ms="search" :label="__('Buscar')" placeholder="{{ __('Nombre, RFC, correo o teléfono') }}" />
        <flux:select wire:model.live="type" :label="__('Tipo')">
            <flux:select.option value="">{{ __('Todos') }}</flux:select.option>
            <flux:select.option value="individual">{{ __('Física') }}</flux:select.option>
            <flux:select.option value="organization">{{ __('Moral') }}</flux:select.option>
        </flux:select>
        <flux:select wire:model.live="role" :label="__('Rol')">
            <flux:select.option value="">{{ __('Todos') }}</flux:select.option>
            @foreach ($roles as $roleOption)
                <flux:select.option value="{{ $roleOption->code }}">{{ $roleOption->name }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:select wire:model.live="status" :label="__('Estado')">
            <flux:select.option value="active">{{ __('Activas') }}</flux:select.option>
            <flux:select.option value="archived">{{ __('Archivadas') }}</flux:select.option>
        </flux:select>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        @forelse ($people as $person)
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-200 p-5 last:border-0 dark:border-zinc-800">
                <div class="min-w-0">
                    <a href="{{ route('people.show', $person) }}" class="font-medium text-indigo-700 hover:underline dark:text-indigo-300">
                        {{ $person->display_name }}
                    </a>
                    <div class="mt-1 text-sm text-zinc-500">
                        {{ $person->type->label() }}
                        @if ($person->tax_id) · {{ $person->tax_id }} @endif
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($person->roles as $commercialRole)
                            <flux:badge size="sm">{{ $commercialRole->name }}</flux:badge>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if ($status === 'active')
                        @if ($canUpdate)
                            <flux:button href="{{ route('people.edit', $person) }}" size="sm">{{ __('Editar') }}</flux:button>
                        @endif
                        @if ($canArchive)
                            <flux:button wire:click="archive({{ $person->id }})" wire:confirm="{{ __('¿Archivar esta Persona?') }}" size="sm" variant="danger">{{ __('Archivar') }}</flux:button>
                        @endif
                    @elseif ($canArchive)
                            <flux:button wire:click="restore({{ $person->id }})" size="sm">{{ __('Restaurar') }}</flux:button>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-10 text-center">
                <flux:heading>{{ __('No se encontraron Personas') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Ajusta los filtros o registra una nueva Persona.') }}</flux:text>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $people->links() }}</div>
</div>
