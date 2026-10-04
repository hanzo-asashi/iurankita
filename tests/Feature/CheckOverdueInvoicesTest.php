<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Household;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

it('updates unpaid invoices with past due date to overdue status', function (): void {
    Carbon::setTestNow('2026-10-15 10:00:00');

    $household1 = Household::factory()->create();
    $household2 = Household::factory()->create();
    $household3 = Household::factory()->create();

    // 1. Overdue unpaid invoice
    $overdueInvoice = Invoice::factory()->create([
        'household_id' => $household1->id,
        'invoice_type' => InvoiceType::Monthly,
        'billing_period' => '2026-09',
        'status' => InvoiceStatus::Unpaid,
        'due_date' => '2026-10-10',
        'total_amount' => 50000,
        'balance' => 50000,
        'amount_paid' => 0,
    ]);

    // 2. Future invoice (not overdue)
    $futureInvoice = Invoice::factory()->create([
        'household_id' => $household2->id,
        'invoice_type' => InvoiceType::Monthly,
        'billing_period' => '2026-10',
        'status' => InvoiceStatus::Unpaid,
        'due_date' => '2026-10-20',
        'total_amount' => 50000,
        'balance' => 50000,
        'amount_paid' => 0,
    ]);

    // 3. Already paid invoice with past due date
    $paidInvoice = Invoice::factory()->create([
        'household_id' => $household3->id,
        'invoice_type' => InvoiceType::Monthly,
        'billing_period' => '2026-08',
        'status' => InvoiceStatus::Paid,
        'due_date' => '2026-10-05',
        'total_amount' => 50000,
        'balance' => 0,
        'amount_paid' => 50000,
    ]);

    Artisan::call('billing:check-overdue');

    expect($overdueInvoice->fresh()->status)->toBe(InvoiceStatus::Overdue);
    expect($futureInvoice->fresh()->status)->toBe(InvoiceStatus::Unpaid);
    expect($paidInvoice->fresh()->status)->toBe(InvoiceStatus::Paid);

    Carbon::setTestNow();
});
