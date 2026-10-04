<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\InvoiceType;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

final class MonthlyPaymentsChart extends ChartWidget
{
    protected ?string $heading = 'Penerimaan Iuran Bulanan (6 Bulan Terakhir)';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $labels[] = $month->translatedFormat('M Y');

            $amount = Payment::query()
                ->whereHas('invoice', fn (Builder $q) => $q->where('invoice_type', InvoiceType::Monthly))
                ->whereBetween('payment_date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->sum('amount');

            $data[] = (int) $amount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pembayaran Rutin (Rp)',
                    'data' => $data,
                    'borderColor' => '#0F766E',
                    'backgroundColor' => 'rgba(15, 118, 110, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
