<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Audit\AuditService;
use App\Services\Receipt\ReceiptNumberGeneratorService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class PaymentRecorderService
{
    public function __construct(
        public ReceiptNumberGeneratorService $receiptNumberGenerator,
    ) {}

    public function recordPayment(
        Invoice $invoice,
        int $amount,
        PaymentMethod $paymentMethod,
        CarbonInterface|string $paymentDate,
        ?int $receivedBy = null,
        ?string $referenceNumber = null,
        ?string $proofPath = null,
        ?string $notes = null
    ): Payment {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Nominal pembayaran harus lebih besar dari 0.');
        }

        if ($amount > $invoice->balance) {
            throw new InvalidArgumentException('Pembayaran tidak dapat melebihi saldo tagihan.');
        }

        $date = is_string($paymentDate) ? Carbon::parse($paymentDate) : $paymentDate;
        $receiptNumber = $this->receiptNumberGenerator->generate($date);

        return DB::transaction(function () use (
            $invoice,
            $amount,
            $paymentMethod,
            $date,
            $receiptNumber,
            $receivedBy,
            $referenceNumber,
            $proofPath,
            $notes
        ): Payment {
            /** @var Invoice $lockedInvoice */
            $lockedInvoice = Invoice::query()->where('id', $invoice->id)->lockForUpdate()->firstOrFail();

            if ($amount > $lockedInvoice->balance) {
                throw new InvalidArgumentException('Pembayaran tidak dapat melebihi saldo tagihan.');
            }

            $payment = Payment::create([
                'invoice_id' => $lockedInvoice->id,
                'receipt_number' => $receiptNumber,
                'payment_date' => $date,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'proof_path' => $proofPath,
                'notes' => $notes,
                'received_by' => $receivedBy ?? Auth::id(),
            ]);

            $newAmountPaid = $lockedInvoice->amount_paid + $amount;
            $newBalance = $lockedInvoice->total_amount - $newAmountPaid;
            $newStatus = $newBalance <= 0 ? InvoiceStatus::Paid : InvoiceStatus::Partial;

            $lockedInvoice->update([
                'amount_paid' => $newAmountPaid,
                'balance' => max(0, $newBalance),
                'status' => $newStatus,
            ]);

            $invoice->setRawAttributes($lockedInvoice->getAttributes(), true);

            AuditService::log(
                action: 'record_payment',
                model: $payment,
                newValues: $payment->toArray(),
                notes: "Pembayaran {$receiptNumber} sebesar Rp".number_format($amount, 0, ',', '.')." untuk invoice {$lockedInvoice->invoice_number} dicatat."
            );

            return $payment;
        });
    }
}
