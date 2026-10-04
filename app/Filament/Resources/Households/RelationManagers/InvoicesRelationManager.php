<?php

declare(strict_types=1);

namespace App\Filament\Resources\Households\RelationManagers;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Invoice;
use App\Services\Payment\PaymentRecorderService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Riwayat Tagihan Iuran';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('invoice_type')
                    ->label('Jenis Tagihan')
                    ->badge(),
                TextColumn::make('billing_period')
                    ->label('Periode')
                    ->formatStateUsing(function (?string $state, Invoice $record): string {
                        if ($record->isConstruction()) {
                            return 'Sekali Bayar';
                        }

                        return $state ? Carbon::createFromFormat('Y-m', $state)->translatedFormat('F Y') : '-';
                    }),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.')),
                TextColumn::make('amount_paid')
                    ->label('Dibayar')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->color('success'),
                TextColumn::make('balance')
                    ->label('Sisa')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->weight('bold'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->headerActions([])
            ->recordActions([
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
                            ->title('Pembayaran Berhasil')
                            ->body('Pembayaran Rp'.number_format($payment->amount, 0, ',', '.')." berhasil dicatat ({$payment->receipt_number}).")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }
}
