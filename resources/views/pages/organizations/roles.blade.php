<?php

use App\Domain\Organizations\Models\Role;
use App\Domain\Organizations\Support\CurrentOrganization;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public function mount(CurrentOrganization $currentOrganization): void
    {
        Gate::authorize('viewRoles', $currentOrganization->get());
    }

    public function with(): array
    {
        return [
            'roles' => Role::query()
                ->whereNull('organization_id')
                ->with('permissions')
                ->orderBy('id')
                ->get(),
        ];
    }
}; ?>

<div>
    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Roles y permisos') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Catálogo base protegido del sistema.') }}</flux:text>
        </div>
        <flux:button href="{{ route('organization.members') }}" variant="ghost">{{ __('Volver al equipo') }}</flux:button>
    </div>

    <div class="mt-8 grid gap-5 lg:grid-cols-2">
        @foreach ($roles as $role)
            <section class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <flux:heading size="lg">{{ $role->name }}</flux:heading>
                <flux:text class="mt-3">{{ trans_choice(':count permiso|:count permisos', $role->permissions->count(), ['count' => $role->permissions->count()]) }}</flux:text>
                <ul class="mt-4 grid gap-2 text-sm text-zinc-600 dark:text-zinc-300">
                    @foreach ($role->permissions as $permission)
                        <li>• {{ $permission->description }}</li>
                    @endforeach
                </ul>
            </section>
        @endforeach
    </div>
</div>
