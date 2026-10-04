<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
final class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'receipt_number' => 'KWT-'.date('Ym').'-'.sprintf('%05d', fake()->unique()->numberBetween(1, 99999)),
            'payment_date' => now(),
            'amount' => 50000,
            'payment_method' => PaymentMethod::Cash,
            'reference_number' => null,
            'proof_path' => null,
            'notes' => null,
            'received_by' => null,
        ];
    }
}
