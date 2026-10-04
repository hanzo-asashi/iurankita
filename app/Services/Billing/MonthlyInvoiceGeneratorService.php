<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\AppSetting;
use App\Models\Household;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\Audit\AuditService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class MonthlyInvoiceGeneratorService
{
    public function __construct(
        public MonthlyBillingCalculatorService $calculator,
    ) {}

    /**
     * @return array{
     *     generated_count: int,
     *     skipped_count: int,
     *     total_amount: int,
     *     invoices: Collection<int, Invoice>
     * }
     */
    public function generate(CarbonInterface|string $periodDate, ?int $householdId = null): array
    {
        $period = is_string($periodDate) ? Carbon::parse($periodDate.'-01') : $periodDate->copy()->startOfMonth();
        $periodString = $period->format('Y-m');
        $setting = AppSetting::current();

        $householdsQuery = Household::query()->active();
        if ($householdId !== null) {
            $householdsQuery->where('id', $householdId);
        }
        $households = $householdsQuery->get();

        $generatedInvoices = collect();
        $skippedCount = 0;
        $totalAmountGenerated = 0;

        $dueDay = min($setting->monthly_due_day, $period->daysInMonth);
        $dueDate = $period->copy()->day($dueDay);
        $issueDate = $period->copy()->startOfMonth();

        foreach ($households as $household) {
            $existing = Invoice::query()
                ->where('household_id', $household->id)
                ->where('invoice_type', InvoiceType::Monthly)
                ->where('billing_period', $periodString)
                ->first();

            if ($existing !== null) {
                $skippedCount++;

                continue;
            }

            $calculation = $this->calculator->calculateMonthlyInvoice($household, $period);

            $invoice = DB::transaction(function () use (
                $household,
                $period,
                $periodString,
                $issueDate,
                $dueDate,
                $calculation
            ): Invoice {
                $year = $period->format('Y');
                $nextSequence = Invoice::query()
                    ->whereYear('created_at', $period->year)
                    ->count() + 1;
                $invoiceNumber = sprintf('INV-%s-%06d', $year, $nextSequence);

                while (Invoice::where('invoice_number', $invoiceNumber)->exists()) {
                    $nextSequence++;
                    $invoiceNumber = sprintf('INV-%s-%06d', $year, $nextSequence);
                }

                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'household_id' => $household->id,
                    'invoice_type' => InvoiceType::Monthly,
                    'billing_period' => $periodString,
                    'issue_date' => $issueDate,
                    'due_date' => $dueDate,
                    'subtotal' => $calculation->amount,
                    'total_amount' => $calculation->amount,
                    'amount_paid' => 0,
                    'balance' => $calculation->amount,
                    'status' => InvoiceStatus::Unpaid,
                    'notes' => "Tagihan Iuran Rutin Bulanan - {$period->translatedFormat('F Y')}",
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'fee_type' => $calculation->feeCode,
                    'description' => $calculation->description,
                    'quantity' => 1,
                    'unit_price' => $calculation->amount,
                    'total' => $calculation->amount,
                    'metadata' => [
                        'occupancy_status' => $household->occupancy_status->value,
                        'period' => $periodString,
                    ],
                ]);

                AuditService::log(
                    action: 'create_monthly_invoice',
                    model: $invoice,
                    newValues: $invoice->toArray(),
                    notes: "Tagihan bulanan dibuat untuk {$household->house_code} periode {$periodString}"
                );

                return $invoice;
            });

            $generatedInvoices->push($invoice);
            $totalAmountGenerated += $calculation->amount;
        }

        return [
            'generated_count' => $generatedInvoices->count(),
            'skipped_count' => $skippedCount,
            'total_amount' => $totalAmountGenerated,
            'invoices' => $generatedInvoices,
        ];
    }
}
