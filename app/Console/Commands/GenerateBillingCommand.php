<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Billing\MonthlyInvoiceGeneratorService;
use Carbon\Carbon;
use Illuminate\Console\Command;

final class GenerateBillingCommand extends Command
{
    protected $signature = 'billing:generate {--period= : Periode tagihan dengan format YYYY-MM (contoh: 2026-10)}';

    protected $description = 'Membuat tagihan iuran rutin bulanan untuk seluruh rumah yang aktif';

    public function handle(MonthlyInvoiceGeneratorService $generator): int
    {
        $periodInput = $this->option('period');
        $period = $periodInput ? Carbon::createFromFormat('Y-m', $periodInput) : Carbon::now();

        if (! $period) {
            $this->error('Format periode tidak valid. Gunakan format YYYY-MM (contoh: 2026-10).');

            return self::FAILURE;
        }

        $periodFormatted = $period->translatedFormat('F Y');
        $this->info("Menghasilkan tagihan iuran bulanan untuk periode {$periodFormatted}...");

        $result = $generator->generate($period);

        $this->table(
            ['Deskripsi', 'Jumlah'],
            [
                ['Tagihan Dibuat', $result['generated_count']],
                ['Tagihan Dilewati (Sudah Ada)', $result['skipped_count']],
                ['Total Nominal Diterbitkan', 'Rp '.number_format($result['total_amount'], 0, ',', '.')],
            ]
        );

        $this->info('Proses pembuatan tagihan bulanan selesai.');

        return self::SUCCESS;
    }
}
