<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Enums\InvoiceType;
use App\Enums\PaymentMethod;
use App\Models\Payment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

final class PaymentReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Penerimaan';

    protected static ?string $title = 'Laporan Penerimaan Pembayaran Iuran';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.reports.payment-report';

    /**
     * @return array{monthly: int, construction: int, total: int, cash: int, bank: int}
     */
    public function getSummaryProperty(): array
    {
        $monthly = Payment::query()
            ->whereHas('invoice', fn (Builder $q) => $q->where('invoice_type', InvoiceType::Monthly))
            ->sum('amount');

        $construction = Payment::query()
            ->whereHas('invoice', fn (Builder $q) => $q->where('invoice_type', InvoiceType::Construction))
            ->sum('amount');

        $cash = Payment::query()
            ->where('payment_method', PaymentMethod::Cash)
            ->sum('amount');

        $bank = Payment::query()
            ->whereIn('payment_method', [PaymentMethod::Transfer, PaymentMethod::Qris])
            ->sum('amount');

        return [
            'monthly' => (int) $monthly,
            'construction' => (int) $construction,
            'total' => (int) ($monthly + $construction),
            'cash' => (int) $cash,
            'bank' => (int) $bank,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Payment::query()->with(['invoice.household', 'receiver']))
            ->columns([
                TextColumn::make('receipt_number')
                    ->label('No. Kwitansi')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('payment_date')
                    ->label('Tanggal Bayar')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('invoice.household.house_code')
                    ->label('Rumah')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('invoice.household.head_of_family')
                    ->label('Warga')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('invoice.invoice_type')
                    ->label('Kategori Penerimaan')
                    ->badge()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->color('success')
                    ->weight('bold')
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->sortable(),
                TextColumn::make('receiver.name')
                    ->label('Petugas Penerima')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->defaultSort('payment_date', 'desc')
            ->emptyStateHeading('Belum Ada Penerimaan Pembayaran')
            ->emptyStateDescription('Riwayat pembayaran iuran warga yang telah dicatat akan tampil di sini.')
            ->filters([
                SelectFilter::make('invoice_type')
                    ->label('Kategori Iuran')
                    ->options(InvoiceType::class)
                    ->native(false)
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'],
                        fn (Builder $q, $value) => $q->whereRelation('invoice', 'invoice_type', $value)
                    )),
                SelectFilter::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options(PaymentMethod::class)
                    ->native(false),
                SelectFilter::make('received_by')
                    ->label('Petugas')
                    ->native(false)
                    ->relationship('receiver', 'name'),
                Filter::make('payment_date')
                    ->schema([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $date) => $q->whereDate('payment_date', '>=', $date))
                            ->when($data['until'], fn (Builder $q, $date) => $q->whereDate('payment_date', '<=', $date));
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Cetak Rekap Fisik (A4)')
                ->icon(Heroicon::OutlinedPrinter)
                ->color('primary')
                ->url(fn (): string => route('reports.print.payments', ['month' => now()->format('Y-m')]))
                ->openUrlInNewTab(),
        ];
    }
}
