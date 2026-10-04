<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Invoice;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

final class OutstandingReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Tunggakan';

    protected static ?string $title = 'Laporan Tunggakan Iuran Warga';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.reports.outstanding-report';

    /**
     * @return array{
     *     monthly_total: int,
     *     monthly_count: int,
     *     construction_total: int,
     *     construction_count: int,
     *     grand_total: int
     * }
     */
    public function getSummaryProperty(): array
    {
        $unpaidStatuses = [InvoiceStatus::Unpaid, InvoiceStatus::Partial, InvoiceStatus::Overdue];

        $monthlyInvoices = Invoice::query()
            ->where('invoice_type', InvoiceType::Monthly)
            ->whereIn('status', $unpaidStatuses)
            ->get();

        $constructionInvoices = Invoice::query()
            ->where('invoice_type', InvoiceType::Construction)
            ->whereIn('status', $unpaidStatuses)
            ->get();

        return [
            'monthly_total' => (int) $monthlyInvoices->sum('balance'),
            'monthly_count' => $monthlyInvoices->count(),
            'construction_total' => (int) $constructionInvoices->sum('balance'),
            'construction_count' => $constructionInvoices->count(),
            'grand_total' => (int) ($monthlyInvoices->sum('balance') + $constructionInvoices->sum('balance')),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Partial, InvoiceStatus::Overdue])
                    ->with(['household', 'constructionProject'])
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('No. Tagihan')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('household.house_code')
                    ->label('Rumah')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('household.head_of_family')
                    ->label('Kepala Keluarga')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('household.phone')
                    ->label('WhatsApp / Telp')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('invoice_type')
                    ->label('Jenis Tunggakan')
                    ->badge()
                    ->sortable(),
                TextColumn::make('rincian')
                    ->label('Keterangan Kewajiban')
                    ->state(function (Invoice $record): string {
                        if ($record->isMonthly()) {
                            return 'Iuran Periode '.($record->billing_period ? Carbon::createFromFormat('Y-m', $record->billing_period)->translatedFormat('F Y') : '-');
                        }

                        $projectName = $record->constructionProject?->project_type?->getLabel() ?? 'Pembangunan';

                        return "Iuran {$projectName} (1x Sekali Bayar)";
                    }),
                TextColumn::make('total_amount')
                    ->label('Total Kewajiban')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->sortable(),
                TextColumn::make('balance')
                    ->label('Sisa Tunggakan')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->color('danger')
                    ->weight('bold')
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('due_date', 'asc')
            ->emptyStateHeading('Tidak Ada Tunggakan')
            ->emptyStateDescription('Semua tagihan warga telah lunas atau belum ada tagihan terbit.')
            ->filters([
                SelectFilter::make('invoice_type')
                    ->label('Jenis Tunggakan')
                    ->options(InvoiceType::class)
                    ->native(false),
                SelectFilter::make('status')
                    ->label('Status Tagihan')
                    ->options([
                        InvoiceStatus::Unpaid->value => 'Belum Lunas',
                        InvoiceStatus::Partial->value => 'Sebagian',
                        InvoiceStatus::Overdue->value => 'Terlambat',
                    ])
                    ->native(false),
            ]);
    }
}
