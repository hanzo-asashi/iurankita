<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\OccupancyStatus;
use App\Models\Household;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\Audit\AuditService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class SpecialInvoiceGeneratorService
{
    /**
     * @param  array<int, OccupancyStatus>|null  $occupancyStatuses
     * @return array{
     *     generated_count: int,
     *     skipped_count: int,
     *     total_amount: int,
     *     invoices: Collection<int, Invoice>
     * }
     */
    public function generate(
        string $title,
        int $amount,
        CarbonInterface|string $dueDate,
        ?string $eventCode = null,
        ?string $description = null,
        ?array $occupancyStatuses = null
    ): array {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Nominal iuran khusus harus lebih besar dari 0.');
        }

        $due = is_string($dueDate) ? Carbon::parse($dueDate) : $dueDate->copy();
        $code = $eventCode ?: Str::slug(mb_substr($title, 0, 30));
        $billingPeriod = 'SPE-'.$code;

        $householdsQuery = Household::query()->active();
        if (! empty($occupancyStatuses)) {
            $householdsQuery->whereIn('occupancy_status', $occupancyStatuses);
        }
        $households = $householdsQuery->get();

        $generatedInvoices = collect();
        $skippedCount = 0;
        $totalAmountGenerated = 0;
        $issueDate = Carbon::now();

        foreach ($households as $household) {
            $existing = Invoice::query()
                ->where('household_id', $household->id)
                ->where('invoice_type', InvoiceType::Special)
                ->where('billing_period', $billingPeriod)
                ->first();

            if ($existing !== null) {
                $skippedCount++;

                continue;
            }

            $invoice = DB::transaction(function () use (
                $household,
                $title,
                $amount,
                $billingPeriod,
                $issueDate,
                $due,
                $description
            ): Invoice {
                $year = $issueDate->format('Y');
                $nextSequence = Invoice::query()
                    ->whereYear('created_at', (int) $issueDate->format('Y'))
                    ->count() + 1;
                $invoiceNumber = sprintf('INV-SPE-%s-%05d', $year, $nextSequence);

                while (Invoice::where('invoice_number', $invoiceNumber)->exists()) {
                    $nextSequence++;
                    $invoiceNumber = sprintf('INV-SPE-%s-%05d', $year, $nextSequence);
                }

                $invoice = Invoice::create([
                    'invoice_number' => $invoiceNumber,
                    'household_id' => $household->id,
                    'invoice_type' => InvoiceType::Special,
                    'billing_period' => $billingPeriod,
                    'issue_date' => $issueDate,
                    'due_date' => $due,
                    'subtotal' => $amount,
                    'total_amount' => $amount,
                    'amount_paid' => 0,
                    'balance' => $amount,
                    'status' => InvoiceStatus::Unpaid,
                    'notes' => $title.($description ? ' - '.$description : ''),
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'fee_type' => 'special_event',
                    'description' => $title,
                    'quantity' => 1,
                    'unit_price' => $amount,
                    'total' => $amount,
                    'metadata' => [
                        'event_code' => $billingPeriod,
                        'household_code' => $household->house_code,
                    ],
                ]);

                AuditService::log(
                    action: 'create_special_invoice',
                    model: $invoice,
                    newValues: $invoice->toArray(),
                    notes: "Tagihan insidental/khusus '{$title}' diterbitkan untuk {$household->house_code} (Rp".number_format($amount, 0, ',', '.').')'
                );

                return $invoice;
            });

            $generatedInvoices->push($invoice);
            $totalAmountGenerated += $amount;
        }

        return [
            'generated_count' => $generatedInvoices->count(),
            'skipped_count' => $skippedCount,
            'total_amount' => $totalAmountGenerated,
            'invoices' => $generatedInvoices,
        ];
    }
}
