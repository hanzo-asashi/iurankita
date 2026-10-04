<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\ConstructionProject;
use App\Models\FeeRate;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\Audit\AuditService;
use Illuminate\Support\Facades\DB;

final class ConstructionInvoiceGeneratorService
{
    /**
     * Create exactly one construction invoice for a construction project.
     * Idempotent: returns existing invoice if already created.
     */
    public function createConstructionInvoice(ConstructionProject $project): Invoice
    {
        $existing = Invoice::query()
            ->where('construction_project_id', $project->id)
            ->where('invoice_type', InvoiceType::Construction)
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        $feeAmount = $project->one_time_fee;
        if ($feeAmount <= 0) {
            $activeRate = FeeRate::getActiveRate('construction_one_time', $project->start_date);
            $feeAmount = $activeRate ? $activeRate->amount : 100000;
        }

        $projectLabel = $project->project_type->getLabel();
        $startDateFormatted = $project->start_date->translatedFormat('d F Y');

        return DB::transaction(function () use ($project, $feeAmount, $projectLabel, $startDateFormatted): Invoice {
            $year = $project->start_date->format('Y');
            $nextSequence = Invoice::query()
                ->whereYear('created_at', $project->start_date->year)
                ->count() + 1;
            $invoiceNumber = sprintf('INV-%s-%06d', $year, $nextSequence);

            while (Invoice::where('invoice_number', $invoiceNumber)->exists()) {
                $nextSequence++;
                $invoiceNumber = sprintf('INV-%s-%06d', $year, $nextSequence);
            }

            $dueDate = $project->start_date->copy()->addDays(14);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'household_id' => $project->household_id,
                'invoice_type' => InvoiceType::Construction,
                'billing_period' => $project->start_date->format('Y-m'),
                'issue_date' => $project->start_date,
                'due_date' => $dueDate,
                'subtotal' => $feeAmount,
                'total_amount' => $feeAmount,
                'amount_paid' => 0,
                'balance' => $feeAmount,
                'status' => InvoiceStatus::Unpaid,
                'construction_project_id' => $project->id,
                'notes' => "Tagihan Iuran Pembangunan: {$projectLabel} (Mulai: {$startDateFormatted}) - Sekali Bayar",
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'fee_type' => 'construction_one_time',
                'description' => "Iuran Pembangunan ({$projectLabel}) - Satu kali bayar",
                'quantity' => 1,
                'unit_price' => $feeAmount,
                'total' => $feeAmount,
                'metadata' => [
                    'construction_project_id' => $project->id,
                    'project_type' => $project->project_type->value,
                    'start_date' => $project->start_date->format('Y-m-d'),
                ],
            ]);

            $project->update([
                'one_time_fee' => $feeAmount,
                'fee_invoice_id' => $invoice->id,
            ]);

            AuditService::log(
                action: 'create_construction_invoice',
                model: $invoice,
                newValues: $invoice->toArray(),
                notes: "Invoice iuran pembangunan dibuat untuk project ID {$project->id} sebesar Rp".number_format($feeAmount, 0, ',', '.')
            );

            return $invoice;
        });
    }
}
