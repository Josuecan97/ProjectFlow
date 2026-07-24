<?php

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\Quotes\Actions\ApproveQuote;
use App\Domain\Quotes\Actions\ArchiveQuote;
use App\Domain\Quotes\Actions\CorrectApprovedQuoteVersion;
use App\Domain\Quotes\Actions\RejectQuote;
use App\Domain\Quotes\Actions\SendQuote;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Services\QuoteCalculator;
use App\Domain\Quotes\Support\AdministrativeCorrectionRules;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public Quote $quote;
    public string $client_name = '';
    public string $contact_name = '';
    public string $contact_email = '';
    public string $contact_phone = '';
    public array $client_address = [];
    public string $notes = '';

    public function mount(Quote $quote, CurrentOrganization $currentOrganization): void
    {
        abort_unless($quote->organization_id === $currentOrganization->id(), 404);
        Gate::authorize('view', $quote);
        $this->quote = $quote;
        $this->reloadQuote();
        $this->loadAdministrativeFields();
    }

    public function send(SendQuote $action): void
    {
        $this->quote = $action->handle(auth()->user(), $this->quote);
        $this->afterAction(__('Cotización marcada como enviada.'));
    }

    public function approve(ApproveQuote $action): void
    {
        $this->quote = $action->handle(auth()->user(), $this->quote);
        $this->afterAction(__('Cotización aprobada.'));
    }

    public function reject(RejectQuote $action): void
    {
        $this->quote = $action->handle(auth()->user(), $this->quote);
        $this->afterAction(__('Cotización rechazada.'));
    }

    public function archive(ArchiveQuote $action): void
    {
        $this->quote = $action->handle(auth()->user(), $this->quote);
        $this->afterAction(__('Cotización archivada.'));
    }

    public function correct(CorrectApprovedQuoteVersion $action): void
    {
        $validated = $this->validate(AdministrativeCorrectionRules::rules());
        $action->handle(auth()->user(), $this->quote->approvedVersion, $validated);
        $this->reloadQuote();
        $this->loadAdministrativeFields();
        session()->flash('status', __('Corrección administrativa registrada.'));
    }

    public function with(
        SubscriptionAccess $subscriptionAccess,
        QuoteCalculator $calculator,
    ): array {
        $canWrite = $subscriptionAccess->allowsWrites($this->quote->organization);

        return [
            'calculator' => $calculator,
            'canUpdate' => $canWrite && Gate::allows('update', $this->quote),
            'canApprove' => $canWrite && Gate::allows('approve', $this->quote),
            'canArchive' => $canWrite && Gate::allows('archive', $this->quote),
        ];
    }

    private function afterAction(string $message): void
    {
        $this->reloadQuote();
        $this->loadAdministrativeFields();
        session()->flash('status', $message);
    }

    private function reloadQuote(): void
    {
        $this->quote->refresh()->load([
            'person',
            'currentVersion.items',
            'approvedVersion.revisions.changedBy.user',
            'versions.items',
            'versions.createdBy.user',
        ]);
    }

    private function loadAdministrativeFields(): void
    {
        $version = $this->quote->approvedVersion;

        if ($version === null) {
            return;
        }

        $this->client_name = $version->client_name;
        $this->contact_name = $version->contact_name ?? '';
        $this->contact_email = $version->contact_email ?? '';
        $this->contact_phone = $version->contact_phone ?? '';
        $this->client_address = $version->client_address ?? [];
        $this->notes = $version->notes ?? '';
    }
}; ?>

<div>
    @if (session('status'))
        <flux:callout variant="success" class="mb-6">{{ session('status') }}</flux:callout>
    @endif

    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <flux:heading size="xl">{{ $quote->number }}</flux:heading>
                <flux:badge>{{ $quote->status->label() }}</flux:badge>
                <flux:badge color="indigo">{{ __('Versión') }} {{ $quote->currentVersion->version_number }}</flux:badge>
            </div>
            <flux:text class="mt-2">{{ $quote->currentVersion->title }} · {{ $quote->person->display_name }}</flux:text>
        </div>
        <div class="flex flex-wrap gap-2">
            @if ($canUpdate && $quote->status === QuoteStatus::Draft)
                <flux:button href="{{ route('quotes.edit', $quote) }}">{{ __('Editar Draft') }}</flux:button>
                <flux:button wire:click="send" variant="primary">{{ __('Marcar enviada') }}</flux:button>
            @endif
            @if ($canApprove && $quote->status === QuoteStatus::Sent)
                <flux:button wire:click="approve" wire:confirm="{{ __('¿Aprobar esta versión comercial?') }}" variant="primary">{{ __('Aprobar') }}</flux:button>
                <flux:button wire:click="reject" wire:confirm="{{ __('¿Rechazar esta cotización?') }}" variant="danger">{{ __('Rechazar') }}</flux:button>
            @endif
            @if ($canUpdate && $quote->status === QuoteStatus::Approved)
                <flux:button href="{{ route('quotes.revise', $quote) }}" variant="primary">{{ __('Nueva versión') }}</flux:button>
            @endif
            @if ($canArchive && $quote->status !== QuoteStatus::Archived)
                <flux:button wire:click="archive" wire:confirm="{{ __('¿Archivar esta cotización?') }}">{{ __('Archivar') }}</flux:button>
            @endif
            <flux:button href="{{ route('quotes.index') }}" variant="ghost">{{ __('Volver') }}</flux:button>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <section class="rounded-xl border border-zinc-200 bg-white p-6 lg:col-span-2 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:heading size="lg">{{ __('Acuerdo comercial') }}</flux:heading>
            <dl class="mt-5 grid gap-4 sm:grid-cols-3">
                <div><dt class="text-sm text-zinc-500">{{ __('Emisión') }}</dt><dd>{{ $quote->currentVersion->issued_on->format('d/m/Y') }}</dd></div>
                <div><dt class="text-sm text-zinc-500">{{ __('Vencimiento') }}</dt><dd>{{ $quote->currentVersion->expires_on->format('d/m/Y') }}</dd></div>
                <div><dt class="text-sm text-zinc-500">{{ __('Moneda') }}</dt><dd>{{ $quote->currentVersion->currency }}</dd></div>
            </dl>
            @foreach ([__('Descripción') => $quote->currentVersion->description, __('Alcance') => $quote->currentVersion->scope, __('Condiciones') => $quote->currentVersion->terms] as $label => $value)
                @if ($value)
                    <div class="mt-5"><div class="text-sm text-zinc-500">{{ $label }}</div><p class="mt-1 whitespace-pre-line">{{ $value }}</p></div>
                @endif
            @endforeach
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:heading size="lg">{{ __('Cliente') }}</flux:heading>
            <div class="mt-4 font-medium">{{ $quote->currentVersion->client_name }}</div>
            @if ($quote->currentVersion->contact_name)<div class="mt-2 text-sm">{{ $quote->currentVersion->contact_name }}</div>@endif
            @if ($quote->currentVersion->contact_email)<div class="text-sm text-zinc-500">{{ $quote->currentVersion->contact_email }}</div>@endif
            @if ($quote->currentVersion->contact_phone)<div class="text-sm text-zinc-500">{{ $quote->currentVersion->contact_phone }}</div>@endif
        </section>
    </div>

    <section class="mt-6 overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
        <div class="p-6"><flux:heading size="lg">{{ __('Conceptos') }}</flux:heading></div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 text-left text-zinc-500 dark:bg-zinc-950">
                    <tr><th class="px-6 py-3">{{ __('Concepto') }}</th><th class="px-4 py-3 text-right">{{ __('Cantidad') }}</th><th class="px-4 py-3 text-right">{{ __('Precio') }}</th><th class="px-4 py-3 text-right">{{ __('Descuento') }}</th><th class="px-4 py-3 text-right">{{ __('Impuesto') }}</th><th class="px-6 py-3 text-right">{{ __('Total') }}</th></tr>
                </thead>
                <tbody>
                    @foreach ($quote->currentVersion->items as $item)
                        <tr class="border-t border-zinc-200 dark:border-zinc-800">
                            <td class="px-6 py-4"><div class="font-medium">{{ $item->name }}</div><div class="text-zinc-500">{{ $item->description }}</div></td>
                            <td class="px-4 py-4 text-right">{{ $item->quantity }} {{ $item->unit }}</td>
                            <td class="px-4 py-4 text-right">{{ $calculator->display($item->unit_price) }}</td>
                            <td class="px-4 py-4 text-right">{{ $calculator->display($item->discount_amount) }}</td>
                            <td class="px-4 py-4 text-right">{{ $calculator->display($item->tax_amount) }}</td>
                            <td class="px-6 py-4 text-right font-medium">{{ $calculator->display($item->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="ml-auto grid max-w-sm gap-2 border-t border-zinc-200 p-6 text-sm dark:border-zinc-800">
            <div class="flex justify-between"><span>{{ __('Subtotal') }}</span><span>{{ $calculator->display($quote->currentVersion->subtotal) }}</span></div>
            <div class="flex justify-between"><span>{{ __('Descuento') }}</span><span>{{ $calculator->display($quote->currentVersion->discount_total) }}</span></div>
            <div class="flex justify-between"><span>{{ __('Impuestos') }}</span><span>{{ $calculator->display($quote->currentVersion->tax_total) }}</span></div>
            <div class="flex justify-between text-lg font-semibold"><span>{{ __('Total') }}</span><span>{{ $quote->currentVersion->currency }} {{ $calculator->display($quote->currentVersion->total) }}</span></div>
        </div>
    </section>

    <section class="mt-6 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
        <flux:heading size="lg">{{ __('Historial de versiones') }}</flux:heading>
        <div class="mt-4 space-y-3">
            @foreach ($quote->versions->sortByDesc('version_number') as $version)
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800">
                    <div><span class="font-medium">{{ __('Versión') }} {{ $version->version_number }}</span> <flux:badge size="sm">{{ $version->status->label() }}</flux:badge><div class="text-sm text-zinc-500">{{ $version->title }}</div></div>
                    <div class="text-right"><div>{{ $version->currency }} {{ $calculator->display($version->total) }}</div><div class="text-sm text-zinc-500">{{ $version->created_at->format('d/m/Y H:i') }}</div></div>
                </div>
            @endforeach
        </div>
    </section>

    @if ($quote->approvedVersion && $canUpdate)
        <section class="mt-6 rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:heading size="lg">{{ __('Corrección administrativa') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Solo datos de presentación; cualquier cambio comercial requiere una nueva versión.') }}</flux:text>
            <form wire:submit="correct" class="mt-5 grid gap-4 md:grid-cols-2">
                <flux:input wire:model="client_name" :label="__('Nombre del cliente')" />
                <flux:input wire:model="contact_name" :label="__('Contacto')" />
                <flux:input wire:model="contact_email" type="email" :label="__('Correo')" />
                <flux:input wire:model="contact_phone" :label="__('Teléfono')" />
                <flux:textarea wire:model="notes" :label="__('Observaciones')" rows="2" />
                <div class="flex items-end justify-end"><flux:button type="submit">{{ __('Registrar corrección') }}</flux:button></div>
            </form>
            @if ($quote->approvedVersion->revisions->isNotEmpty())
                <div class="mt-6 border-t border-zinc-200 pt-5 dark:border-zinc-800">
                    <flux:heading>{{ __('Auditoría administrativa') }}</flux:heading>
                    @foreach ($quote->approvedVersion->revisions as $revision)
                        <div class="mt-3 rounded-lg bg-zinc-50 p-4 text-sm dark:bg-zinc-950">
                            {{ $revision->created_at->format('d/m/Y H:i') }}
                            · {{ $revision->changedBy?->user?->name ?? __('Miembro eliminado') }}
                            · {{ implode(', ', array_keys($revision->after_values)) }}
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</div>
