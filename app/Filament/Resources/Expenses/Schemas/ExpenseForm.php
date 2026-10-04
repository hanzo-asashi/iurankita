<?php

declare(strict_types=1);

namespace App\Filament\Resources\Expenses\Schemas;

use App\Enums\ExpenseCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Catat Pengeluaran Kas Lingkungan')
                    ->description('Catat arus kas keluar untuk operasional kompleks, kebersihan, keamanan, atau pemeliharaan lingkungan.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('category')
                                ->label('Kategori Pengeluaran')
                                ->options(ExpenseCategory::class)
                                ->default(ExpenseCategory::WasteManagement)
                                ->native(false)
                                ->required(),
                            TextInput::make('title')
                                ->label('Keperluan / Judul Pengeluaran')
                                ->placeholder('Contoh: Honor Angkut Sampah Minggu ke-1')
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('amount')
                                ->label('Nominal Pengeluaran (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->minValue(1)
                                ->required(),
                            DatePicker::make('expense_date')
                                ->label('Tanggal Pengeluaran')
                                ->default(now())
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('recipient')
                                ->label('Penerima Dana / Toko')
                                ->placeholder('Contoh: Pak Supri (Petugas Kebersihan)'),
                            FileUpload::make('receipt_path')
                                ->label('Nota / Kwitansi / Struk Belanja')
                                ->image()
                                ->directory('expense-receipts')
                                ->maxSize(5120)
                                ->helperText('Lampirkan foto struk/nota pembelian bila ada.'),
                        ]),
                        Textarea::make('notes')
                            ->label('Catatan / Keterangan Tambahan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
