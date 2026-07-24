<?php

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Queries\QuoteIndexQuery;
use App\Domain\Quotes\Services\QuoteCalculator;
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
    public string $status = '';

    public function mount(CurrentOrganization $currentOrganization): void
    {
        Gate::authorize('viewAny', [Quote::class, $currentOrganization->get()]);
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function with(
        CurrentOrganization $currentOrganization,
        SubscriptionAccess $subscriptionAccess,
        QuoteIndexQuery $query,
        QuoteCalculator $calculator,
    ): array {
        $organization = $currentOrganization->get();

        return [
            'quotes' => $query->paginate(
                $organization,
                $this->search,
                QuoteStatus::tryFrom($this->status),
            ),
            'statuses' => QuoteStatus::cases(),
            'calculator' => $calculator,
            'canCreate' => $subscriptionAccess->allowsWrites($organization)
                && Gate::allows('create', [Quote::class, $organization]),
        ];
    }
}; ?>

<div>
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Cotizaciones') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Propuestas comerciales versionadas y trazables.') }}</flux:text>
        </div>
        @if ($canCreate)
            <flux:button href="{{ route('quotes.create') }}" variant="primary">{{ __('Nueva Cotización') }}</flux:button>
        @endif
    </div>

    @if (session('status'))
        <flux:callout variant="success" class="mt-6">{{ session('status') }}</flux:callout>
    @endif

    <div class="mt-8 grid gap-4 rounded-xl border border-zinc-200 bg-white p-5 md:grid-cols-2 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:input wire:model.live.debounce.300ms="search" :label="__('Buscar')" placeholder="{{ __('Folio, cliente o título') }}" />
        <flux:select wire:model.live="status" :label="__('Estado')">
            <flux:select.option value="">{{ __('Todos') }}</flux:select.option>
            @foreach ($statuses as $statusOption)
                <flux:select.option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="mt-6 overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        @forelse ($quotes as $quote)
            <a href="{{ route('quotes.show', $quote) }}" class="grid gap-3 border-b border-zinc-200 p-5 transition hover:bg-zinc-50 md:grid-cols-5 md:items-center last:border-0 dark:border-zinc-800 dark:hover:bg-zinc-800/50">
                <div>
                    <div class="font-semibold text-indigo-700 dark:text-indigo-300">{{ $quote->number }}</div>
                    <div class="text-sm text-zinc-500">{{ __('Versión') }} {{ $quote->currentVersion->version_number }}</div>
                </div>
                <div class="md:col-span-2">
                    <div class="font-medium">{{ $quote->currentVersion->title }}</div>
                    <div class="text-sm text-zinc-500">{{ $quote->person->display_name }}</div>
                </div>
                <div>
                    <flux:badge>{{ $quote->status->label() }}</flux:badge>
                    <div class="mt-1 text-sm text-zinc-500">{{ __('Vence') }} {{ $quote->currentVersion->expires_on->format('d/m/Y') }}</div>
                </div>
                <div class="text-right font-semibold">
                    {{ $quote->currentVersion->currency }} {{ $calculator->display($quote->currentVersion->total) }}
                </div>
            </a>
        @empty
            <div class="p-10 text-center">
                <flux:heading>{{ __('No hay Cotizaciones') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Crea la primera propuesta comercial de la Organización.') }}</flux:text>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $quotes->links() }}</div>
</div>
