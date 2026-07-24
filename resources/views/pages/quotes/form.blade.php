<?php

use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Models\Person;
use App\Domain\Quotes\Actions\CreateCommercialQuoteVersion;
use App\Domain\Quotes\Actions\CreateDraftQuote;
use App\Domain\Quotes\Actions\UpdateDraftQuote;
use App\Domain\Quotes\Enums\QuoteStatus;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Support\QuoteDraftRules;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component
{
    public ?Quote $quote = null;
    public string $mode = 'create';
    public string $person_id = '';
    public string $title = '';
    public string $description = '';
    public string $scope = '';
    public string $terms = '';
    public string $notes = '';
    public string $issued_on = '';
    public string $expires_on = '';
    public string $currency = 'MXN';
    public string $client_name = '';
    public string $contact_name = '';
    public string $contact_email = '';
    public string $contact_phone = '';
    public array $client_address = ['country' => 'MX'];
    public array $items = [];

    public function mount(
        CurrentOrganization $currentOrganization,
        SubscriptionAccess $subscriptionAccess,
        ?Quote $quote = null,
    ): void {
        $organization = $currentOrganization->get();
        $subscriptionAccess->authorizeWrites($organization);
        $this->mode = request()->routeIs('quotes.revise')
            ? 'revise'
            : ($quote ? 'edit' : 'create');

        if ($quote === null) {
            Gate::authorize('create', [Quote::class, $organization]);
            $this->currency = $organization->currency;
            $this->issued_on = today()->toDateString();
            $this->expires_on = today()->addDays(14)->toDateString();
            $this->addItem();

            return;
        }

        abort_unless($quote->organization_id === $organization->id, 404);
        Gate::authorize('update', $quote);
        abort_if(
            $this->mode === 'edit' && $quote->status !== QuoteStatus::Draft,
            409,
            __('La cotización ya no es editable como Draft.'),
        );
        abort_if(
            $this->mode === 'revise' && $quote->status !== QuoteStatus::Approved,
            409,
            __('Solo una cotización aprobada puede revisarse.'),
        );

        $this->quote = $quote->load('currentVersion.items');
        $version = $quote->currentVersion;
        $this->person_id = (string) $quote->person_id;
        $this->title = $version->title;
        $this->description = $version->description ?? '';
        $this->scope = $version->scope ?? '';
        $this->terms = $version->terms ?? '';
        $this->notes = $version->notes ?? '';
        $this->issued_on = $version->issued_on->toDateString();
        $this->expires_on = $version->expires_on->toDateString();
        $this->currency = $version->currency;
        $this->client_name = $version->client_name;
        $this->contact_name = $version->contact_name ?? '';
        $this->contact_email = $version->contact_email ?? '';
        $this->contact_phone = $version->contact_phone ?? '';
        $this->client_address = $version->client_address ?? ['country' => 'MX'];
        $this->items = $version->items->map(fn ($item): array => [
            'name' => $item->name,
            'description' => $item->description ?? '',
            'quantity' => $item->quantity,
            'unit' => $item->unit,
            'unit_price' => $item->unit_price,
            'discount_amount' => $item->discount_amount,
            'tax_rate' => $item->tax_rate,
        ])->all();
    }

    public function selectClient(CurrentOrganization $currentOrganization): void
    {
        if ($this->person_id === '') {
            return;
        }

        $person = Person::query()
            ->forOrganization($currentOrganization->get())
            ->whereHas('roles', fn ($query) => $query->where('code', PersonRoleCode::Client->value))
            ->findOrFail((int) $this->person_id);

        $this->client_name = $person->display_name;
        $this->contact_email = $person->primary_email ?? '';
        $this->contact_phone = $person->primary_phone ?? '';
        $this->client_address = $person->address ?? ['country' => 'MX'];
    }

    public function addItem(): void
    {
        $this->items[] = [
            'name' => '',
            'description' => '',
            'quantity' => '1.0000',
            'unit' => 'servicio',
            'unit_price' => '0.000000',
            'discount_amount' => '0.000000',
            'tax_rate' => '16.0000',
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function save(
        CurrentOrganization $currentOrganization,
        CreateDraftQuote $createDraft,
        UpdateDraftQuote $updateDraft,
        CreateCommercialQuoteVersion $createRevision,
    ): void {
        $organization = $currentOrganization->get();
        $validated = $this->validate(QuoteDraftRules::forOrganization($organization->id));

        $quote = match ($this->mode) {
            'edit' => $updateDraft->handle(auth()->user(), $this->quote, $validated),
            'revise' => $createRevision->handle(auth()->user(), $this->quote, $validated),
            default => $createDraft->handle(auth()->user(), $organization, $validated),
        };

        session()->flash('status', match ($this->mode) {
            'edit' => __('Cotización actualizada.'),
            'revise' => __('Nueva versión comercial creada.'),
            default => __('Cotización creada.'),
        });
        $this->redirect(route('quotes.show', $quote, absolute: false), navigate: true);
    }

    public function with(CurrentOrganization $currentOrganization): array
    {
        return [
            'clients' => Person::query()
                ->forOrganization($currentOrganization->get())
                ->whereHas('roles', fn ($query) => $query->where('code', PersonRoleCode::Client->value))
                ->orderBy('display_name')
                ->get(['id', 'display_name']),
        ];
    }
}; ?>

<div class="max-w-5xl">
    <flux:heading size="xl">
        {{ match ($mode) {
            'edit' => __('Editar Cotización'),
            'revise' => __('Nueva versión comercial'),
            default => __('Nueva Cotización'),
        } }}
    </flux:heading>
    <flux:text class="mt-2">
        {{ $mode === 'revise'
            ? __('La versión aprobada permanecerá intacta; los cambios iniciarán un nuevo Draft.')
            : __('Los totales se calculan y validan en el servidor.') }}
    </flux:text>

    <form wire:submit="save" class="mt-8 space-y-8">
        <section class="grid gap-5 rounded-xl border border-zinc-200 bg-white p-6 md:grid-cols-2 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:select wire:model="person_id" wire:change="selectClient" :label="__('Cliente')" required :disabled="$mode === 'revise'">
                <flux:select.option value="">{{ __('Selecciona un Cliente') }}</flux:select.option>
                @foreach ($clients as $client)
                    <flux:select.option value="{{ $client->id }}">{{ $client->display_name }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input wire:model="client_name" :label="__('Nombre para presentación')" required />
            <div class="md:col-span-2">
                <flux:input wire:model="title" :label="__('Título')" required />
            </div>
            <flux:textarea wire:model="description" :label="__('Descripción')" rows="3" />
            <flux:textarea wire:model="scope" :label="__('Alcance')" rows="3" />
            <flux:textarea wire:model="terms" :label="__('Condiciones comerciales')" rows="3" />
            <flux:textarea wire:model="notes" :label="__('Observaciones')" rows="3" />
            <flux:input wire:model="issued_on" type="date" :label="__('Fecha de emisión')" required />
            <flux:input wire:model="expires_on" type="date" :label="__('Fecha de vencimiento')" required />
            <flux:input wire:model="currency" :label="__('Moneda')" maxlength="3" required />
        </section>

        <section class="grid gap-5 rounded-xl border border-zinc-200 bg-white p-6 md:grid-cols-2 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="md:col-span-2"><flux:heading size="lg">{{ __('Contacto y dirección') }}</flux:heading></div>
            <flux:input wire:model="contact_name" :label="__('Contacto')" />
            <flux:input wire:model="contact_email" type="email" :label="__('Correo')" />
            <flux:input wire:model="contact_phone" :label="__('Teléfono')" />
            <flux:input wire:model="client_address.street" :label="__('Calle')" />
            <flux:input wire:model="client_address.city" :label="__('Ciudad')" />
            <flux:input wire:model="client_address.state" :label="__('Estado')" />
            <flux:input wire:model="client_address.postal_code" :label="__('Código postal')" />
            <flux:input wire:model="client_address.country" :label="__('País')" maxlength="2" />
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex items-center justify-between gap-4">
                <flux:heading size="lg">{{ __('Conceptos') }}</flux:heading>
                <flux:button type="button" wire:click="addItem" size="sm">{{ __('Agregar concepto') }}</flux:button>
            </div>
            <div class="mt-5 space-y-5">
                @foreach ($items as $index => $item)
                    <div wire:key="quote-item-{{ $index }}" class="grid gap-4 rounded-lg bg-zinc-50 p-4 md:grid-cols-6 dark:bg-zinc-950">
                        <div class="md:col-span-3"><flux:input wire:model="items.{{ $index }}.name" :label="__('Concepto')" required /></div>
                        <flux:input wire:model="items.{{ $index }}.quantity" :label="__('Cantidad')" inputmode="decimal" required />
                        <flux:input wire:model="items.{{ $index }}.unit" :label="__('Unidad')" required />
                        <flux:input wire:model="items.{{ $index }}.unit_price" :label="__('Precio')" inputmode="decimal" required />
                        <div class="md:col-span-3"><flux:textarea wire:model="items.{{ $index }}.description" :label="__('Descripción')" rows="2" /></div>
                        <flux:input wire:model="items.{{ $index }}.discount_amount" :label="__('Descuento')" inputmode="decimal" />
                        <flux:input wire:model="items.{{ $index }}.tax_rate" :label="__('Impuesto %')" inputmode="decimal" />
                        <div class="flex items-end justify-end">
                            <flux:button type="button" wire:click="removeItem({{ $index }})" size="sm" variant="danger">{{ __('Retirar') }}</flux:button>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="flex gap-3">
            <flux:button type="submit" variant="primary">{{ __('Guardar') }}</flux:button>
            <flux:button href="{{ $quote ? route('quotes.show', $quote) : route('quotes.index') }}" variant="ghost">{{ __('Cancelar') }}</flux:button>
        </div>
    </form>
</div>
