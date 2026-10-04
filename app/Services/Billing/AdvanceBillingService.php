<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\PaymentMethod;
use App\Models\AppSetting;
use App\Models\Household;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Payment\PaymentRecorderService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

final class AdvanceBillingService
{
    public function __construct(
        public MonthlyBillingCalculatorService $calculator,
        public PaymentRecorderService $paymentRecorder,
    ) {}

    /**
     * @return array{
     *     invoices_count: int,
     *     total_amount: int,
     *     payments: array<int, Payment>
     * }
     */
    public function payInAdvance(
        Household $household,
        int $monthsCount,
        PaymentMethod $paymentMethod,
        CarbonInterface|string $paymentDate,
        ?string $referenceNumber = null,
        ?string $proofPath = null,
        ?string $notes = null
    ): array {
        return DB::transaction(function () use (
            $household,
            $monthsCount,
            $paymentMethod,
            $paymentDate,
            $referenceNumber,
            $proofPath,
            $notes
        ): array {
            $setting = AppSetting::current();
            $dueDay = $setting->monthly_due_day ?? 10;
            $currentPeriodStr = Carbon::now()->format('Y-m');

            // Cek apakah tagihan bulan ini sudah ada dan belum lunas
            /** @var Invoice|null $currentInvoice */
            $currentInvoice = $household->invoices()
                ->where('invoice_type', InvoiceType::Monthly)
                ->where('billing_period', $currentPeriodStr)
                ->first();

            $periodsToProcess = [];
            $currentMonth = Carbon::now()->startOfMonth();

            // Jika bulan ini belum ada tagihan atau ada tapi belum lunas, proses bulan ini terlebih dahulu
            if (! $currentInvoice || $currentInvoice->balance > 0) {
                $periodsToProcess[] = $currentMonth->copy();
                $remaining = $monthsCount - 1;
            } else {
                $remaining = $monthsCount;
            }

            for ($i = 1; $i <= $remaining; $i++) {
                $periodsToProcess[] = $currentMonth->copy()->addMonths($i);
            }

            $totalPaid = 0;
            $recordedPayments = [];

            foreach ($periodsToProcess as $periodDate) {
                $periodStr = $periodDate->format('Y-m');

                /** @var Invoice|null $existingInvoice */
                $existingInvoice = $household->invoices()
                    ->where('invoice_type', InvoiceType::Monthly)
                    ->where('billing_period', $periodStr)
                    ->first();

                if (! $existingInvoice) {
                    $billingResult = $this->calculator->calculateMonthlyInvoice($household, $periodDate);
                    $dueDate = Carbon::create($periodDate->year, $periodDate->month, $dueDay)->endOfDay();
                    $lastSeq = Invoice::query()->whereYear('issue_date', $periodDate->year)->count() + 1;
                    $invoiceNumber = sprintf('INV/%s/%04d', $periodDate->format('Ym'), $lastSeq);

                    $existingInvoice = Invoice::create([
                        'invoice_number' => $invoiceNumber,
                        'household_id' => $household->id,
                        'invoice_type' => InvoiceType::Monthly,
                        'billing_period' => $periodStr,
                        'issue_date' => $periodDate->copy()->startOfMonth(),
                        'due_date' => $dueDate,
                        'subtotal' => $billingResult->amount,
                        'total_amount' => $billingResult->amount,
                        'amount_paid' => 0,
                        'balance' => $billingResult->amount,
                        'status' => InvoiceStatus::Unpaid,
                        'notes' => 'Tagihan iuran pembayaran di muka (advance).',
                    ]);
                }

                if ($existingInvoice->balance > 0) {
                    $amountToPay = $existingInvoice->balance;
                    $payment = $this->paymentRecorder->recordPayment(
                        invoice: $existingInvoice,
                        amount: $amountToPay,
                        paymentMethod: $paymentMethod,
                        paymentDate: $paymentDate,
                        referenceNumber: $referenceNumber,
                        proofPath: $proofPath,
                        notes: $notes ? "{$notes} (Periode {$periodStr})" : "Pembayaran di muka periode {$periodStr}"
                    );

                    $totalPaid += $amountToPay;
                    $recordedPayments[] = $payment;
                }
            }

            return [
                'invoices_count' => count($recordedPayments),
                'total_amount' => $totalPaid,
                'payments' => $recordedPayments,
            ];
        });
    }
}
