<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\PaymentMethod;
use App\Models\AuditLog;
use App\Models\Household;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\Billing\SpecialInvoiceGeneratorService;
use App\Services\Payment\PaymentRecorderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dapat menerbitkan tagihan iuran khusus / insidental secara serentak ke warga aktif', function () {
    // 3 active households, 1 inactive household
    $h1 = Household::factory()->create(['house_code' => 'A-01', 'is_active' => true]);
    $h2 = Household::factory()->create(['house_code' => 'A-02', 'is_active' => true]);
    $h3 = Household::factory()->create(['house_code' => 'A-03', 'is_active' => true]);
    $hInactive = Household::factory()->create(['house_code' => 'A-04', 'is_active' => false]);

    $service = app(SpecialInvoiceGeneratorService::class);
    $result = $service->generate(
        title: 'Iuran Peringatan HUT RI Ke-81',
        amount: 50000,
        dueDate: Carbon::now()->addDays(14),
        description: 'Untuk perlombaan anak dan panggung gembira'
    );

    expect($result['generated_count'])->toBe(3);
    expect($result['skipped_count'])->toBe(0);
    expect($result['total_amount'])->toBe(150000);

    // Verify database invoices
    $invoices = Invoice::where('invoice_type', InvoiceType::Special)->get();
    expect($invoices)->toHaveCount(3);

    foreach ($invoices as $inv) {
        expect($inv->invoice_type)->toBe(InvoiceType::Special);
        expect($inv->total_amount)->toBe(50000);
        expect($inv->balance)->toBe(50000);
        expect($inv->status)->toBe(InvoiceStatus::Unpaid);
        expect($inv->notes)->toContain('Iuran Peringatan HUT RI Ke-81');
        expect($inv->household_id)->not->toBe($hInactive->id);

        $item = InvoiceItem::where('invoice_id', $inv->id)->first();
        expect($item)->not->toBeNull();
        expect($item->fee_type)->toBe('special_event');
        expect($item->total)->toBe(50000);
    }

    // Verify audit logs created
    expect(AuditLog::where('action', 'create_special_invoice')->count())->toBe(3);
});

test('mencegah penerbitan tagihan khusus ganda untuk kegiatan yang sama', function () {
    $h1 = Household::factory()->create(['house_code' => 'B-01', 'is_active' => true]);

    $service = app(SpecialInvoiceGeneratorService::class);

    // First generation
    $result1 = $service->generate(
        title: 'Gotong Royong Bersih Saluran',
        amount: 30000,
        dueDate: Carbon::now()->addDays(7)
    );
    expect($result1['generated_count'])->toBe(1);
    expect($result1['skipped_count'])->toBe(0);

    // Second generation for the exact same event
    $result2 = $service->generate(
        title: 'Gotong Royong Bersih Saluran',
        amount: 30000,
        dueDate: Carbon::now()->addDays(7)
    );
    expect($result2['generated_count'])->toBe(0);
    expect($result2['skipped_count'])->toBe(1);

    // Ensure still only 1 invoice exists
    expect(Invoice::where('household_id', $h1->id)->where('invoice_type', InvoiceType::Special)->count())->toBe(1);
});

test('menolak nominal iuran khusus nol atau negatif', function () {
    $service = app(SpecialInvoiceGeneratorService::class);

    expect(fn () => $service->generate('Kegiatan Test', 0, now()))
        ->toThrow(InvalidArgumentException::class, 'Nominal iuran khusus harus lebih besar dari 0.');

    expect(fn () => $service->generate('Kegiatan Test', -10000, now()))
        ->toThrow(InvalidArgumentException::class, 'Nominal iuran khusus harus lebih besar dari 0.');
});

test('warga dapat membayar dan melunasi tagihan iuran khusus', function () {
    $household = Household::factory()->create(['house_code' => 'C-01', 'is_active' => true]);

    $service = app(SpecialInvoiceGeneratorService::class);
    $result = $service->generate(
        title: 'Iuran Pengecatan Gapura Kompleks',
        amount: 100000,
        dueDate: Carbon::now()->addDays(10)
    );

    /** @var Invoice $invoice */
    $invoice = $result['invoices']->first();

    $recorder = app(PaymentRecorderService::class);
    $payment = $recorder->recordPayment(
        invoice: $invoice,
        amount: 100000,
        paymentMethod: PaymentMethod::Transfer,
        paymentDate: Carbon::now(),
        notes: 'Transfer pelunasan iuran gapura'
    );

    $invoice->refresh();
    expect($invoice->isPaid())->toBeTrue();
    expect($invoice->balance)->toBe(0);
    expect($payment->receipt_number)->toStartWith('KWT-');
});
