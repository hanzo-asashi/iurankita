<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ConstructionStatus;
use App\Models\ConstructionProject;
use App\Services\Billing\ConstructionInvoiceGeneratorService;
use Illuminate\Console\Command;

final class CheckConstructionCommand extends Command
{
    protected $signature = 'construction:check {--fix : Buat invoice secara otomatis untuk proyek aktif yang belum memiliki invoice}';

    protected $description = 'Memeriksa status proyek pembangunan dan kelengkapan invoice satu kalinya';

    public function handle(ConstructionInvoiceGeneratorService $invoiceGenerator): int
    {
        $this->info('Memeriksa data proyek pembangunan...');

        $activeProjects = ConstructionProject::with(['household', 'invoice'])->where('status', ConstructionStatus::Active)->get();
        $missingInvoices = $activeProjects->filter(fn (ConstructionProject $p) => $p->invoice === null);

        $this->table(
            ['ID', 'Rumah', 'Jenis', 'Mulai', 'Status', 'Ada Invoice?'],
            $activeProjects->map(fn (ConstructionProject $p) => [
                $p->id,
                $p->household?->house_code ?? '-',
                $p->project_type->getLabel(),
                $p->start_date->format('Y-m-d'),
                $p->status->getLabel(),
                $p->invoice ? 'Ya ('.$p->invoice->invoice_number.')' : '<fg=red>Belum Ada</>',
            ])
        );

        if ($missingInvoices->isNotEmpty()) {
            $this->warn("Ditemukan {$missingInvoices->count()} proyek aktif tanpa invoice.");

            if ($this->option('fix')) {
                foreach ($missingInvoices as $project) {
                    $invoice = $invoiceGenerator->createConstructionInvoice($project);
                    $this->info("Invoice {$invoice->invoice_number} dibuat untuk proyek ID {$project->id}.");
                }
            } else {
                $this->comment('Gunakan opsi --fix untuk membuat invoice yang hilang secara otomatis.');
            }
        } else {
            $this->info('Semua proyek aktif telah memiliki invoice pembangunan.');
        }

        return self::SUCCESS;
    }
}
