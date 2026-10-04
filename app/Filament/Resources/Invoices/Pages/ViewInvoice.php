<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Pages;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Services\Payment\PaymentRecorderService;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

final class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Invoice $record */
        $record = $this->getRecord();

        return [
            Action::make('pay')
                ->label('Catat Pembayaran')
                ->icon(Heroicon::OutlinedCurrencyDollar)
                ->color('success')
                ->visible(fn (): bool => $record->balance > 0 && $record->status !== InvoiceStatus::Cancelled)
                ->schema([
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
                        ->required(),
                    DatePicker::make('payment_date')
                        ->label('Tanggal Pembayaran')
                        ->default(now())
                        ->required(),
                    TextInput::make('reference_number')
                        ->label('Nomor Bukti Transfer / Referensi'),
                    Textarea::make('notes')
                        ->label('Catatan Pembayaran')
                        ->rows(2),
                ])
                ->action(function (array $data, PaymentRecorderService $recorder): void {
                    /** @var Invoice $record */
                    $record = $this->getRecord();

                    $payment = $recorder->recordPayment(
                        invoice: $record,
                        amount: (int) $data['amount'],
                        paymentMethod: PaymentMethod::from($data['payment_method']),
                        paymentDate: $data['payment_date'],
                        referenceNumber: $data['reference_number'] ?? null,
                        notes: $data['notes'] ?? null,
                    );

                    Notification::make()
                        ->title('Pembayaran Berhasil')
                        ->body('Pembayaran Rp'.number_format($payment->amount, 0, ',', '.').' berhasil dicatat.')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'amount_paid', 'balance']);
                }),
        ];
    }
}
