<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Payment\PaymentRecorderService;
use App\Services\Receipt\ReceiptNumberGeneratorService;

it('records a full payment, updates invoice status to paid, and generates unique receipt number', function (): void {
    $invoice = Invoice::factory()->create([
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
        paymentMethod: PaymentMethod::Cash,
        paymentDate: now(),
        notes: 'Bayar tunai lunas'
    );

    expect($payment)->not->toBeNull()
        ->and($payment->amount)->toBe(50000)
        ->and($payment->receipt_number)->toStartWith('KWT-')
        ->and($invoice->fresh()->balance)->toBe(0)
        ->and($invoice->fresh()->amount_paid)->toBe(50000)
        ->and($invoice->fresh()->status)->toBe(InvoiceStatus::Paid);
});

it('records a partial payment, updates balance, and marks status as partial', function (): void {
    $invoice = Invoice::factory()->construction()->create([
        'total_amount' => 100000,
        'amount_paid' => 0,
        'balance' => 100000,
        'status' => InvoiceStatus::Unpaid,
    ]);

    $recorder = app(PaymentRecorderService::class);
    $payment = $recorder->recordPayment(
        invoice: $invoice,
        amount: 40000,
        paymentMethod: PaymentMethod::Transfer,
        paymentDate: now(),
        notes: 'Cicilan pertama'
    );

    expect($invoice->fresh()->balance)->toBe(60000)
        ->and($invoice->fresh()->amount_paid)->toBe(40000)
        ->and($invoice->fresh()->status)->toBe(InvoiceStatus::Partial);

    // Second payment to complete it
    $payment2 = $recorder->recordPayment(
        invoice: $invoice->fresh(),
        amount: 60000,
        paymentMethod: PaymentMethod::Transfer,
        paymentDate: now(),
        notes: 'Pelunasan sisa'
    );

    expect($invoice->fresh()->balance)->toBe(0)
        ->and($invoice->fresh()->amount_paid)->toBe(100000)
        ->and($invoice->fresh()->status)->toBe(InvoiceStatus::Paid)
        ->and($payment->receipt_number)->not->toBe($payment2->receipt_number);
});

it('rejects overpayment and throws an exception', function (): void {
    $invoice = Invoice::factory()->create([
        'total_amount' => 50000,
        'balance' => 50000,
    ]);

    $recorder = app(PaymentRecorderService::class);

    expect(fn () => $recorder->recordPayment(
        invoice: $invoice,
        amount: 60000, // Exceeds balance of 50.000
        paymentMethod: PaymentMethod::Cash,
        paymentDate: now()
    ))->toThrow(InvalidArgumentException::class, 'Pembayaran tidak dapat melebihi saldo tagihan.');
});

it('rejects zero or negative payment amounts', function (): void {
    $invoice = Invoice::factory()->create(['balance' => 50000]);
    $recorder = app(PaymentRecorderService::class);

    expect(fn () => $recorder->recordPayment(
        invoice: $invoice,
        amount: 0,
        paymentMethod: PaymentMethod::Cash,
        paymentDate: now()
    ))->toThrow(InvalidArgumentException::class, 'Nominal pembayaran harus lebih besar dari 0.');
});

it('generates sequential unique receipt numbers', function (): void {
    $generator = app(ReceiptNumberGeneratorService::class);

    $kwt1 = $generator->generate();
    Payment::factory()->create(['receipt_number' => $kwt1]);

    $kwt2 = $generator->generate();
    expect($kwt1)->not->toBe($kwt2);
});
