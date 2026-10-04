<?php

declare(strict_types=1);

namespace App\Filament\Resources\ConstructionProjects;

use App\Filament\Resources\ConstructionProjects\Pages\CreateConstructionProject;
use App\Filament\Resources\ConstructionProjects\Pages\EditConstructionProject;
use App\Filament\Resources\ConstructionProjects\Pages\ListConstructionProjects;
use App\Filament\Resources\ConstructionProjects\Schemas\ConstructionProjectForm;
use App\Filament\Resources\ConstructionProjects\Tables\ConstructionProjectsTable;
use App\Models\ConstructionProject;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

final class ConstructionProjectResource extends Resource
{
    protected static ?string $model = ConstructionProject::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?string $navigationLabel = 'Pembangunan';

    protected static ?string $modelLabel = 'Pembangunan';

    protected static ?string $pluralModelLabel = 'Data Pembangunan';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ConstructionProjectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConstructionProjectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConstructionProjects::route('/'),
            'create' => CreateConstructionProject::route('/create'),
            'edit' => EditConstructionProject::route('/{record}/edit'),
        ];
    }
}
