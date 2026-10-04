<?php

declare(strict_types=1);

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\PaymentConfirmationStatus;
use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\Household;
use App\Models\Invoice;
use App\Models\PaymentConfirmation;
use App\Models\User;
use App\Services\Payment\PaymentRecorderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('warga dapat mengirim konfirmasi pembayaran mandiri dengan bukti transfer', function () {
    Storage::fake('public');

    $household = Household::factory()->create([
        'house_code' => 'A-01',
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV/202610/001',
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

    $file = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 800);

    $response = $this->postJson('/konfirmasi-pembayaran', [
        'invoice_id' => $invoice->id,
        'sender_name' => 'Andi Baso',
        'sender_bank' => 'Bank Sulselbar',
        'amount' => 50000,
        'payment_date' => now()->toDateString(),
        'proof_file' => $file,
        'notes' => 'Pembayaran iuran Oktober via mobile banking',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('payment_confirmations', [
        'household_id' => $household->id,
        'invoice_id' => $invoice->id,
        'sender_name' => 'Andi Baso',
        'sender_bank' => 'Bank Sulselbar',
        'amount' => 50000,
        'status' => PaymentConfirmationStatus::Pending->value,
    ]);

    $confirmation = PaymentConfirmation::first();
    expect($confirmation->proof_path)->not->toBeNull();
    Storage::disk('public')->assertExists($confirmation->proof_path);
});

test('warga tidak dapat mengirim konfirmasi untuk tagihan yang sudah lunas', function () {
    Storage::fake('public');

    $household = Household::factory()->create();

    $invoice = Invoice::create([
        'invoice_number' => 'INV/202610/002',
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

    $file = UploadedFile::fake()->image('bukti_transfer.jpg');

    $response = $this->postJson('/konfirmasi-pembayaran', [
        'invoice_id' => $invoice->id,
        'sender_name' => 'Budi Santoso',
        'sender_bank' => 'BCA',
        'amount' => 50000,
        'payment_date' => now()->toDateString(),
        'proof_file' => $file,
    ]);

    $response->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Tagihan ini telah lunas sebelumnya.',
        ]);

    $this->assertDatabaseCount('payment_confirmations', 0);
});

test('pengurus dapat menyetujui konfirmasi pembayaran dan mencatat pembayaran lunas', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $household = Household::factory()->create();

    $invoice = Invoice::create([
        'invoice_number' => 'INV/202610/003',
        'household_id' => $household->id,
        'invoice_type' => InvoiceType::Monthly,
        'billing_period' => '2026-10',
        'issue_date' => Carbon::now()->startOfMonth(),
        'due_date' => Carbon::now()->addDays(10),
        'subtotal' => 75000,
        'total_amount' => 75000,
        'amount_paid' => 0,
        'balance' => 75000,
        'status' => InvoiceStatus::Unpaid,
    ]);

    $confirmation = PaymentConfirmation::create([
        'household_id' => $household->id,
        'invoice_id' => $invoice->id,
        'sender_name' => 'Warga Ta',
        'sender_bank' => 'BRI',
        'amount' => 75000,
        'payment_date' => now()->toDateString(),
        'proof_path' => 'payment-proofs/test.jpg',
        'status' => PaymentConfirmationStatus::Pending,
    ]);

    // Test service approve workflow directly as used in Filament action
    $recorder = app(PaymentRecorderService::class);
    $payment = $recorder->recordPayment(
        invoice: $confirmation->invoice,
        amount: $confirmation->amount,
        paymentMethod: PaymentMethod::Transfer,
        paymentDate: Carbon::parse($confirmation->payment_date),
        receivedBy: $admin->id,
        referenceNumber: 'TRX123456',
        notes: "Diverifikasi otomatis dari konfirmasi mandiri portal warga (Pengirim: {$confirmation->sender_name} - {$confirmation->sender_bank})."
    );

    $confirmation->update([
        'status' => PaymentConfirmationStatus::Approved,
        'verified_by' => $admin->id,
        'verified_at' => now(),
    ]);

    expect($payment->receipt_number)->toStartWith('KWT-');
    expect($invoice->fresh()->balance)->toBe(0);
    expect($invoice->fresh()->status)->toBe(InvoiceStatus::Paid);
    expect($confirmation->fresh()->isApproved())->toBeTrue();
    expect($confirmation->fresh()->verified_by)->toBe($admin->id);
});

test('pengurus dapat menolak konfirmasi pembayaran dengan alasan', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $household = Household::factory()->create();

    $invoice = Invoice::create([
        'invoice_number' => 'INV/202610/004',
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

    $confirmation = PaymentConfirmation::create([
        'household_id' => $household->id,
        'invoice_id' => $invoice->id,
        'sender_name' => 'Warga Ta',
        'sender_bank' => 'BRI',
        'amount' => 50000,
        'payment_date' => now()->toDateString(),
        'proof_path' => 'payment-proofs/test.jpg',
        'status' => PaymentConfirmationStatus::Pending,
    ]);

    $confirmation->update([
        'status' => PaymentConfirmationStatus::Rejected,
        'rejection_reason' => 'Mutasi rekening belum masuk / foto buram',
        'verified_by' => $admin->id,
        'verified_at' => now(),
    ]);

    expect($confirmation->fresh()->isRejected())->toBeTrue();
    expect($confirmation->fresh()->rejection_reason)->toBe('Mutasi rekening belum masuk / foto buram');
    expect($invoice->fresh()->balance)->toBe(50000);
});
