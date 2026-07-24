<?php

namespace App\Providers;

use App\Domain\Organizations\Models\Organization;
use App\Domain\Organizations\Policies\OrganizationPolicy;
use App\Domain\Organizations\Support\CurrentOrganization;
use App\Domain\People\Models\Person;
use App\Domain\People\Policies\PersonPolicy;
use App\Domain\Quotes\Models\Quote;
use App\Domain\Quotes\Policies\QuotePolicy;
use App\Http\Middleware\EnsureOrganizationSelected;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(CurrentOrganization::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Organization::class, OrganizationPolicy::class);
        Gate::policy(Person::class, PersonPolicy::class);
        Gate::policy(Quote::class, QuotePolicy::class);
        Livewire::addPersistentMiddleware(EnsureOrganizationSelected::class);
    }
}
