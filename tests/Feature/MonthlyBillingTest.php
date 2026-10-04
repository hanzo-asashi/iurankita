<?php

declare(strict_types=1);

use App\Enums\OccupancyStatus;
use App\Models\FeeRate;
use App\Models\Household;
use App\Models\Invoice;
use App\Services\Billing\MonthlyBillingCalculatorService;
use App\Services\Billing\MonthlyInvoiceGeneratorService;
use Carbon\Carbon;

beforeEach(function (): void {
    FeeRate::firstOrCreate(
        ['code' => 'monthly_occupied'],
        ['name' => 'Iuran Dihuni', 'amount' => 50000, 'effective_from' => '2026-01-01', 'is_active' => true]
    );

    FeeRate::firstOrCreate(
        ['code' => 'monthly_unoccupied'],
        ['name' => 'Iuran Belum Dihuni', 'amount' => 35000, 'effective_from' => '2026-01-01', 'is_active' => true]
    );

    FeeRate::firstOrCreate(
        ['code' => 'construction_one_time'],
        ['name' => 'Iuran Pembangunan', 'amount' => 100000, 'effective_from' => '2026-01-01', 'is_active' => true]
    );
});

it('calculates occupied household routine fee as Rp50.000', function (): void {
    $calculator = app(MonthlyBillingCalculatorService::class);
    $household = Household::factory()->create([
        'house_code' => 'A-01',
        'occupancy_status' => OccupancyStatus::Occupied,
    ]);

    $result = $calculator->calculateMonthlyInvoice($household, Carbon::parse('2026-09-01'));

    expect($result->amount)->toBe(50000)
        ->and($result->feeCode)->toBe('monthly_occupied');
});

it('calculates unoccupied household routine fee as Rp35.000', function (): void {
    $calculator = app(MonthlyBillingCalculatorService::class);
    $household = Household::factory()->create([
        'house_code' => 'A-02',
        'occupancy_status' => OccupancyStatus::Unoccupied,
    ]);

    $result = $calculator->calculateMonthlyInvoice($household, Carbon::parse('2026-09-01'));

    expect($result->amount)->toBe(35000)
        ->and($result->feeCode)->toBe('monthly_unoccupied');
});

it('generates exactly one monthly invoice per household per period and is idempotent', function (): void {
    $generator = app(MonthlyInvoiceGeneratorService::class);

    $h1 = Household::factory()->create(['house_code' => 'A-01', 'occupancy_status' => OccupancyStatus::Occupied]);
    $h2 = Household::factory()->create(['house_code' => 'A-02', 'occupancy_status' => OccupancyStatus::Unoccupied]);

    $result1 = $generator->generate(Carbon::parse('2026-09-01'));

    expect($result1['generated_count'])->toBe(2)
        ->and($result1['skipped_count'])->toBe(0)
        ->and($result1['total_amount'])->toBe(85000);

    expect(Invoice::where('billing_period', '2026-09')->count())->toBe(2);

    // Run again for the exact same period
    $result2 = $generator->generate(Carbon::parse('2026-09-01'));

    expect($result2['generated_count'])->toBe(0)
        ->and($result2['skipped_count'])->toBe(2);

    // Invoices count remains 2
    expect(Invoice::where('billing_period', '2026-09')->count())->toBe(2);
});

it('preserves historical invoice amounts when fee rate is changed later', function (): void {
    $generator = app(MonthlyInvoiceGeneratorService::class);

    $household = Household::factory()->create([
        'house_code' => 'A-01',
        'occupancy_status' => OccupancyStatus::Occupied,
    ]);

    // Generate for 2026-09 when tariff is 50.000
    $generator->generate(Carbon::parse('2026-09-01'));

    $invoice2026 = Invoice::where('household_id', $household->id)
        ->where('billing_period', '2026-09')
        ->first();

    expect($invoice2026->total_amount)->toBe(50000);

    // Update master tariff in 2027 to 55.000
    FeeRate::where('code', 'monthly_occupied')->update(['amount' => 55000]);

    // 2026 invoice MUST NOT change
    $freshInvoice = $invoice2026->fresh();
    expect($freshInvoice->total_amount)->toBe(50000);

    // Future invoice in 2027 uses new tariff
    $generator->generate(Carbon::parse('2027-01-01'));
    $invoice2027 = Invoice::where('household_id', $household->id)
        ->where('billing_period', '2027-01')
        ->first();

    expect($invoice2027->total_amount)->toBe(55000);
});
