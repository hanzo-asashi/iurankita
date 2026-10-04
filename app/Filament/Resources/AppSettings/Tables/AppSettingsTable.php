<?php

declare(strict_types=1);

namespace App\Filament\Resources\AppSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class AppSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('app_name')
                    ->label('Nama Aplikasi')
                    ->weight('bold'),
                TextColumn::make('complex_name')
                    ->label('Nama Kompleks'),
                TextColumn::make('phone')
                    ->label('Telepon'),
                TextColumn::make('email')
                    ->label('Email'),
                TextColumn::make('monthly_due_day')
                    ->label('Jatuh Tempo Bulanan')
                    ->formatStateUsing(fn (int $state): string => "Tanggal {$state}"),
            ])
            ->emptyStateHeading('Belum Ada Pengaturan')
            ->emptyStateDescription('Pengaturan profil perumahan dan aplikasi akan ditampilkan di sini.')
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
