<?php

declare(strict_types=1);

namespace App\Filament\Resources\AuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class AuditLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu Kejadian')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('user.name')
                    ->label('Pengguna / Petugas')
                    ->placeholder('Sistem / Otomatis')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'record_payment' => 'success',
                        'create_invoice', 'generate_monthly_invoices' => 'primary',
                        'create_construction', 'complete_construction' => 'warning',
                        'cancel_construction', 'cancel_invoice' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('notes')
                    ->label('Keterangan Aktivitas')
                    ->searchable()
                    ->wrap(),
            ])
            ->emptyStateHeading('Belum Ada Log Audit')
            ->emptyStateDescription('Seluruh aktivitas pencatatan transaksi, pembayaran, dan tagihan akan tercatat otomatis di sini.')
            ->recordActions([
                ViewAction::make()
                    ->modalHeading('Detail Log Aktivitas')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('created_at')
                                ->label('Waktu')
                                ->formatStateUsing(fn ($state) => $state?->format('d M Y H:i:s')),
                            TextInput::make('user.name')
                                ->label('Petugas / Pengguna')
                                ->placeholder('Sistem / Otomatis'),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('action')
                                ->label('Aksi'),
                            TextInput::make('model_type')
                                ->label('Tipe Model / Modul'),
                        ]),
                        Textarea::make('notes')
                            ->label('Keterangan')
                            ->rows(2)
                            ->columnSpanFull(),
                        KeyValue::make('new_values')
                            ->label('Data / Nilai Baru')
                            ->columnSpanFull(),
                    ]),
            ])
            ->toolbarActions([]);
    }
}
