<?php

declare(strict_types=1);

namespace App\Filament\Resources\AppSettings\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class AppSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Identitas Perumahan & Aplikasi')
                    ->description('Pengaturan umum yang ditampilkan pada landing page dan kwitansi pembayaran.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('app_name')
                                ->label('Nama Aplikasi')
                                ->default('IuranKita')
                                ->required(),
                            TextInput::make('complex_name')
                                ->label('Nama Kompleks / Perumahan')
                                ->default('Del Mattappa Residence')
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('tagline')
                                ->label('Tagline')
                                ->default('Bayar Iuran, Tertib Administrasi, Nyaman Bertetangga.')
                                ->columnSpanFull(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('phone')
                                ->label('Nomor Telepon Pengurus')
                                ->tel(),
                            TextInput::make('email')
                                ->label('Email Pengurus')
                                ->email(),
                        ]),
                        Textarea::make('address')
                            ->label('Alamat Kompleks')
                            ->default('Jl. Kemakmuran No.45 Blok A5, Kab. Soppeng')
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('footer_text')
                            ->label('Teks Footer')
                            ->rows(2)
                            ->columnSpanFull(),
                        Grid::make(2)->schema([
                            TextInput::make('monthly_due_day')
                                ->label('Jatuh Tempo Iuran Bulanan (Tanggal)')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(28)
                                ->default(10)
                                ->helperText('Tanggal jatuh tempo tiap bulannya (contoh: tanggal 10).')
                                ->required(),
                            ColorPicker::make('primary_color')
                                ->label('Warna Tema Utama')
                                ->default('#0F766E'),
                        ]),
                    ]),
                Section::make('Informasi Rekening Bank & QRIS Pembayaran')
                    ->description('Rekening kas resmi dan barcode QRIS yang akan ditampilkan kepada warga untuk transfer iuran.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('bank_name')
                                ->label('Nama Bank')
                                ->placeholder('Contoh: Bank BRI / BCA / Mandiri'),
                            TextInput::make('bank_account_number')
                                ->label('Nomor Rekening')
                                ->placeholder('Contoh: 5012-01-002345-53-1'),
                            TextInput::make('bank_account_holder')
                                ->label('Atas Nama Rekening')
                                ->placeholder('Contoh: Kas Del Mattappa Residence'),
                        ]),
                        \Filament\Forms\Components\FileUpload::make('qris_image')
                            ->label('Gambar Barcode QRIS Kas')
                            ->image()
                            ->directory('qris')
                            ->imageEditor()
                            ->helperText('Unggah gambar QRIS statis kas perumahan untuk mempermudah warga scan barcode bayar.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
