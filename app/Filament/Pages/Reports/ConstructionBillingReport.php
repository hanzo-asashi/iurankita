<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Enums\ConstructionStatus;
use App\Enums\ConstructionType;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Invoice;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

final class ConstructionBillingReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrench;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Rekap Iuran Pembangunan';

    protected static ?string $title = 'Laporan Rekap Iuran Pembangunan (Sekali Bayar)';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.reports.construction-billing-report';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->where('invoice_type', InvoiceType::Construction)
                    ->with(['household', 'constructionProject'])
            )
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
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
                TextColumn::make('constructionProject.project_type')
                    ->label('Jenis Kegiatan')
                    ->badge()
                    ->sortable(),
                TextColumn::make('constructionProject.start_date')
                    ->label('Mulai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('constructionProject.completion_date')
                    ->label('Selesai')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Nominal (1x)')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('amount_paid')
                    ->label('Dibayar')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->color('success')
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('balance')
                    ->label('Sisa')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status Tagihan')
                    ->badge()
                    ->sortable(),
                TextColumn::make('constructionProject.status')
                    ->label('Status Proyek')
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Data Rekap Pembangunan')
            ->emptyStateDescription('Data rekapitulasi iuran pembangunan akan muncul setelah proyek pembangunan dicatat.')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Tagihan')
                    ->options(InvoiceStatus::class)
                    ->native(false),
                SelectFilter::make('construction_status')
                    ->label('Status Pembangunan')
                    ->options(ConstructionStatus::class)
                    ->native(false)
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $q, $value) => $q->whereRelation('constructionProject', 'status', $value)
                    )),
                SelectFilter::make('project_type')
                    ->label('Jenis Kegiatan')
                    ->options(ConstructionType::class)
                    ->native(false)
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $q, $value) => $q->whereRelation('constructionProject', 'project_type', $value)
                    )),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Cetak Rekap Fisik (A4)')
                ->icon(Heroicon::OutlinedPrinter)
                ->color('primary')
                ->url(fn (): string => route('reports.print.construction'))
                ->openUrlInNewTab(),
        ];
    }
}
