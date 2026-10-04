<?php

declare(strict_types=1);

namespace App\Filament\Resources\Expenses\Tables;

use App\Enums\ExpenseCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

final class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('expense_date', 'desc')
            ->columns([
                TextColumn::make('expense_number')
                    ->label('No. Pengeluaran')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Keperluan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Nominal Keluar')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->color('danger')
                    ->weight('bold')
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total Keluar: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('expense_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('recipient')
                    ->label('Penerima / Vendor')
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('creator.name')
                    ->label('Dicatat Oleh')
                    ->placeholder('Sistem')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->emptyStateHeading('Belum Ada Pengeluaran Kas')
            ->emptyStateDescription('Catat pengeluaran operasional lingkungan (sampah, keamanan, perbaikan fasilitas) untuk transparansi kas.')
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori Pengeluaran')
                    ->options(ExpenseCategory::class)
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([]);
    }
}
