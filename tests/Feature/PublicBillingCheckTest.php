<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Household;
use App\Models\Invoice;

it('validates empty code input for public billing check', function (): void {
    $this->getJson(route('public.billing.check', ['code' => '']))
        ->assertStatus(422)
        ->assertJson(['success' => false]);
});

it('returns 404 when household code does not exist', function (): void {
    $this->getJson(route('public.billing.check', ['code' => 'Z-99']))
        ->assertStatus(404)
        ->assertJson(['success' => false]);
});

it('allows residents to check billing status with various code formats', function (): void {
    $household = Household::factory()->create([
        'house_code' => 'A-05',
        'block' => 'A',
        'house_number' => '05',
        'head_of_family' => 'Budi Santoso',
    ]);

    Invoice::factory()->create([
        'household_id' => $household->id,
        'invoice_type' => InvoiceType::Monthly,
        'billing_period' => now()->format('Y-m'),
        'total_amount' => 50000,
        'balance' => 50000,
        'amount_paid' => 0,
        'status' => InvoiceStatus::Unpaid,
    ]);

    // Test with normalized search 'a5'
    $response = $this->getJson(route('public.billing.check', ['code' => 'a5']));

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'household' => [
                'house_code' => 'A-05',
                'block' => 'A',
                'house_number' => '05',
            ],
            'total_outstanding' => 50000,
            'unpaid_count' => 1,
        ]);

    expect($response->json('household.masked_name'))->not->toBe('Budi Santoso');
    expect($response->json('household.masked_name'))->toContain('*');
});
