<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\ConstructionStatus;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\ConstructionProject;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class ConstructionStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $activeProjectsCount = ConstructionProject::query()
            ->where('status', ConstructionStatus::Active)
            ->count();

        $constructionInvoices = Invoice::query()
            ->where('invoice_type', InvoiceType::Construction)
            ->get();

        $totalBilled = $constructionInvoices->sum('total_amount');
        $totalPaid = $constructionInvoices->sum('amount_paid');
        $totalOutstanding = $constructionInvoices->sum('balance');
        $unpaidInvoicesCount = $constructionInvoices
            ->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Partial, InvoiceStatus::Overdue])
            ->count();

        return [
            Stat::make('Pembangunan Aktif', "{$activeProjectsCount} Kegiatan")
                ->description('Sedang berjalan (iuran 1x per kegiatan)')
                ->descriptionIcon('heroicon-o-wrench-screwdriver')
                ->color('warning'),

            Stat::make('Total Iuran Pembangunan Terbit', 'Rp '.number_format($totalBilled, 0, ',', '.'))
                ->description('Sudah dibayar: Rp '.number_format($totalPaid, 0, ',', '.'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Tunggakan Iuran Pembangunan', 'Rp '.number_format($totalOutstanding, 0, ',', '.'))
                ->description("{$unpaidInvoicesCount} proyek belum lunas")
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),
        ];
    }
}
