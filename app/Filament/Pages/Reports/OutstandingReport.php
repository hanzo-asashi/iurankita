<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\PaymentMethod;
use App\Models\AppSetting;
use App\Models\Invoice;
use App\Services\Payment\PaymentRecorderService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
            ])
            ->recordActions([
                Action::make('pay')
                    ->label('Catat Bayar')
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->color('success')
                    ->schema(fn (Invoice $record): array => [
                        TextInput::make('balance_info')
                            ->label('Sisa Tagihan Tertunggak')
                            ->default('Rp'.number_format($record->balance, 0, ',', '.'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('amount')
                            ->label('Nominal Pembayaran (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default($record->balance)
                            ->minValue(1)
                            ->maxValue($record->balance)
                            ->required(),
                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options(PaymentMethod::class)
                            ->default(PaymentMethod::Cash)
                            ->native(false)
                            ->required(),
                        DatePicker::make('payment_date')
                            ->label('Tanggal Pembayaran')
                            ->default(now())
                            ->required(),
                        TextInput::make('reference_number')
                            ->label('Nomor Bukti Transfer / Referensi'),
                        FileUpload::make('proof_path')
                            ->label('Lampiran Bukti Bayar')
                            ->image()
                            ->directory('payment-proofs')
                            ->maxSize(5120),
                        Textarea::make('notes')
                            ->label('Catatan Pembayaran')
                            ->rows(2),
                    ])
                    ->action(function (Invoice $record, array $data, PaymentRecorderService $recorder): void {
                        $payment = $recorder->recordPayment(
                            invoice: $record,
                            amount: (int) $data['amount'],
                            paymentMethod: PaymentMethod::from($data['payment_method']),
                            paymentDate: $data['payment_date'],
                            referenceNumber: $data['reference_number'] ?? null,
                            proofPath: $data['proof_path'] ?? null,
                            notes: $data['notes'] ?? null,
                        );

                        Notification::make()
                            ->title('Pembayaran Berhasil Dicatat')
                            ->body('Pembayaran Rp'.number_format($payment->amount, 0, ',', '.')." berhasil dicatat ({$payment->receipt_number}).")
                            ->success()
                            ->send();
                    }),
                Action::make('send_whatsapp')
                    ->label('Kirim WA')
                    ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
                    ->color('warning')
                    ->visible(fn (Invoice $record): bool => ! empty($record->household?->phone))
                    ->url(function (Invoice $record): string {
                        $setting = AppSetting::current();
                        $phone = preg_replace('/[^0-9]/', '', (string) $record->household->phone);
                        if (str_starts_with($phone, '0')) {
                            $phone = '62'.mb_substr($phone, 1);
                        }

                        $rincian = $record->isMonthly()
                            ? 'Iuran Rutin Periode '.($record->billing_period ? Carbon::createFromFormat('Y-m', $record->billing_period)->translatedFormat('F Y') : '-')
                            : 'Iuran Pembangunan: '.($record->constructionProject?->project_type?->getLabel() ?? 'Pembangunan');

                        $dueDate = $record->due_date ? $record->due_date->translatedFormat('d M Y') : 'Segera';
                        $bankInfo = $setting->bank_name ? "Pembayaran dapat ditransfer ke:\n*{$setting->bank_name} {$setting->bank_account_number}*\na.n. {$setting->bank_account_holder}\n\n" : '';

                        $msg = "Halo Bapak/Ibu {$record->household->head_of_family} (Rumah {$record->household->house_code}),\n\nKami menginformasikan bahwa tagihan iuran berikut belum terselesaikan:\n- Kewajiban: *{$rincian}*\n- Sisa Tunggakan: *Rp".number_format($record->balance, 0, ',', '.')."*\n- Jatuh Tempo: {$dueDate}\n\n{$bankInfo}Mohon segera melakukan konfirmasi atau pelunasan iuran untuk kelancaran bersama di {$setting->complex_name}.\n\nTerima kasih banyak atas perhatian dan kerja samanya.";

                        return 'https://wa.me/'.$phone.'?text='.rawurlencode($msg);
                    })
                    ->openUrlInNewTab(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Cetak Rekap Fisik (A4)')
                ->icon(Heroicon::OutlinedPrinter)
                ->color('primary')
                ->url(fn (): string => route('reports.print.outstanding'))
                ->openUrlInNewTab(),
        ];
    }
}
