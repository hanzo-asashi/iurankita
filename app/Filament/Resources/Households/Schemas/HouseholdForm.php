<?php

declare(strict_types=1);

namespace App\Filament\Resources\Households\Schemas;

use App\Enums\OccupancyStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class HouseholdForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Rumah & KK')
                    ->description('Data identitas rumah dan kepala keluarga')
                    ->columnSpanFull()
                    ->headerActions([
                        \Filament\Actions\Action::make('view_denah')
                            ->label('Lihat Denah Kawasan')
                            ->icon('heroicon-o-map')
                            ->color('info')
                            ->modalHeading('Denah Kawasan Del Mattappa Residence')
                            ->modalDescription('Panduan tata letak nomor blok dan rumah')
                            ->modalContent(view('filament.components.denah-modal'))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Tutup'),
                    ])
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('house_code')
                                ->label('Kode Rumah')
                                ->placeholder('Contoh: A-01')
                                ->unique(ignoreRecord: true)
                                ->required()
                                ->maxLength(20),
                            TextInput::make('block')
                                ->label('Blok')
                                ->placeholder('Contoh: A')
                                ->required()
                                ->maxLength(10),
                            TextInput::make('house_number')
                                ->label('Nomor Rumah')
                                ->placeholder('Contoh: 01')
                                ->required()
                                ->maxLength(10),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('head_of_family')
                                ->label('Nama Kepala Keluarga')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('kk_number')
                                ->label('Nomor Kartu Keluarga (KK)')
                                ->maxLength(30),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('phone')
                                ->label('Nomor Telepon / WhatsApp')
                                ->tel()
                                ->maxLength(20),
                            Select::make('occupancy_status')
                                ->label('Status Hunian')
                                ->options(OccupancyStatus::class)
                                ->default(OccupancyStatus::Occupied)
                                ->helperText('Dihuni: Rp50.000/bln | Belum Dihuni: Rp35.000/bln')
                                ->native(false)
                                ->required(),
                        ]),
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->rows(2)
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Rumah Aktif')
                            ->default(true)
                            ->helperText('Hanya rumah aktif yang masuk dalam tagihan rutin bulanan.'),
                    ]),
            ]);
    }
}
