<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Household;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\Payment\PaymentRecorderService;
use Carbon\Carbon;

beforeEach(function (): void {
    $this->admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
});

it('can safely void a payment and rollback invoice balance and status to unpaid', function (): void {
    $household = Household::factory()->create(['house_code' => 'TEST-VOID-01']);
    $invoice = Invoice::create([
        'invoice_number' => 'INV/202610/9991',
        'household_id' => $household->id,
        'invoice_type' => InvoiceType::Monthly,
        'billing_period' => '2026-10',
        'issue_date' => Carbon::now()->startOfMonth(),
        'due_date' => Carbon::now()->addDays(10),
        'subtotal' => 50000,
        'total_amount' => 50000,
        'amount_paid' => 0,
        'balance' => 50000,
        'status' => InvoiceStatus::Unpaid,
    ]);

    $recorder = app(PaymentRecorderService::class);
    $payment = $recorder->recordPayment(
        invoice: $invoice,
        amount: 50000,
        paymentMethod: PaymentMethod::Transfer,
        paymentDate: Carbon::now(),
        receivedBy: $this->admin->id,
        notes: 'Transfer BRI'
    );

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::Paid)
        ->and($invoice->balance)->toBe(0)
        ->and($invoice->amount_paid)->toBe(50000);

    // Now void the payment
    $recorder->voidPayment($payment, 'Salah nomor rekening tujuan', $this->admin->id);

    // Payment should no longer exist
    expect(Payment::find($payment->id))->toBeNull();

    // Invoice should be restored
    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::Unpaid)
        ->and($invoice->balance)->toBe(50000)
        ->and($invoice->amount_paid)->toBe(0);

    // Audit log should be recorded
    $log = AuditLog::where('action', 'void_payment')->latest()->first();
    expect($log)->not->toBeNull()
        ->and($log->notes)->toContain('Salah nomor rekening tujuan');
});

it('reverts invoice status to partial when only one of multiple payments is voided', function (): void {
    $household = Household::factory()->create(['house_code' => 'TEST-VOID-02']);
    $invoice = Invoice::create([
        'invoice_number' => 'INV/202610/9992',
        'household_id' => $household->id,
        'invoice_type' => InvoiceType::Monthly,
        'billing_period' => '2026-10',
        'issue_date' => Carbon::now()->startOfMonth(),
        'due_date' => Carbon::now()->addDays(10),
        'subtotal' => 50000,
        'total_amount' => 50000,
        'amount_paid' => 0,
        'balance' => 50000,
        'status' => InvoiceStatus::Unpaid,
    ]);

    $recorder = app(PaymentRecorderService::class);
    $p1 = $recorder->recordPayment($invoice, 25000, PaymentMethod::Cash, Carbon::now(), $this->admin->id);
    $invoice->refresh();
    $p2 = $recorder->recordPayment($invoice, 25000, PaymentMethod::Cash, Carbon::now(), $this->admin->id);

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::Paid)
        ->and($invoice->balance)->toBe(0);

    // Void only the second payment
    $recorder->voidPayment($p2, 'Dobel input kwitansi kedua', $this->admin->id);

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::Partial)
        ->and($invoice->balance)->toBe(25000)
        ->and($invoice->amount_paid)->toBe(25000);
});
