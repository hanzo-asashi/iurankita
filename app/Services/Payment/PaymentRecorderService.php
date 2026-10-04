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

    public function voidPayment(Payment $payment, string $reason, ?int $voidedBy = null): void
    {
        $cleanReason = mb_trim($reason);
        if ($cleanReason === '') {
            throw new InvalidArgumentException('Alasan pembatalan pembayaran wajib diisi.');
        }

        DB::transaction(function () use ($payment, $cleanReason, $voidedBy): void {
            /** @var Invoice $lockedInvoice */
            $lockedInvoice = Invoice::query()->where('id', $payment->invoice_id)->lockForUpdate()->firstOrFail();

            $revertedAmountPaid = max(0, $lockedInvoice->amount_paid - $payment->amount);
            $revertedBalance = $lockedInvoice->total_amount - $revertedAmountPaid;

            if ($revertedAmountPaid === 0) {
                $revertedStatus = ($lockedInvoice->due_date && $lockedInvoice->due_date->isPast())
                    ? InvoiceStatus::Overdue
                    : InvoiceStatus::Unpaid;
            } else {
                $revertedStatus = InvoiceStatus::Partial;
            }

            $lockedInvoice->update([
                'amount_paid' => $revertedAmountPaid,
                'balance' => $revertedBalance,
                'status' => $revertedStatus,
            ]);

            $paymentData = $payment->toArray();
            $receiptNum = $payment->receipt_number;
            $amount = $payment->amount;
            $operatorId = $voidedBy ?? Auth::id();

            AuditService::log(
                action: 'void_payment',
                model: $payment,
                oldValues: $paymentData,
                notes: "Pembayaran {$receiptNum} (Rp".number_format($amount, 0, ',', '.').") untuk invoice {$lockedInvoice->invoice_number} dibatalkan (void). Alasan: {$cleanReason} (Operator ID: {$operatorId})"
            );

            $payment->delete();
        });
    }
}
