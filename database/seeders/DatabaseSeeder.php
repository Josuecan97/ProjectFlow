<?php

namespace Database\Seeders;

use App\Domain\Organizations\Actions\CreateOrganization;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AccessControlSeeder::class);
        $this->call(PersonRoleSeeder::class);

        $user = User::query()->updateOrCreate([
            'email' => 'demo@projectflow.test',
        ], [
            'name' => 'Usuario Demo',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);

        if ($user->organizationMembers()->doesntExist()) {
            app(CreateOrganization::class)->handle($user, [
                'name' => 'Organización Demo',
            ]);
        }

        $this->call(PeopleDemoSeeder::class);
    }
}
