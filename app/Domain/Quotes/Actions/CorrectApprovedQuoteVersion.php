<?php

declare(strict_types=1);

namespace App\Domain\Quotes\Actions;

use App\Domain\Organizations\Support\SubscriptionAccess;
use App\Domain\Quotes\Enums\QuoteVersionRevisionType;
use App\Domain\Quotes\Enums\QuoteVersionStatus;
use App\Domain\Quotes\Models\QuoteVersion;
use App\Domain\Quotes\Services\QuoteMemberResolver;
use App\Domain\Quotes\Support\AdministrativeCorrectionRules;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class CorrectApprovedQuoteVersion
{
    public function __construct(
        private readonly QuoteMemberResolver $memberResolver,
        private readonly SubscriptionAccess $subscriptionAccess,
    ) {}

    /**
     * @param  array<string, mixed>  $changes
     */
    public function handle(User $actor, QuoteVersion $version, array $changes): QuoteVersion
    {
        $quote = $version->quote;
        Gate::forUser($actor)->authorize('update', $quote);
        $this->subscriptionAccess->authorizeWrites($version->organization);
        $member = $this->memberResolver->resolve($actor, $version->organization);

        $unsupported = array_diff(array_keys($changes), AdministrativeCorrectionRules::fields());

        if ($unsupported !== []) {
            throw ValidationException::withMessages([
                'changes' => __('La corrección contiene campos comerciales que requieren una nueva versión.'),
            ]);
        }

        $validated = Validator::make(
            $changes,
            AdministrativeCorrectionRules::rules(),
        )->validate();

        return DB::transaction(function () use ($version, $quote, $validated, $member): QuoteVersion {
            $lockedVersion = QuoteVersion::query()->lockForUpdate()->findOrFail($version->id);
            $quote->refresh();

            if (
                $lockedVersion->status !== QuoteVersionStatus::Approved
                || $quote->approved_version_id !== $lockedVersion->id
            ) {
                throw ValidationException::withMessages([
                    'version' => __('Solo la versión aprobada vigente admite correcciones administrativas.'),
                ]);
            }

            $before = collect($validated)
                ->mapWithKeys(fn (mixed $value, string $field): array => [
                    $field => $lockedVersion->getAttribute($field),
                ])
                ->all();

            $lockedVersion->fill($validated);
            $dirty = $lockedVersion->getDirty();

            if ($dirty === []) {
                return $lockedVersion;
            }

            $changedBefore = array_intersect_key($before, $dirty);
            $lockedVersion->save();
            $after = collect(array_keys($dirty))
                ->mapWithKeys(fn (string $field): array => [
                    $field => $lockedVersion->getAttribute($field),
                ])
                ->all();
            $lockedVersion->revisions()->create([
                'organization_id' => $lockedVersion->organization_id,
                'changed_by_organization_member_id' => $member->id,
                'type' => QuoteVersionRevisionType::AdministrativeCorrection,
                'before_values' => $changedBefore,
                'after_values' => $after,
            ]);

            return $lockedVersion->refresh()->load('revisions');
        });
    }
}
