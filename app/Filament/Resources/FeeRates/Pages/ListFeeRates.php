<?php

declare(strict_types=1);

namespace App\Filament\Resources\FeeRates\Pages;

use App\Filament\Resources\FeeRates\FeeRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListFeeRates extends ListRecords
{
    protected static string $resource = FeeRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Tarif Iuran'),
        ];
    }
}
