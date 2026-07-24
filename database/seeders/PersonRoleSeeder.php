<?php

namespace Database\Seeders;

use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Models\PersonRole;
use Illuminate\Database\Seeder;

final class PersonRoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PersonRoleCode::cases() as $role) {
            PersonRole::query()->updateOrCreate(
                ['code' => $role->value],
                ['name' => $role->label(), 'is_system' => true],
            );
        }
    }
}
