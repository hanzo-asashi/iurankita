<?php

declare(strict_types=1);

namespace App\Filament\Resources\ConstructionProjects\Schemas;

use App\Enums\ConstructionStatus;
use App\Enums\ConstructionType;
use App\Models\FeeRate;
use App\Models\Household;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ConstructionProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        $defaultFee = FeeRate::getActiveRate('construction_one_time')?->amount ?? 100000;

        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Proyek Pembangunan')
                    ->description('Iuran pembangunan hanya dikenakan SATU KALI untuk setiap kegiatan pembangunan, dan BUKAN iuran bulanan.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('household_id')
                                ->label('Rumah / Warga')
                                ->relationship('household', 'house_code')
                                ->getOptionLabelFromRecordUsing(fn (Household $h): string => "{$h->house_code} - {$h->head_of_family} (Blok {$h->block}/{$h->house_number})")
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->required(),
                            Select::make('project_type')
                                ->label('Jenis Kegiatan Pembangunan')
                                ->options(ConstructionType::class)
                                ->default(ConstructionType::Kitchen)
                                ->native(false)
                                ->required(),
                        ]),
                        Grid::make(3)->schema([
                            DatePicker::make('start_date')
                                ->label('Tanggal Mulai')
                                ->default(now())
                                ->required(),
                            DatePicker::make('completion_date')
                                ->label('Tanggal Selesai')
                                ->afterOrEqual('start_date'),
                            Select::make('status')
                                ->label('Status Pembangunan')
                                ->options(ConstructionStatus::class)
                                ->default(ConstructionStatus::Active)
                                ->helperText('Jika Aktif, sistem akan menerbitkan 1 invoice pembangunan.')
                                ->native(false)
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('one_time_fee')
                                ->label('Iuran Pembangunan (Sekali Bayar)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default($defaultFee)
                                ->helperText('Tarif sekali bayar per kegiatan (default: Rp100.000).')
                                ->required(),
                        ]),
                        Textarea::make('description')
                            ->label('Deskripsi Pembangunan')
                            ->placeholder('Contoh: Pembangunan dapur belakang ukuran 3x4 meter')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
