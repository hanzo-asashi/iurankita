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

final class MonthlyBillingReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Rekap Iuran Bulanan';

    protected static ?string $title = 'Laporan Rekap Iuran Rutin Bulanan';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.reports.monthly-billing-report';

    public function table(Table $table): Table
    {
        return $table
            ->query(Invoice::query()->where('invoice_type', InvoiceType::Monthly)->with('household'))
            ->columns([
                TextColumn::make('household.house_code')
                    ->label('Kode Rumah')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('household.head_of_family')
                    ->label('Kepala Keluarga')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('household.occupancy_status')
                    ->label('Status Hunian')
                    ->badge()
                    ->sortable(),
                TextColumn::make('billing_period')
                    ->label('Periode')
                    ->formatStateUsing(fn (?string $state): string => $state ? Carbon::createFromFormat('Y-m', $state)->translatedFormat('F Y') : '-')
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Tagihan')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('amount_paid')
                    ->label('Pembayaran')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->color('success')
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('balance')
                    ->label('Sisa Tagihan')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('billing_period', 'desc')
            ->emptyStateHeading('Belum Ada Data Rekap Bulanan')
            ->emptyStateDescription('Data rekapitulasi iuran bulanan akan muncul setelah tagihan bulanan dibuat.')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options(InvoiceStatus::class)
                    ->native(false),
                SelectFilter::make('billing_period')
                    ->label('Periode Tagihan')
                    ->native(false)
                    ->options(fn (): array => Invoice::query()
                        ->where('invoice_type', InvoiceType::Monthly)
                        ->whereNotNull('billing_period')
                        ->distinct()
                        ->orderByDesc('billing_period')
                        ->pluck('billing_period')
                        ->mapWithKeys(fn (string $period): array => [
                            $period => Carbon::createFromFormat('Y-m', $period)->translatedFormat('F Y'),
                        ])
                        ->toArray()
                    ),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('print')
                ->label('Cetak Rekap Fisik (A4)')
                ->icon(Heroicon::OutlinedPrinter)
                ->color('primary')
                ->url(fn (): string => route('reports.print.monthly', ['period' => now()->format('Y-m')]))
                ->openUrlInNewTab(),
        ];
    }
}
