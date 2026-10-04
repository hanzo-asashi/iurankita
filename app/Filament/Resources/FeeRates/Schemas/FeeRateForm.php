<?php

declare(strict_types=1);

namespace App\Filament\Resources\FeeRates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class FeeRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Ketentuan Tarif Iuran')
                    ->description('Pastikan nama dan nominal mencerminkan sifat pembayaran (bulanan atau sekali bayar).')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('code')
                                ->label('Kode Tarif')
                                ->options([
                                    'monthly_occupied' => 'Iuran Rutin Rumah Dihuni (Bulanan)',
                                    'monthly_unoccupied' => 'Iuran Rutin Rumah Belum Dihuni (Bulanan)',
                                    'construction_one_time' => 'Iuran Pembangunan (Sekali Bayar)',
                                ])
                                ->native(false)
                                ->required(),
                            TextInput::make('name')
                                ->label('Nama Tarif')
                                ->placeholder('Contoh: Iuran Pembangunan — Sekali Bayar')
                                ->required()
                                ->maxLength(255),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('amount')
                                ->label('Nominal Tarif (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->minValue(0)
                                ->required(),
                            DatePicker::make('effective_from')
                                ->label('Berlaku Mulai')
                                ->default(now())
                                ->required(),
                            DatePicker::make('effective_until')
                                ->label('Berlaku Sampai')
                                ->helperText('Kosongkan jika berlaku seterusnya tanpa batas waktu.'),
                        ]),
                        Textarea::make('description')
                            ->label('Deskripsi / Catatan Aturan')
                            ->rows(2)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Tarif Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}
