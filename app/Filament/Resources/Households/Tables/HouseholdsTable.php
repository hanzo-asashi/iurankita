<?php

declare(strict_types=1);

namespace App\Filament\Resources\Households\Tables;

use App\Enums\ConstructionStatus;
use App\Enums\OccupancyStatus;
use App\Models\Household;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

final class HouseholdsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount([
                'constructionProjects as active_construction_count' => fn (Builder $q) => $q->where('status', ConstructionStatus::Active),
            ]))
            ->columns([
                TextColumn::make('house_code')
                    ->label('Kode Rumah')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('block')
                    ->label('Blok')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('house_number')
                    ->label('No.')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('head_of_family')
                    ->label('Kepala Keluarga')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('occupancy_status')
                    ->label('Status Hunian')
                    ->badge()
                    ->sortable(),
                TextColumn::make('monthly_fee')
                    ->label('Tarif Iuran')
                    ->state(function (Household $record): string {
                        if ($record->monthly_fee_override) {
                            return 'Rp'.number_format($record->monthly_fee_override, 0, ',', '.').' (Khusus)';
                        }

                        return $record->occupancy_status === OccupancyStatus::Occupied ? 'Rp50.000' : 'Rp35.000';
                    })
                    ->badge()
                    ->color(fn (Household $record): string => $record->monthly_fee_override ? 'warning' : 'info'),
                TextColumn::make('construction_projects_count')
                    ->label('Total Proyek')
                    ->counts('constructionProjects')
                    ->badge()
                    ->color('gray')
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('active_construction')
                    ->label('Pembangunan Aktif')
                    ->state(fn (Household $record): string => ($record->active_construction_count ?? 0) > 0 ? "{$record->active_construction_count} Aktif" : '-')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (string $state): string => $state !== '-' ? 'warning' : 'gray'),
                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable()
                    ->placeholder('-'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->defaultSort('house_code', 'asc')
            ->emptyStateHeading('Belum Ada Data Rumah')
            ->emptyStateDescription('Tambahkan data rumah warga untuk memulai pendataan dan penagihan iuran.')
            ->filters([
                SelectFilter::make('occupancy_status')
                    ->label('Status Hunian')
                    ->options(OccupancyStatus::class)
                    ->native(false),
                TernaryFilter::make('is_active')
                    ->label('Status Rumah')
                    ->trueLabel('Hanya Rumah Aktif')
                    ->falseLabel('Hanya Rumah Nonaktif')
                    ->native(false),
                Filter::make('has_active_construction')
                    ->label('Memiliki Pembangunan Aktif')
                    ->query(fn (Builder $query): Builder => $query->whereHas(
                        'constructionProjects',
                        fn (Builder $q) => $q->where('status', ConstructionStatus::Active)
                    )),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\Action::make('advance_payment')
                    ->label('Bayar di Muka')
                    ->icon(\Filament\Support\Icons\Heroicon::OutlinedSparkles)
                    ->color('success')
                    ->modalHeading(fn (Household $record) => "Bayar Iuran di Muka — {$record->house_code} ({$record->head_of_family})")
                    ->modalDescription('Terbitkan tagihan dan langsung catat pembayaran lunas sekaligus untuk beberapa bulan ke depan.')
                    ->schema(fn (Household $record): array => [
                        \Filament\Forms\Components\Select::make('months')
                            ->label('Berapa Bulan ke Depan?')
                            ->options([
                                1 => '1 Bulan ke Depan',
                                2 => '2 Bulan ke Depan',
                                3 => '3 Bulan ke Depan',
                                6 => '6 Bulan (Setengah Tahun)',
                                12 => '12 Bulan (1 Tahun Penuh)',
                            ])
                            ->default(3)
                            ->required()
                            ->native(false)
                            ->live(),
                        \Filament\Forms\Components\Placeholder::make('rate_info')
                            ->label('Perhitungan Estimasi')
                            ->content(function (\Filament\Forms\Get $get) use ($record): string {
                                $months = (int) ($get('months') ?? 3);
                                $rate = $record->monthly_fee_override ?? ($record->occupancy_status === OccupancyStatus::Occupied ? 50000 : 35000);
                                $total = $rate * $months;
                                $label = $record->monthly_fee_override ? 'Tarif Khusus' : $record->occupancy_status->getLabel();

                                return 'Total: Rp'.number_format($total, 0, ',', '.')." ({$months} bulan x Rp".number_format($rate, 0, ',', '.')." [{$label}])";
                            }),
                        \Filament\Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options(\App\Enums\PaymentMethod::class)
                            ->default(\App\Enums\PaymentMethod::Cash)
                            ->native(false)
                            ->required(),
                        \Filament\Forms\Components\DatePicker::make('payment_date')
                            ->label('Tanggal Pembayaran')
                            ->default(now())
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('reference_number')
                            ->label('Nomor Bukti Transfer / Referensi'),
                        \Filament\Forms\Components\FileUpload::make('proof_path')
                            ->label('Bukti Pembayaran / Struk')
                            ->image()
                            ->directory('payment-proofs'),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Catatan Pembayaran')
                            ->rows(2),
                    ])
                    ->action(function (Household $record, array $data, \App\Services\Billing\AdvanceBillingService $advanceService): void {
                        $result = $advanceService->payInAdvance(
                            household: $record,
                            monthsCount: (int) $data['months'],
                            paymentMethod: \App\Enums\PaymentMethod::from($data['payment_method']),
                            paymentDate: $data['payment_date'],
                            referenceNumber: $data['reference_number'] ?? null,
                            proofPath: $data['proof_path'] ?? null,
                            notes: $data['notes'] ?? null,
                        );

                        Notification::make()
                            ->title('Pembayaran di Muka Berhasil')
                            ->body("Sebanyak {$result['invoices_count']} bulan tagihan iuran ({$record->house_code}) senilai Rp".number_format($result['total_amount'], 0, ',', '.').' berhasil dilunasi.')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make()
                    ->disabled(fn (Household $record): bool => $record->invoices()->exists())
                    ->tooltip(fn (Household $record): ?string => $record->invoices()->exists() ? 'Rumah ini memiliki riwayat tagihan dan tidak dapat dihapus.' : null),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $blocked = $records->filter(fn (Household $household): bool => $household->invoices()->exists());

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->title('Penghapusan Dibatalkan')
                                    ->body("{$blocked->count()} rumah yang dipilih memiliki riwayat tagihan/keuangan sehingga tidak dapat dihapus.")
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $records->each->delete();

                            Notification::make()
                                ->title('Berhasil Dihapus')
                                ->body('Data rumah yang dipilih berhasil dihapus.')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}
