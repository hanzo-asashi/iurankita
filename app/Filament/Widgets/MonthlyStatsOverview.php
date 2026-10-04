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

        $totalAllTimeMonthlyOutstanding = (int) Invoice::query()
            ->where('invoice_type', InvoiceType::Monthly)
            ->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Partial, InvoiceStatus::Overdue])
            ->sum('balance');

        $currentMonthExpense = (int) \App\Models\Expense::query()
            ->whereYear('expense_date', now()->year)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        $totalReceived = (int) \App\Models\Payment::query()
            ->whereYear('payment_date', now()->year)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        $netCashFlow = $totalReceived - $currentMonthExpense;

        return [
            Stat::make("Tagihan Rutin ({$monthName})", 'Rp '.number_format($totalBilled, 0, ',', '.'))
                ->description("{$currentMonthInvoices->count()} rumah aktif terbit")
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),

            Stat::make('Penerimaan Kas Bulan Ini', 'Rp '.number_format($totalReceived, 0, ',', '.'))
                ->description('Iuran rutin & pembangunan')
                ->descriptionIcon('heroicon-o-arrow-down-tray')
                ->color('success'),

            Stat::make('Pengeluaran Kas Bulan Ini', 'Rp '.number_format($currentMonthExpense, 0, ',', '.'))
                ->description('Operasional, sampah, kebersihan')
                ->descriptionIcon('heroicon-o-arrow-up-tray')
                ->color('danger'),

            Stat::make('Saldo Kas Bersih Bulan Ini', 'Rp '.number_format($netCashFlow, 0, ',', '.'))
                ->description('Penerimaan dikurangi pengeluaran')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color($netCashFlow >= 0 ? 'success' : 'danger'),
        ];
    }
}
