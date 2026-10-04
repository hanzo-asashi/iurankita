<?php

declare(strict_types=1);

namespace App\Filament\Resources\ConstructionProjects\Tables;

use App\Enums\ConstructionStatus;
use App\Enums\ConstructionType;
use App\Enums\PaymentMethod;
use App\Models\ConstructionProject;
use App\Services\Construction\ConstructionProjectService;
use App\Services\Payment\PaymentRecorderService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class ConstructionProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['household', 'invoice']))
            ->columns([
                TextColumn::make('household.house_code')
                    ->label('Rumah')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('household.head_of_family')
                    ->label('Kepala Keluarga')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project_type')
                    ->label('Jenis Kegiatan')
                    ->badge()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('completion_date')
                    ->label('Tanggal Selesai')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('one_time_fee')
                    ->label('Iuran (Sekali Bayar)')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Status Pembayaran')
                    ->state(function (ConstructionProject $record): string {
                        if (! $record->invoice) {
                            return 'Belum Ada Tagihan';
                        }

                        return $record->invoice->status->getLabel();
                    })
                    ->badge()
                    ->color(function (ConstructionProject $record): string {
                        if (! $record->invoice) {
                            return 'gray';
                        }

                        return $record->invoice->status->getColor() ?? 'gray';
                    }),
            ])
            ->defaultSort('start_date', 'desc')
            ->emptyStateHeading('Belum Ada Proyek Pembangunan')
            ->emptyStateDescription('Catat proyek renovasi atau pembangunan baru untuk rumah warga.')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pembangunan')
                    ->options(ConstructionStatus::class)
                    ->native(false),
                SelectFilter::make('project_type')
                    ->label('Jenis Pembangunan')
                    ->options(ConstructionType::class)
                    ->native(false),
            ])
            ->recordActions([
                Action::make('start')
                    ->label('Mulai Pembangunan')
                    ->icon(Heroicon::OutlinedPlay)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Mulai Proyek Pembangunan')
                    ->modalDescription('Proyek akan diaktifkan dan SATU invoice iuran pembangunan sekali bayar akan otomatis diterbitkan.')
                    ->visible(fn (ConstructionProject $record): bool => $record->status === ConstructionStatus::Planned)
                    ->action(function (ConstructionProject $record, ConstructionProjectService $service): void {
                        $invoice = $service->startProject($record);
                        Notification::make()
                            ->title('Pembangunan Dimulai')
                            ->body("Pembangunan berhasil dicatat. Invoice iuran pembangunan {$invoice->invoice_number} telah dibuat sebesar Rp".number_format($record->one_time_fee, 0, ',', '.').'.')
                            ->success()
                            ->send();
                    }),

                Action::make('complete')
                    ->label('Tandai Selesai')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (ConstructionProject $record): bool => $record->status === ConstructionStatus::Active)
                    ->schema([
                        DatePicker::make('completion_date')
                            ->label('Tanggal Selesai')
                            ->default(now())
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan Selesai')
                            ->rows(2),
                    ])
                    ->action(function (ConstructionProject $record, array $data, ConstructionProjectService $service): void {
                        $service->completeProject($record, $data['completion_date'], $data['notes'] ?? null);
                        Notification::make()
                            ->title('Pembangunan Selesai')
                            ->body('Pembangunan telah ditandai selesai. Tidak ada tagihan pembangunan tambahan.')
                            ->success()
                            ->send();
                    }),

                Action::make('pay')
                    ->label('Catat Bayar')
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->color('info')
                    ->visible(fn (ConstructionProject $record): bool => $record->invoice !== null && $record->invoice->balance > 0)
                    ->schema(fn (ConstructionProject $record): array => [
                        TextInput::make('balance_info')
                            ->label('Sisa Tagihan Pembangunan')
                            ->default('Rp'.number_format($record->invoice->balance, 0, ',', '.'))
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('amount')
                            ->label('Nominal Pembayaran (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default($record->invoice->balance)
                            ->minValue(1)
                            ->maxValue($record->invoice->balance)
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
                            ->label('Nomor Referensi / Bukti'),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2),
                    ])
                    ->action(function (ConstructionProject $record, array $data, PaymentRecorderService $recorder): void {
                        $payment = $recorder->recordPayment(
                            invoice: $record->invoice,
                            amount: (int) $data['amount'],
                            paymentMethod: PaymentMethod::from($data['payment_method']),
                            paymentDate: $data['payment_date'],
                            referenceNumber: $data['reference_number'] ?? null,
                            notes: $data['notes'] ?? null,
                        );

                        Notification::make()
                            ->title('Pembayaran Berhasil')
                            ->body('Pembayaran Rp'.number_format($payment->amount, 0, ',', '.')." berhasil dicatat dengan kwitansi {$payment->receipt_number}.")
                            ->success()
                            ->send();
                    }),

                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Proyek Pembangunan')
                    ->modalDescription('Proyek pembangunan akan dibatalkan. Tagihan terkait yang belum dibayar akan otomatis dibatalkan.')
                    ->visible(fn (ConstructionProject $record): bool => in_array($record->status, [ConstructionStatus::Planned, ConstructionStatus::Active], true) && ($record->invoice === null || $record->invoice->amount_paid === 0))
                    ->schema([
                        Textarea::make('reason')
                            ->label('Alasan Pembatalan')
                            ->required(),
                    ])
                    ->action(function (ConstructionProject $record, array $data, ConstructionProjectService $service): void {
                        $service->cancelProject($record, $data['reason']);
                        Notification::make()
                            ->title('Proyek Dibatalkan')
                            ->body('Proyek pembangunan berhasil dibatalkan.')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
