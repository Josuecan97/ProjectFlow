<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\People\Models\Person;
use Illuminate\Support\Facades\Gate;

new #[Layout('components.layouts.app')] class extends Component
{
    public function with(CurrentOrganization $currentOrganization, SubscriptionAccess $subscriptionAccess): array
    {
        $organization = $currentOrganization->get();

        return [
            'organization' => $organization,
            'memberCount' => $organization->members()->count(),
            'subscription' => $subscriptionAccess->current($organization),
            'personCount' => $organization->people()->count(),
            'canManageOrganization' => Gate::allows('update', $organization),
            'canViewMembers' => Gate::allows('viewMembers', $organization),
            'canViewPeople' => Gate::allows('viewAny', [Person::class, $organization]),
        ];
    }
}; ?>

<div class="space-y-8">
    <div>
        <flux:heading size="xl">{{ __('Hola, :name', ['name' => auth()->user()->name]) }}</flux:heading>
        <flux:text class="mt-2">
            {{ __('Estás trabajando en :organization.', ['organization' => $organization->name]) }}
        </flux:text>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            [
                'title' => __('Organización'),
                'text' => $organization->name,
                'href' => $canManageOrganization ? route('organization.settings') : null,
                'badge' => __('Configurar'),
            ],
            [
                'title' => __('Equipo'),
                'text' => trans_choice(':count miembro|:count miembros', $memberCount, ['count' => $memberCount]),
                'href' => $canViewMembers ? route('organization.members') : null,
                'badge' => __('Administrar'),
            ],
            [
                'title' => __('Personas'),
                'text' => trans_choice(':count expediente|:count expedientes', $personCount, ['count' => $personCount]),
                'href' => $canViewPeople ? route('people.index') : null,
                'badge' => __('Consultar'),
            ],
            [
                'title' => __('Membresía'),
                'text' => $subscription?->status->label() ?? __('Sin membresía'),
                'href' => route('organization.subscription'),
                'badge' => $subscription?->remainingDays() !== null
                    ? trans_choice(':count día|:count días', $subscription->remainingDays(), ['count' => $subscription->remainingDays()])
                    : __('Ver detalle'),
            ],
        ] as $item)
            <a @if ($item['href']) href="{{ $item['href'] }}" @endif class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <flux:heading>{{ $item['title'] }}</flux:heading>
                <flux:text class="mt-2">{{ $item['text'] }}</flux:text>
                <flux:badge class="mt-5">{{ $item['badge'] }}</flux:badge>
            </a>
        @endforeach
    </div>
</div>
