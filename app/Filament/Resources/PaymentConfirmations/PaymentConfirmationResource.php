<?php

declare(strict_types=1);

namespace App\Filament\Resources\PaymentConfirmations;

use App\Enums\PaymentConfirmationStatus;
use App\Enums\PaymentMethod;
use App\Filament\Resources\PaymentConfirmations\Pages\ListPaymentConfirmations;
use App\Models\PaymentConfirmation;
use App\Services\Payment\PaymentRecorderService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

final class PaymentConfirmationResource extends Resource
{
    protected static ?string $model = PaymentConfirmation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Verifikasi Pembayaran';

    protected static ?string $modelLabel = 'Konfirmasi Pembayaran';

    protected static ?string $pluralModelLabel = 'Verifikasi Pembayaran Warga';

    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        $count = PaymentConfirmation::query()
            ->where('status', PaymentConfirmationStatus::Pending)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['household', 'invoice']))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal Kirim')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('household.house_code')
                    ->label('Rumah')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('household.head_of_family')
                    ->label('Kepala Keluarga')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('invoice.invoice_number')
                    ->label('No. Tagihan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->weight('bold')
                    ->color('success')
                    ->sortable(),
                TextColumn::make('sender_bank')
                    ->label('Bank & Pengirim')
                    ->description(fn (PaymentConfirmation $record): string => $record->sender_name)
                    ->searchable(),
                ImageColumn::make('proof_path')
                    ->label('Bukti Transfer')
                    ->disk('public')
                    ->square()
                    ->openUrlInNewTab(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('verifier.name')
                    ->label('Diverifikasi Oleh')
                    ->placeholder('-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Verifikasi')
                    ->options(PaymentConfirmationStatus::class)
                    ->default(PaymentConfirmationStatus::Pending->value)
                    ->native(false),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui (Approve)')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (PaymentConfirmation $record): bool => $record->isPending())
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pembayaran Warga')
                    ->modalDescription(fn (PaymentConfirmation $record): string => 'Konfirmasi dan catat pembayaran sebesar Rp'.number_format($record->amount, 0, ',', '.')." untuk Rumah {$record->household->house_code}?")
                    ->form([
                        Select::make('payment_method')
                            ->label('Metode Pembayaran Masuk')
                            ->options([
                                PaymentMethod::Transfer->value => 'Transfer Bank',
                                PaymentMethod::Qris->value => 'QRIS',
                                PaymentMethod::Cash->value => 'Tunai',
                            ])
                            ->default(PaymentMethod::Transfer->value)
                            ->required()
                            ->native(false),
                        TextInput::make('reference_number')
                            ->label('Nomor Referensi / Trace No.')
                            ->placeholder('Opsional (dari bukti transfer)'),
                    ])
                    ->action(function (PaymentConfirmation $record, array $data, PaymentRecorderService $recorder): void {
                        $payment = $recorder->recordPayment(
                            invoice: $record->invoice,
                            amount: $record->amount,
                            paymentMethod: PaymentMethod::from($data['payment_method']),
                            paymentDate: $record->payment_date,
                            receivedBy: auth()->id(),
                            referenceNumber: $data['reference_number'] ?? null,
                            proofPath: $record->proof_path,
                            notes: "Diverifikasi otomatis dari konfirmasi mandiri portal warga (Pengirim: {$record->sender_name} - {$record->sender_bank})."
                        );

                        $record->update([
                            'status' => PaymentConfirmationStatus::Approved,
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Pembayaran Berhasil Disetujui')
                            ->body("Kwitansi {$payment->receipt_number} telah diterbitkan dan saldo tagihan warga telah lunas/berkurang.")
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (PaymentConfirmation $record): bool => $record->isPending())
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Konfirmasi Pembayaran')
                    ->modalDescription('Bukti pembayaran tidak valid, mutasi tidak ditemukan, atau nominal tidak sesuai.')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->placeholder('Contoh: Mutasi dana di rekening kas RT belum masuk, atau foto bukti transfer buram/tidak terbaca.')
                            ->required(),
                    ])
                    ->action(function (PaymentConfirmation $record, array $data): void {
                        $record->update([
                            'status' => PaymentConfirmationStatus::Rejected,
                            'rejection_reason' => $data['rejection_reason'],
                            'verified_by' => auth()->id(),
                            'verified_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Konfirmasi Pembayaran Ditolak')
                            ->body('Status konfirmasi telah diperbarui menjadi ditolak.')
                            ->warning()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentConfirmations::route('/'),
        ];
    }
}
