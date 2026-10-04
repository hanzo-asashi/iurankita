<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Services\Payment\PaymentRecorderService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('household'))
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Nomor Invoice')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('invoice_type')
                    ->label('Jenis Tagihan')
                    ->badge()
                    ->sortable(),
                TextColumn::make('household.house_code')
                    ->label('Rumah')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('household.head_of_family')
                    ->label('Kepala Keluarga')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('billing_period')
                    ->label('Periode')
                    ->formatStateUsing(function (?string $state, Invoice $record): string {
                        if ($record->isConstruction()) {
                            return 'Sekali Bayar';
                        }
                        if ($record->isSpecial()) {
                            return 'Insidental / Acara';
                        }
                        if (! $state) {
                            return '-';
                        }

                        return Carbon::createFromFormat('Y-m', $state)->translatedFormat('F Y');
                    })
                    ->sortable(),
                TextColumn::make('issue_date')
                    ->label('Tgl Terbit')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('amount_paid')
                    ->label('Dibayar')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->color('success'),
                TextColumn::make('balance')
                    ->label('Sisa')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->weight('bold'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Tagihan')
            ->emptyStateDescription('Terbitkan tagihan iuran rutin bulanan atau iuran pembangunan untuk warga.')
            ->filters([
                SelectFilter::make('invoice_type')
                    ->label('Jenis Tagihan')
                    ->options(InvoiceType::class)
                    ->native(false),
                SelectFilter::make('status')
                    ->label('Status Tagihan')
                    ->options(InvoiceStatus::class)
                    ->native(false),
                Filter::make('block')
                    ->schema([
                        TextInput::make('block_name')
                            ->label('Filter Blok')
                            ->placeholder('Contoh: A'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['block_name'])) {
                            return $query;
                        }

                        return $query->whereHas('household', fn (Builder $q) => $q->where('block', 'like', "%{$data['block_name']}%"));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('pay')
                    ->label('Catat Bayar')
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->color('success')
                    ->visible(fn (Invoice $record): bool => $record->balance > 0 && $record->status !== InvoiceStatus::Cancelled)
                    ->schema(fn (Invoice $record): array => [
                        TextInput::make('balance_info')
                            ->label('Sisa Tagihan yang Harus Dibayar')
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
                        \Filament\Forms\Components\FileUpload::make('proof_path')
                            ->label('Lampiran Struk / Bukti Bayar')
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
                            ->title('Pembayaran Berhasil')
                            ->body('Pembayaran Rp'.number_format($payment->amount, 0, ',', '.')." berhasil dicatat dengan nomor kwitansi {$payment->receipt_number}.")
                            ->success()
                            ->send();
                    }),
                Action::make('send_whatsapp')
                    ->label('Kirim WA')
                    ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
                    ->color('warning')
                    ->visible(fn (Invoice $record): bool => $record->balance > 0 && ! empty($record->household?->phone))
                    ->url(function (Invoice $record): string {
                        $setting = \App\Models\AppSetting::current();
                        $phone = preg_replace('/[^0-9]/', '', (string) $record->household->phone);
                        if (str_starts_with($phone, '0')) {
                            $phone = '62'.mb_substr($phone, 1);
                        }

                        $rincian = match (true) {
                            $record->isMonthly() => 'Iuran Rutin Periode '.($record->billing_period ? Carbon::createFromFormat('Y-m', $record->billing_period)->translatedFormat('F Y') : '-'),
                            $record->isSpecial() => 'Iuran Khusus / Kegiatan: '.($record->notes ?? 'Insidental'),
                            default => 'Iuran Pembangunan: '.($record->constructionProject?->project_type?->getLabel() ?? 'Pembangunan (1x Sekali Bayar)'),
                        };

                        $dueDate = $record->due_date ? $record->due_date->translatedFormat('d M Y') : 'Segera';
                        $bankInfo = $setting->bank_name ? "Pembayaran dapat ditransfer ke:\n*{$setting->bank_name} {$setting->bank_account_number}*\na.n. {$setting->bank_account_holder}\n\n" : '';

                        $msg = "Halo Bapak/Ibu {$record->household->head_of_family} (Rumah {$record->household->house_code}),\n\nKami dari Pengurus {$setting->complex_name} menginformasikan tagihan iuran:\n- Tagihan: *{$rincian}*\n- Nominal: *Rp".number_format($record->balance, 0, ',', '.')."*\n- Jatuh Tempo: {$dueDate}\n\n{$bankInfo}Mohon konfirmasi dan kirimkan bukti transfer setelah pembayaran.\n\nTerima kasih atas partisipasi aktif Bapak/Ibu.";

                        return 'https://wa.me/'.$phone.'?text='.rawurlencode($msg);
                    })
                    ->openUrlInNewTab(),
            ]);
    }
}
