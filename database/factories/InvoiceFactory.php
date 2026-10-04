<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Household;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
final class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $amount = 50000;

        return [
            'invoice_number' => 'INV-'.date('Y').'-'.sprintf('%06d', fake()->unique()->numberBetween(1, 999999)),
            'household_id' => Household::factory(),
            'invoice_type' => InvoiceType::Monthly,
            'billing_period' => now()->format('Y-m'),
            'issue_date' => now()->startOfMonth(),
            'due_date' => now()->day(10),
            'subtotal' => $amount,
            'total_amount' => $amount,
            'amount_paid' => 0,
            'balance' => $amount,
            'status' => InvoiceStatus::Unpaid,
            'construction_project_id' => null,
            'notes' => null,
        ];
    }

    public function monthly(): static
    {
        return $this->state(fn (array $attributes): array => [
            'invoice_type' => InvoiceType::Monthly,
        ]);
    }

    public function construction(): static
    {
        return $this->state(fn (array $attributes): array => [
            'invoice_type' => InvoiceType::Construction,
            'subtotal' => 100000,
            'total_amount' => 100000,
            'balance' => 100000,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes): array => [
            'amount_paid' => $attributes['total_amount'] ?? 50000,
            'balance' => 0,
            'status' => InvoiceStatus::Paid,
        ]);
    }
}
