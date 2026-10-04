<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Enums\OccupancyStatus;
use App\Enums\PaymentMethod;
use App\Models\FeeRate;
use App\Models\Household;
use App\Services\Billing\AdvanceBillingService;

beforeEach(function () {
    FeeRate::firstOrCreate(
        ['code' => 'monthly_occupied'],
        ['name' => 'Dihuni', 'amount' => 50000, 'effective_from' => '2026-01-01', 'is_active' => true]
    );

    FeeRate::firstOrCreate(
        ['code' => 'monthly_unoccupied'],
        ['name' => 'Belum Dihuni', 'amount' => 35000, 'effective_from' => '2026-01-01', 'is_active' => true]
    );
});

it('can pay multiple months in advance for occupied household', function () {
    $household = Household::factory()->create([
        'house_code' => 'ADV-01',
        'occupancy_status' => OccupancyStatus::Occupied,
    ]);

    /** @var AdvanceBillingService $service */
    $service = app(AdvanceBillingService::class);

    $result = $service->payInAdvance(
        household: $household,
        monthsCount: 3,
        paymentMethod: PaymentMethod::Cash,
        paymentDate: now(),
        notes: 'Uji bayar di muka 3 bulan'
    );

    expect($result['invoices_count'])->toBe(3);
    expect($result['total_amount'])->toBe(150000);

    // Verify all 3 invoices are marked Paid and balance is 0
    $invoices = $household->invoices()->get();
    expect($invoices)->toHaveCount(3);
    foreach ($invoices as $inv) {
        expect($inv->status)->toBe(InvoiceStatus::Paid);
        expect($inv->balance)->toBe(0);
        expect($inv->total_amount)->toBe(50000);
    }
});
