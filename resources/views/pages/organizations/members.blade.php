<?php

use App\Domain\Organizations\Actions\InviteOrganizationMember;
use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Enums\SystemRole;
use App\Domain\Organizations\Models\Role;
use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Organizations\Support\SubscriptionAccess;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public string $email = '';

    public string $roleId = '';

    public function mount(CurrentOrganization $currentOrganization): void
    {
        Gate::authorize('viewMembers', $currentOrganization->get());
        $this->roleId = (string) Role::query()->where('code', SystemRole::Collaborator->value)->value('id');
    }

    public function invite(CurrentOrganization $currentOrganization, SubscriptionAccess $subscriptionAccess, InviteOrganizationMember $invite): void
    {
        $organization = $currentOrganization->get();
        Gate::authorize('inviteMembers', $organization);
        $subscriptionAccess->authorizeWrites($organization);

        $validated = $this->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'roleId' => ['required', Rule::exists('roles', 'id')->whereNull('organization_id')],
        ]);

        $role = Role::query()->whereNull('organization_id')->findOrFail($validated['roleId']);
        $invite->handle($organization, auth()->user(), $validated['email'], $role);

        $this->reset('email');
        session()->flash('status', __('Invitación enviada.'));
    }

    public function setStatus(int $memberId, string $status, CurrentOrganization $currentOrganization, SubscriptionAccess $subscriptionAccess): void
    {
        $organization = $currentOrganization->get();
        Gate::authorize('manageMembers', $organization);
        $subscriptionAccess->authorizeWrites($organization);

        $member = $organization->members()->findOrFail($memberId);
        abort_if($member->roles()->where('code', SystemRole::Owner->value)->exists(), 422);
        $member->update(['status' => OrganizationMemberStatus::from($status)]);
    }

    public function remove(int $memberId, CurrentOrganization $currentOrganization, SubscriptionAccess $subscriptionAccess): void
    {
        $organization = $currentOrganization->get();
        Gate::authorize('manageMembers', $organization);
        $subscriptionAccess->authorizeWrites($organization);

        $member = $organization->members()->findOrFail($memberId);
        abort_if($member->roles()->where('code', SystemRole::Owner->value)->exists(), 422);
        $member->delete();
    }

    public function assignRole(int $memberId, int $roleId, CurrentOrganization $currentOrganization, SubscriptionAccess $subscriptionAccess): void
    {
        $organization = $currentOrganization->get();
        Gate::authorize('manageMembers', $organization);
        $subscriptionAccess->authorizeWrites($organization);

        $member = $organization->members()->findOrFail($memberId);
        abort_if($member->roles()->where('code', SystemRole::Owner->value)->exists(), 422);

        $role = Role::query()
            ->whereNull('organization_id')
            ->where('code', '!=', SystemRole::Owner->value)
            ->findOrFail($roleId);

        $member->roles()->sync([$role->id]);
    }

    public function with(CurrentOrganization $currentOrganization): array
    {
        $organization = $currentOrganization->get();

        return [
            'members' => $organization->members()->with(['user', 'roles'])->orderBy('id')->get(),
            'roles' => Role::query()->whereNull('organization_id')
                ->where('code', '!=', SystemRole::Owner->value)->orderBy('id')->get(),
            'canInvite' => Gate::allows('inviteMembers', $organization),
            'canManage' => Gate::allows('manageMembers', $organization),
        ];
    }
}; ?>

<div>
    <div class="flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Equipo') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Miembros, accesos e invitaciones de la organización actual.') }}</flux:text>
        </div>
        @can('viewRoles', app(CurrentOrganization::class)->get())
            <flux:button href="{{ route('organization.roles') }}" variant="ghost">{{ __('Ver roles') }}</flux:button>
        @endcan
    </div>

    @if (session('status'))
        <flux:callout variant="success" class="mt-6">{{ session('status') }}</flux:callout>
    @endif

    @if ($canInvite)
        <form wire:submit="invite" class="mt-8 grid gap-4 rounded-xl border border-zinc-200 bg-white p-6 sm:grid-cols-[1fr_14rem_auto] dark:border-zinc-800 dark:bg-zinc-900">
            <flux:input wire:model="email" type="email" :label="__('Correo electrónico')" required />
            <flux:select wire:model="roleId" :label="__('Rol')">
                @foreach ($roles as $role)
                    <flux:select.option value="{{ $role->id }}">{{ $role->name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:button type="submit" variant="primary" class="self-end">{{ __('Invitar') }}</flux:button>
        </form>
    @endif

    <div class="mt-8 overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        @foreach ($members as $member)
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-zinc-200 p-5 last:border-0 dark:border-zinc-800">
                <div>
                    <div class="font-medium">{{ $member->user->name }}</div>
                    <div class="text-sm text-zinc-500">{{ $member->user->email }} · {{ $member->roles->pluck('name')->join(', ') }}</div>
                </div>
                <div class="flex items-center gap-2">
                    <flux:badge>{{ $member->status->label() }}</flux:badge>
                    @if ($canManage && ! $member->roles->contains('code', SystemRole::Owner->value))
                        <flux:select
                            wire:change="assignRole({{ $member->id }}, $event.target.value)"
                            size="sm"
                            aria-label="{{ __('Rol de :name', ['name' => $member->user->name]) }}"
                        >
                            @foreach ($roles as $role)
                                <flux:select.option value="{{ $role->id }}" :selected="$member->roles->contains($role)">
                                    {{ $role->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        @if ($member->status === OrganizationMemberStatus::Active)
                            <flux:button wire:click="setStatus({{ $member->id }}, 'suspended')" size="sm" variant="ghost">{{ __('Suspender') }}</flux:button>
                        @else
                            <flux:button wire:click="setStatus({{ $member->id }}, 'active')" size="sm" variant="ghost">{{ __('Activar') }}</flux:button>
                        @endif
                        <flux:button wire:click="remove({{ $member->id }})" wire:confirm="{{ __('¿Retirar a este miembro?') }}" size="sm" variant="danger">{{ __('Retirar') }}</flux:button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
