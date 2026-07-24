<?php

use App\Domain\Organizations\Actions\AcceptOrganizationInvitation;
use App\Domain\Organizations\Enums\OrganizationInvitationStatus;
use App\Domain\Organizations\Models\OrganizationInvitation;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component
{
    public string $token;

    public OrganizationInvitation $invitation;

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->invitation = OrganizationInvitation::query()
            ->with(['organization', 'role'])
            ->where('token_hash', hash('sha256', $token))
            ->firstOrFail();
    }

    public function accept(AcceptOrganizationInvitation $acceptInvitation): void
    {
        abort_unless(auth()->check(), 403);

        $member = $acceptInvitation->handle(auth()->user(), $this->token);
        session(['organization_id' => $member->organization_id]);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    public function isAvailable(): bool
    {
        return $this->invitation->status === OrganizationInvitationStatus::Pending
            && $this->invitation->expires_at->isFuture();
    }
}; ?>

<div>
    <flux:heading size="xl">{{ __('Invitación a ProjectFlow') }}</flux:heading>
    <flux:text class="mt-3">
        {{ __('Te invitaron a colaborar en :organization con el rol :role.', [
            'organization' => $invitation->organization->name,
            'role' => $invitation->role->name,
        ]) }}
    </flux:text>

    @if (! $this->isAvailable())
        <flux:callout variant="warning" class="mt-6">
            {{ __('Esta invitación expiró o ya fue utilizada.') }}
        </flux:callout>
    @elseif (auth()->check())
        <flux:button wire:click="accept" variant="primary" class="mt-8">
            {{ __('Aceptar invitación') }}
        </flux:button>
    @else
        <div class="mt-8 flex gap-3">
            <flux:button href="{{ route('login', ['invitation' => $token]) }}" variant="primary">
                {{ __('Iniciar sesión') }}
            </flux:button>
            <flux:button href="{{ route('register', ['invitation' => $token]) }}">
                {{ __('Crear cuenta') }}
            </flux:button>
        </div>
    @endif
</div>
