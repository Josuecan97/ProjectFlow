<?php

use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Models\OrganizationMember;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    /** @return Collection<int, OrganizationMember> */
    #[Computed]
    public function memberships(): Collection
    {
        return OrganizationMember::query()
            ->where('user_id', auth()->id())
            ->where('status', OrganizationMemberStatus::Active)
            ->with('organization')
            ->orderBy('id')
            ->get();
    }

    public function selectOrganization(int $organizationId): void
    {
        $authorized = $this->memberships()
            ->contains(fn (OrganizationMember $member): bool => $member->organization_id === $organizationId);

        abort_unless($authorized, 403);

        session()->put('organization_id', $organizationId);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <div class="flex items-end justify-between gap-6">
        <div>
            <flux:heading size="xl">{{ __('Selecciona una organización') }}</flux:heading>
            <flux:text class="mt-2">{{ __('El contexto elegido determina qué información puedes consultar.') }}</flux:text>
        </div>

        <flux:button href="{{ route('organizations.create') }}" variant="primary">
            {{ __('Nueva organización') }}
        </flux:button>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2">
        @foreach ($this->memberships as $membership)
            <button
                type="button"
                wire:click="selectOrganization({{ $membership->organization_id }})"
                class="rounded-2xl border border-zinc-200 bg-white p-6 text-left shadow-sm transition hover:border-indigo-400 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900"
            >
                <span class="font-semibold">{{ $membership->organization->name }}</span>
                <span class="mt-2 block text-sm text-zinc-500">{{ __('Miembro activo') }}</span>
            </button>
        @endforeach
    </div>
</div>
