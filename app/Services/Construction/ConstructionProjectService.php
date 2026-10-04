<?php

declare(strict_types=1);

namespace App\Services\Construction;

use App\Enums\ConstructionStatus;
use App\Enums\InvoiceStatus;
use App\Models\ConstructionProject;
use App\Models\Invoice;
use App\Services\Audit\AuditService;
use App\Services\Billing\ConstructionInvoiceGeneratorService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

final class ConstructionProjectService
{
    public function __construct(
        public ConstructionInvoiceGeneratorService $invoiceGenerator,
    ) {}

    public function startProject(ConstructionProject $project, ?CarbonInterface $startDate = null): Invoice
    {
        return DB::transaction(function () use ($project, $startDate): Invoice {
            $oldValues = $project->toArray();

            $project->update([
                'status' => ConstructionStatus::Active,
                'start_date' => $startDate ?? $project->start_date ?? now(),
            ]);

            $invoice = $this->invoiceGenerator->createConstructionInvoice($project);

            AuditService::log(
                action: 'start_construction_project',
                model: $project,
                oldValues: $oldValues,
                newValues: $project->fresh()->toArray(),
                notes: "Proyek pembangunan {$project->id} diaktifkan dan invoice {$invoice->invoice_number} dibuat."
            );

            return $invoice;
        });
    }

    public function completeProject(
        ConstructionProject $project,
        CarbonInterface|string $completionDate,
        ?string $notes = null
    ): void {
        DB::transaction(function () use ($project, $completionDate, $notes): void {
            $oldValues = $project->toArray();
            $resolvedDate = is_string($completionDate) ? Carbon::parse($completionDate) : $completionDate;

            $project->update([
                'status' => ConstructionStatus::Completed,
                'completion_date' => $resolvedDate,
                'notes' => $notes ? mb_trim(($project->notes ?? '')."\n".$notes) : $project->notes,
            ]);

            AuditService::log(
                action: 'complete_construction_project',
                model: $project,
                oldValues: $oldValues,
                newValues: $project->fresh()->toArray(),
                notes: "Proyek pembangunan {$project->id} diselesaikan pada {$resolvedDate->format('Y-m-d')}."
            );
        });
    }

    public function cancelProject(ConstructionProject $project, ?string $reason = null): void
    {
        DB::transaction(function () use ($project, $reason): void {
            $oldValues = $project->toArray();

            $project->update([
                'status' => ConstructionStatus::Cancelled,
                'notes' => $reason ? mb_trim(($project->notes ?? '')."\n[Dibatalkan]: ".$reason) : $project->notes,
            ]);

            $invoice = $project->feeInvoice ?? $project->invoice;
            if ($invoice !== null && $invoice->amount_paid === 0 && $invoice->status !== InvoiceStatus::Paid) {
                $invoice->update([
                    'status' => InvoiceStatus::Cancelled,
                    'notes' => mb_trim(($invoice->notes ?? '')."\n[Dibatalkan]: Proyek pembangunan dibatalkan."),
                ]);
            }

            AuditService::log(
                action: 'cancel_construction_project',
                model: $project,
                oldValues: $oldValues,
                newValues: $project->fresh()->toArray(),
                notes: "Proyek pembangunan {$project->id} dibatalkan."
            );
        });
    }
}
