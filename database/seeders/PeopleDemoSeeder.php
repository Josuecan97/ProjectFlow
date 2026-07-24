<?php

namespace Database\Seeders;

use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Enums\PersonRoleCode;
use App\Domain\People\Models\Person;
use App\Domain\People\Models\PersonRelationship;
use App\Domain\People\Models\PersonRole;
use Illuminate\Database\Seeder;

final class PeopleDemoSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::query()->where('name', 'Organización Demo')->first();

        if ($organization === null) {
            return;
        }

        $company = Person::query()->updateOrCreate([
            'organization_id' => $organization->id,
            'tax_id' => 'COD240101ABC',
        ], [
            'type' => 'organization',
            'display_name' => 'Constructora Horizonte',
            'legal_name' => 'Constructora Horizonte, S.A. de C.V.',
            'primary_email' => 'contacto@horizonte.test',
            'primary_phone' => '9991002000',
            'status' => 'active',
        ]);
        $company->roles()->sync(PersonRole::query()
            ->whereIn('code', [PersonRoleCode::Client->value, PersonRoleCode::Partner->value])
            ->pluck('id'));

        $contact = Person::query()->updateOrCreate([
            'organization_id' => $organization->id,
            'primary_email' => 'ana@horizonte.test',
        ], [
            'type' => 'individual',
            'display_name' => 'Ana Martínez',
            'first_name' => 'Ana',
            'last_name' => 'Martínez',
            'primary_phone' => '9991002001',
            'status' => 'active',
        ]);
        $contact->roles()->sync(PersonRole::query()
            ->where('code', PersonRoleCode::Contact->value)
            ->pluck('id'));

        $supplier = Person::query()->updateOrCreate([
            'organization_id' => $organization->id,
            'tax_id' => 'SUM240101XYZ',
        ], [
            'type' => 'organization',
            'display_name' => 'Suministros del Mayab',
            'legal_name' => 'Suministros del Mayab, S.A.',
            'primary_email' => 'ventas@suministros.test',
            'status' => 'active',
        ]);
        $supplier->roles()->sync(PersonRole::query()
            ->where('code', PersonRoleCode::Supplier->value)
            ->pluck('id'));

        PersonRelationship::query()->updateOrCreate([
            'organization_id' => $organization->id,
            'parent_person_id' => $company->id,
            'related_person_id' => $contact->id,
            'type' => 'contact',
        ], [
            'job_title' => 'Directora de operaciones',
            'is_primary' => true,
        ]);
    }
}
