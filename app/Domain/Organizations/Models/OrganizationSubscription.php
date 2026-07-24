<?php

namespace App\Domain\Organizations\Models;

use App\Domain\Organizations\Enums\SubscriptionSource;
use App\Domain\Organizations\Enums\SubscriptionStatus;
use App\Models\User;
use Carbon\CarbonInterface;
use Database\Factories\OrganizationSubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property SubscriptionStatus $status
 * @property CarbonInterface|null $ends_at
 * @property-read Organization $organization
 */
#[Fillable([
    'organization_id',
    'status',
    'source',
    'starts_at',
    'ends_at',
    'auto_renew',
    'granted_by_user_id',
    'provider',
    'provider_customer_id',
    'provider_subscription_id',
    'last_payment_at',
    'notes',
])]
final class OrganizationSubscription extends Model
{
    /** @use HasFactory<OrganizationSubscriptionFactory> */
    use HasFactory;

    protected static function newFactory(): OrganizationSubscriptionFactory
    {
        return OrganizationSubscriptionFactory::new();
    }

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'source' => SubscriptionSource::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'auto_renew' => 'boolean',
            'last_payment_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsTo<User, $this> */
    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by_user_id');
    }

    /** @return HasMany<OrganizationSubscriptionEvent, $this> */
    public function events(): HasMany
    {
        return $this->hasMany(OrganizationSubscriptionEvent::class);
    }

    public function isCurrent(): bool
    {
        return in_array($this->status, [
            SubscriptionStatus::Trial,
            SubscriptionStatus::Active,
        ], true) && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function allowsWrites(): bool
    {
        return $this->isCurrent();
    }

    public function remainingDays(): ?int
    {
        if ($this->ends_at === null) {
            return null;
        }

        return max(0, (int) now()->startOfDay()->diffInDays($this->ends_at->startOfDay(), false));
    }
}
