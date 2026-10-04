<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

final class CheckOverdueInvoicesCommand extends Command
{
    protected $signature = 'billing:check-overdue';

    protected $description = 'Memeriksa tagihan yang melewati tanggal jatuh tempo dan mengubah statusnya menjadi Terlambat (Overdue)';

    public function handle(): int
    {
        $today = Carbon::today()->toDateString();
        $this->info("Memeriksa tagihan yang melewati tanggal jatuh tempo ({$today})...");

        // Find unpaid invoices that have past due_date and positive balance
        $overdueQuery = Invoice::query()
            ->where('status', InvoiceStatus::Unpaid)
            ->whereDate('due_date', '<', $today)
            ->where('balance', '>', 0);

        $count = $overdueQuery->count();

        if ($count === 0) {
            $this->info('Tidak ada tagihan jatuh tempo yang perlu diperbarui.');

            return self::SUCCESS;
        }

        $overdueInvoices = $overdueQuery->with('household')->get();

        $overdueQuery->update([
            'status' => InvoiceStatus::Overdue,
        ]);

        $this->table(
            ['ID', 'No. Invoice', 'Rumah', 'Jatuh Tempo', 'Sisa Tagihan'],
            $overdueInvoices->map(fn (Invoice $invoice) => [
                $invoice->id,
                $invoice->invoice_number,
                $invoice->household?->house_code ?? '-',
                $invoice->due_date?->format('d-m-Y') ?? '-',
                'Rp '.number_format($invoice->balance, 0, ',', '.'),
            ])
        );

        $this->info("Berhasil memperbarui {$count} tagihan menjadi Terlambat (Overdue).");

        return self::SUCCESS;
    }
}
