<?php

namespace Database\Factories;

use App\Domain\Organizations\Models\Organization;
use App\Domain\People\Enums\PersonStatus;
use App\Domain\People\Enums\PersonType;
use App\Domain\People\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Person>
 */
final class PersonFactory extends Factory
{
    protected $model = Person::class;

    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'organization_id' => Organization::factory(),
            'type' => PersonType::Individual,
            'display_name' => "{$firstName} {$lastName}",
            'first_name' => $firstName,
            'last_name' => $lastName,
            'primary_email' => fake()->unique()->safeEmail(),
            'primary_phone' => fake()->numerify('999#######'),
            'status' => PersonStatus::Active,
        ];
    }

    public function organization(): static
    {
        return $this->state(function (): array {
            $name = fake()->company();

            return [
                'type' => PersonType::Organization,
                'display_name' => $name,
                'legal_name' => $name,
                'first_name' => null,
                'last_name' => null,
            ];
        });
    }

    public function archived(): static
    {
        return $this->state([
            'status' => PersonStatus::Archived,
            'deleted_at' => now(),
        ]);
    }
}
