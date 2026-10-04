<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ConstructionStatus;
use App\Enums\ConstructionType;
use App\Models\ConstructionProject;
use App\Models\Household;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConstructionProject>
 */
final class ConstructionProjectFactory extends Factory
{
    protected $model = ConstructionProject::class;

    public function definition(): array
    {
        return [
            'household_id' => Household::factory(),
            'project_type' => fake()->randomElement([
                ConstructionType::Kitchen,
                ConstructionType::BuildingAddition,
                ConstructionType::Expansion,
                ConstructionType::Renovation,
            ]),
            'description' => fake()->sentence(),
            'start_date' => now()->format('Y-m-d'),
            'completion_date' => null,
            'status' => ConstructionStatus::Active,
            'one_time_fee' => 100000,
            'fee_invoice_id' => null,
            'created_by' => null,
            'notes' => null,
        ];
    }

    public function planned(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ConstructionStatus::Planned,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ConstructionStatus::Active,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ConstructionStatus::Completed,
            'completion_date' => now()->format('Y-m-d'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ConstructionStatus::Cancelled,
        ]);
    }
}
