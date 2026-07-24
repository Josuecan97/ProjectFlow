<?php

namespace App\Http\Middleware;

use App\Domain\Organizations\Enums\OrganizationMemberStatus;
use App\Domain\Organizations\Models\OrganizationMember;
use App\Domain\Organizations\Support\CurrentOrganization;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureOrganizationSelected
{
    public function __construct(private readonly CurrentOrganization $currentOrganization) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $memberships = OrganizationMember::query()
            ->where('user_id', $request->user()->id)
            ->where('status', OrganizationMemberStatus::Active)
            ->with('organization')
            ->get();

        if ($memberships->isEmpty()) {
            return redirect()->route('organizations.create');
        }

        $selectedId = $request->session()->get('organization_id');
        $member = $selectedId === null
            ? null
            : $memberships->firstWhere('organization_id', (int) $selectedId);

        if ($member === null && $memberships->count() === 1) {
            $member = $memberships->first();
            $request->session()->put('organization_id', $member->organization_id);
        }

        if ($member === null) {
            $request->session()->forget('organization_id');

            return redirect()->route('organizations.select');
        }

        $this->currentOrganization->set($member->organization);

        return $next($request);
    }
}
