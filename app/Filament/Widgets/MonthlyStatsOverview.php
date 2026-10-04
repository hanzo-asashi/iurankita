<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class MonthlyStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $currentMonth = now()->format('Y-m');
        $monthName = now()->translatedFormat('F Y');

        $currentMonthInvoices = Invoice::query()
            ->where('invoice_type', InvoiceType::Monthly)
            ->where('billing_period', $currentMonth)
            ->get();

        $totalBilled = $currentMonthInvoices->sum('total_amount');
        $paidCount = $currentMonthInvoices->where('status', InvoiceStatus::Paid)->count();
        $unpaidCount = $currentMonthInvoices->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Partial, InvoiceStatus::Overdue])->count();
        $currentMonthPaid = $currentMonthInvoices->sum('amount_paid');
        $currentMonthBalance = $currentMonthInvoices->sum('balance');

        // Total seluruh tunggakan bulanan (termasuk bulan-bulan sebelumnya)
        $totalAllTimeMonthlyOutstanding = (int) Invoice::query()
            ->where('invoice_type', InvoiceType::Monthly)
            ->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Partial, InvoiceStatus::Overdue])
            ->sum('balance');

        return [
            Stat::make("Tagihan Rutin ({$monthName})", 'Rp '.number_format($totalBilled, 0, ',', '.'))
                ->description("{$currentMonthInvoices->count()} rumah aktif terbit")
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),

            Stat::make('Pembayaran Diterima Bulan Ini', 'Rp '.number_format($currentMonthPaid, 0, ',', '.'))
                ->description("{$paidCount} rumah telah lunas")
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Tunggakan Iuran Bulanan', 'Rp '.number_format($totalAllTimeMonthlyOutstanding, 0, ',', '.'))
                ->description("{$unpaidCount} rumah belum lunas ({$monthName})")
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('danger'),
        ];
    }
}
