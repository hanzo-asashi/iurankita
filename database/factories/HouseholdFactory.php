<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OccupancyStatus;
use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Household>
 */
final class HouseholdFactory extends Factory
{
    protected $model = Household::class;

    public function definition(): array
    {
        $block = fake()->randomElement(['A', 'B', 'C', 'D']);
        $number = sprintf('%02d', fake()->numberBetween(1, 30));

        return [
            'house_code' => "{$block}-{$number}",
            'block' => $block,
            'house_number' => $number,
            'head_of_family' => fake()->name(),
            'kk_number' => fake()->numerify('################'),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'occupancy_status' => fake()->randomElement([OccupancyStatus::Occupied, OccupancyStatus::Unoccupied]),
            'notes' => null,
            'is_active' => true,
        ];
    }

    public function occupied(): static
    {
        return $this->state(fn (array $attributes): array => [
            'occupancy_status' => OccupancyStatus::Occupied,
        ]);
    }

    public function unoccupied(): static
    {
        return $this->state(fn (array $attributes): array => [
            'occupancy_status' => OccupancyStatus::Unoccupied,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
