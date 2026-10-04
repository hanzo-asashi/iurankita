<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\InvoiceType;
use App\Models\Household;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

it('prohibits deleting a household that has invoices', function (): void {
    $admin = User::factory()->create();
    $household = Household::factory()->create();

    // Create an invoice for this household
    Invoice::factory()->create([
        'household_id' => $household->id,
        'invoice_type' => InvoiceType::Monthly,
    ]);

    expect(Gate::forUser($admin)->allows('delete', $household))->toBeFalse();
});

it('permits deleting a household without invoices', function (): void {
    $admin = User::factory()->create();
    $household = Household::factory()->create();

    expect(Gate::forUser($admin)->allows('delete', $household))->toBeTrue();
});
