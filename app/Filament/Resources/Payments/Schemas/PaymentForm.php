<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentMethod;
use App\Models\Invoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Catat Pembayaran Iuran')
                    ->description('Pembayaran dapat dilakukan untuk tagihan bulanan maupun tagihan pembangunan.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('invoice_id')
                                ->label('Pilih Tagihan')
                                ->relationship(
                                    name: 'invoice',
                                    titleAttribute: 'invoice_number',
                                    modifyQueryUsing: fn (Builder $query, ?Model $record) => $query
                                        ->where(fn (Builder $q) => $q->where('balance', '>', 0)->orWhere('id', $record?->invoice_id))
                                        ->with('household')
                                )
                                ->getOptionLabelFromRecordUsing(fn (Invoice $i): string => "{$i->invoice_number} - {$i->household->house_code} ({$i->household->head_of_family}) - Sisa: Rp".number_format($i->balance, 0, ',', '.'))
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(function (?int $state, \Filament\Schemas\Components\Utilities\Set $set): void {
                                    if ($state) {
                                        $inv = Invoice::find($state);
                                        if ($inv) {
                                            $set('amount', $inv->balance);
                                        }
                                    }
                                })
                                ->required(),
                            TextInput::make('amount')
                                ->label('Nominal Pembayaran (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->minValue(1)
                                ->helperText('Nominal pembayaran default otomatis terisi dengan sisa saldo tagihan.')
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('payment_method')
                                ->label('Metode Pembayaran')
                                ->options(PaymentMethod::class)
                                ->default(PaymentMethod::Cash)
                                ->native(false)
                                ->required(),
                            DatePicker::make('payment_date')
                                ->label('Tanggal Pembayaran')
                                ->default(now())
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('reference_number')
                                ->label('Nomor Bukti / Referensi Transfer'),
                            Textarea::make('notes')
                                ->label('Catatan Pembayaran')
                                ->rows(2),
                        ]),
                        \Filament\Forms\Components\FileUpload::make('proof_path')
                            ->label('Bukti Pembayaran / Slip Transfer (Foto/PDF)')
                            ->image()
                            ->directory('payment-proofs')
                            ->maxSize(5120)
                            ->helperText('Unggah foto slip transfer, struk ATM, atau bukti screenshot pembayaran bila ada.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
