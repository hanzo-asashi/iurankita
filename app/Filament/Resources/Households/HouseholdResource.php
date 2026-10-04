<?php

declare(strict_types=1);

namespace App\Filament\Resources\Households;

use App\Filament\Resources\Households\Pages\CreateHousehold;
use App\Filament\Resources\Households\Pages\EditHousehold;
use App\Filament\Resources\Households\Pages\ListHouseholds;
use App\Filament\Resources\Households\Schemas\HouseholdForm;
use App\Filament\Resources\Households\Tables\HouseholdsTable;
use App\Models\Household;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class HouseholdResource extends Resource
{
    protected static ?string $model = Household::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?string $navigationLabel = 'KK / Rumah';

    protected static ?string $modelLabel = 'Rumah';

    protected static ?string $pluralModelLabel = 'KK / Rumah';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'house_code';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'house_code',
            'head_of_family',
            'block',
            'house_number',
            'phone',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return HouseholdForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HouseholdsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\InvoicesRelationManager::class,
            RelationManagers\ConstructionProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHouseholds::route('/'),
            'create' => CreateHousehold::route('/create'),
            'view' => Pages\ViewHousehold::route('/{record}'),
            'edit' => EditHousehold::route('/{record}/edit'),
        ];
    }
}
