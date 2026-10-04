<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\InvoiceType;
use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

final class ConstructionFeesChart extends ChartWidget
{
    protected ?string $heading = 'Iuran Pembangunan Diterbitkan (Bukan Biaya Rutin)';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $labels[] = $month->translatedFormat('M Y');

            $amount = Invoice::query()
                ->where('invoice_type', InvoiceType::Construction)
                ->whereBetween('issue_date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                ->sum('total_amount');

            $data[] = (int) $amount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Iuran Pembangunan 1x Diterbitkan (Rp)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(217, 119, 6, 0.8)',
                    'borderColor' => '#D97706',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
