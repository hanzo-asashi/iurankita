<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Household;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Rincian Tagihan')
                    ->description('Data rincian tagihan iuran warga')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('invoice_number')
                                ->label('Nomor Invoice')
                                ->disabled()
                                ->dehydrated(false),
                            Select::make('invoice_type')
                                ->label('Jenis Tagihan')
                                ->options(InvoiceType::class)
                                ->native(false)
                                ->disabled(),
                            Select::make('status')
                                ->label('Status Tagihan')
                                ->options(InvoiceStatus::class)
                                ->native(false)
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('household_id')
                                ->label('Rumah / Warga')
                                ->relationship('household', 'house_code')
                                ->getOptionLabelFromRecordUsing(fn (Household $h): string => "{$h->house_code} - {$h->head_of_family}")
                                ->native(false)
                                ->disabled(),
                            TextInput::make('billing_period')
                                ->label('Periode Tagihan')
                                ->placeholder('Contoh: 2026-09')
                                ->disabled(fn (string $operation): bool => $operation !== 'create'),
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('issue_date')
                                ->label('Tanggal Terbit')
                                ->required(),
                            DatePicker::make('due_date')
                                ->label('Jatuh Tempo')
                                ->required(),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('total_amount')
                                ->label('Total Tagihan')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled(),
                            TextInput::make('amount_paid')
                                ->label('Telah Dibayar')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled(),
                            TextInput::make('balance')
                                ->label('Sisa Tagihan')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled(),
                        ]),
                        Textarea::make('notes')
                            ->label('Catatan Tagihan')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
