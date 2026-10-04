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
                    ->label('Sifat Iuran')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'monthly_occupied' => 'Bulanan (Dihuni)',
                        'monthly_unoccupied' => 'Bulanan (Belum Dihuni)',
                        'construction_one_time' => 'Sekali Bayar',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'construction_one_time' => 'warning',
                        default => 'info',
                    }),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->formatStateUsing(function (int $state, FeeRate $record): string {
                        $rupiah = 'Rp'.number_format($state, 0, ',', '.');

                        return match ($record->code) {
                            'construction_one_time' => "{$rupiah} (sekali bayar)",
                            default => "{$rupiah} / bulan",
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
