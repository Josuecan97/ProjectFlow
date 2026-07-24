<?php

use App\Domain\Organizations\Models\Organization;
use App\Domain\Quotes\Actions\ExpireQuotes;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('quotes:expire', function (ExpireQuotes $expireQuotes) {
    Organization::query()
        ->select('id')
        ->chunkById(100, fn ($organizations) => $organizations->each(
            fn (Organization $organization) => $expireQuotes->handle($organization),
        ));

    $this->info('Expired quotations processed.');
})->purpose('Expire active quotations that are past their validity date');

Schedule::command('quotes:expire')->dailyAt('00:10')->withoutOverlapping();
