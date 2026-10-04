<?php

declare(strict_types=1);

namespace App\Filament\Resources\FeeRates;

use App\Filament\Resources\FeeRates\Pages\CreateFeeRate;
use App\Filament\Resources\FeeRates\Pages\EditFeeRate;
use App\Filament\Resources\FeeRates\Pages\ListFeeRates;
use App\Filament\Resources\FeeRates\Schemas\FeeRateForm;
use App\Filament\Resources\FeeRates\Tables\FeeRatesTable;
use App\Models\FeeRate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class FeeRateResource extends Resource
{
    protected static ?string $model = FeeRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?string $navigationLabel = 'Tarif Iuran';

    protected static ?string $modelLabel = 'Tarif Iuran';

    protected static ?string $pluralModelLabel = 'Tarif Iuran';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return FeeRateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FeeRatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeeRates::route('/'),
            'create' => CreateFeeRate::route('/create'),
            'edit' => EditFeeRate::route('/{record}/edit'),
        ];
    }
}
