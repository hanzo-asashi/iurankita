<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\PaymentMethod;
use App\Models\Household;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dapat menampilkan kwitansi standar A4', function () {
    $household = Household::factory()->create([
        'house_code' => 'B-02',
        'head_of_family' => 'Muhammad Basri',
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV/202610/101',
        'household_id' => $household->id,
        'invoice_type' => InvoiceType::Monthly,
        'billing_period' => '2026-10',
        'issue_date' => Carbon::now()->startOfMonth(),
        'due_date' => Carbon::now()->addDays(10),
        'subtotal' => 50000,
        'total_amount' => 50000,
        'amount_paid' => 50000,
        'balance' => 0,
        'status' => InvoiceStatus::Paid,
    ]);

    $payment = Payment::create([
        'invoice_id' => $invoice->id,
        'receipt_number' => 'KWT-202610-00101',
        'amount' => 50000,
        'payment_method' => PaymentMethod::Cash,
        'payment_date' => Carbon::now(),
        'notes' => 'Lunas tunai di tempat',
    ]);

    $response = $this->get(route('receipt.print', $payment));

    $response->assertOk();
    $response->assertSee('KWT-202610-00101');
    $response->assertSee('Muhammad Basri');
    $response->assertSee('Format Thermal (58mm)');
});

test('dapat menampilkan struk kasir termal mini 58mm / 80mm', function () {
    $household = Household::factory()->create([
        'house_code' => 'B-03',
        'head_of_family' => 'Hj. Rosdiana',
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV/202610/102',
        'household_id' => $household->id,
        'invoice_type' => InvoiceType::Monthly,
        'billing_period' => '2026-10',
        'issue_date' => Carbon::now()->startOfMonth(),
        'due_date' => Carbon::now()->addDays(10),
        'subtotal' => 50000,
        'total_amount' => 50000,
        'amount_paid' => 50000,
        'balance' => 0,
        'status' => InvoiceStatus::Paid,
    ]);

    $payment = Payment::create([
        'invoice_id' => $invoice->id,
        'receipt_number' => 'KWT-202610-00102',
        'amount' => 50000,
        'payment_method' => PaymentMethod::Transfer,
        'payment_date' => Carbon::now(),
        'reference_number' => 'TRX998877',
        'notes' => 'Transfer via BRImo',
    ]);

    $response = $this->get(route('receipt.print', ['payment' => $payment, 'format' => 'thermal']));

    $response->assertOk();
    $response->assertSee('STRUK BUKTI PEMBAYARAN');
    $response->assertSee('KWT-202610-00102');
    $response->assertSee('Hj. Rosdiana');
    $response->assertSee('B-03');
    $response->assertSee('58mm');
    $response->assertSee('TRX998877');
    $response->assertSee('LUNAS');
});
