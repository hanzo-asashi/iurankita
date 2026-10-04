<?php

declare(strict_types=1);

namespace App\Filament\Resources\FeeRates\Schemas;

use Filament\Forms\Components\DatePicker;
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
                            TextInput::make('name')
                                ->label('Nama Tarif')
                                ->placeholder('Contoh: Iuran Rutin Rumah Dihuni / Iuran Pengelolaan Sampah')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $operation, ?string $state, \Filament\Schemas\Components\Utilities\Set $set, \Filament\Schemas\Components\Utilities\Get $get): void {
                                    if ($operation === 'create' && blank($get('code')) && filled($state)) {
                                        $set('code', \Illuminate\Support\Str::slug($state, '_'));
                                    }
                                }),
                            TextInput::make('code')
                                ->label('Kode Tarif')
                                ->placeholder('Contoh: monthly_occupied, iuran_sampah, keamanan')
                                ->helperText('Kode unik identitas tarif (huruf kecil & garis bawah). Preset bawaan: monthly_occupied, monthly_unoccupied, construction_one_time.')
                                ->datalist([
                                    'monthly_occupied',
                                    'monthly_unoccupied',
                                    'construction_one_time',
                                    'iuran_sampah',
                                    'iuran_keamanan',
                                    'dana_sosial',
                                    'iuran_kegiatan',
                                ])
                                ->required()
                                ->maxLength(50),
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
