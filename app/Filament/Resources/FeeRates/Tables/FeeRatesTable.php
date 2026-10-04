<?php

declare(strict_types=1);

namespace App\Filament\Resources\FeeRates\Tables;

use App\Models\FeeRate;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class FeeRatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Tarif')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('Kode Tarif')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'monthly_occupied' => 'success',
                        'monthly_unoccupied' => 'info',
                        'construction_one_time' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(function (int $state, FeeRate $record): string {
                        $rupiah = 'Rp'.number_format($state, 0, ',', '.');

                        return match (true) {
                            $record->code === 'construction_one_time' => "{$rupiah} (sekali bayar)",
                            str_contains($record->code, 'monthly') => "{$rupiah} / bulan",
                            default => $rupiah,
                        };
                    })
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('effective_from')
                    ->label('Mulai Berlaku')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('effective_until')
                    ->label('Sampai Dengan')
                    ->date('d M Y')
                    ->placeholder('Selamanya'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter()
                    ->sortable(),
            ])
            ->emptyStateHeading('Belum Ada Tarif Iuran')
            ->emptyStateDescription('Konfigurasi tarif iuran bulanan dan iuran pembangunan akan tampil di sini.')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
