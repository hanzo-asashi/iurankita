<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentMethod;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['invoice.household', 'receiver']))
            ->columns([
                TextColumn::make('receipt_number')
                    ->label('No. Kwitansi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('invoice.invoice_number')
                    ->label('No. Tagihan')
                    ->searchable()
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
                    ->label('Jenis Iuran')
                    ->badge()
                    ->sortable(),
                TextColumn::make('payment_date')
                    ->label('Tanggal Bayar')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->sortable(),
                TextColumn::make('receiver.name')
                    ->label('Petugas')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Pembayaran')
            ->emptyStateDescription('Catat pembayaran iuran dari warga untuk menerbitkan kwitansi sah.')
            ->filters([
                SelectFilter::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options(PaymentMethod::class)
                    ->native(false),
            ])
            ->recordActions([
                Action::make('print')
                    ->label('Cetak')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('primary')
                    ->url(fn (Payment $record): string => route('receipt.print', $record))
                    ->openUrlInNewTab(),
                Action::make('whatsapp')
                    ->label('Kirim WA')
                    ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
                    ->color('success')
                    ->visible(fn (Payment $record): bool => ! empty($record->invoice?->household?->phone))
                    ->url(function (Payment $record): ?string {
                        $phone = $record->invoice?->household?->phone;
                        if (! $phone) {
                            return null;
                        }

                        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '62'.mb_substr($cleanPhone, 1);
                        }

                        $resident = $record->invoice->household->head_of_family;
                        $houseCode = $record->invoice->household->house_code;
                        $receiptUrl = route('receipt.print', $record);
                        $amount = number_format($record->amount, 0, ',', '.');
                        $date = $record->payment_date->translatedFormat('d F Y');
                        $type = $record->invoice->invoice_type->getLabel();

                        $text = "Halo Bpk/Ibu *{$resident}* ({$houseCode}),\n\nTerima kasih, pembayaran *{$type}* sebesar *Rp{$amount}* pada tanggal *{$date}* telah kami terima.\n\nNo. Kwitansi: *{$record->receipt_number}*\nLihat/unduh kwitansi digital resmi Anda di:\n{$receiptUrl}\n\nSalam hormat,\nPengurus Lingkungan Del Mattappa Residence";

                        return 'https://wa.me/'.$cleanPhone.'?text='.rawurlencode($text);
                    })
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([]);
    }
}
