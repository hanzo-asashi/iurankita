<?php

declare(strict_types=1);

namespace App\Filament\Resources\Households\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class ConstructionProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'constructionProjects';

    protected static ?string $title = 'Riwayat Proyek Pembangunan / Renovasi';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('project_type')
            ->defaultSort('start_date', 'desc')
            ->columns([
                TextColumn::make('project_type')
                    ->label('Jenis Pembangunan')
                    ->badge()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Deskripsi / Catatan')
                    ->limit(40)
                    ->placeholder('-'),
                TextColumn::make('start_date')
                    ->label('Tgl Mulai')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('completion_date')
                    ->label('Tgl Selesai')
                    ->date('d M Y')
                    ->placeholder('Masih Berjalan')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status Proyek')
                    ->badge()
                    ->sortable(),
                TextColumn::make('one_time_fee')
                    ->label('Iuran (1x)')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->weight('bold'),
                TextColumn::make('feeInvoice.status')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->placeholder('Belum Terbit'),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
