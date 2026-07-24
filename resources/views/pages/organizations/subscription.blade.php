<?php

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Organizations\Support\SubscriptionAccess;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public function mount(CurrentOrganization $currentOrganization): void
    {
        Gate::authorize('viewSubscription', $currentOrganization->get());
    }

    public function with(CurrentOrganization $currentOrganization, SubscriptionAccess $access): array
    {
        $subscription = $access->current($currentOrganization->get());

        return [
            'subscription' => $subscription,
            'events' => $subscription?->events()->with('actor')->latest('occurred_at')->get() ?? collect(),
        ];
    }
}; ?>

<div class="max-w-4xl">
    <flux:heading size="xl">{{ __('Membresía') }}</flux:heading>
    <flux:text class="mt-2">{{ __('Estado comercial y vigencia de la organización actual.') }}</flux:text>

    @if ($subscription)
        <section class="mt-8 rounded-2xl border border-zinc-200 bg-white p-7 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <flux:badge>{{ $subscription->status->label() }}</flux:badge>
                    <flux:heading size="lg" class="mt-4">
                        @if ($subscription->remainingDays() !== null)
                            {{ trans_choice(':count día restante|:count días restantes', $subscription->remainingDays(), ['count' => $subscription->remainingDays()]) }}
                        @else
                            {{ __('Sin fecha de vencimiento') }}
                        @endif
                    </flux:heading>
                </div>
                <flux:text>{{ $subscription->starts_at->format('d/m/Y') }} — {{ $subscription->ends_at?->format('d/m/Y') ?? __('Sin vencimiento') }}</flux:text>
            </div>

            @if (! $subscription->allowsWrites())
                <flux:callout variant="warning" class="mt-6">
                    {{ __('La organización está en modo de solo lectura. Tus datos siguen disponibles para consulta.') }}
                </flux:callout>
            @endif
        </section>

        <flux:heading size="lg" class="mt-10">{{ __('Historial') }}</flux:heading>
        <div class="mt-4 overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            @foreach ($events as $event)
                <div class="border-b border-zinc-200 p-5 last:border-0 dark:border-zinc-800">
                    <div class="font-medium">{{ str($event->type)->replace('_', ' ')->headline() }}</div>
                    <div class="mt-1 text-sm text-zinc-500">{{ $event->occurred_at->format('d/m/Y H:i') }}</div>
                </div>
            @endforeach
        </div>
    @else
        <flux:callout variant="warning" class="mt-8">{{ __('No existe una membresía registrada.') }}</flux:callout>
    @endif
</div>
