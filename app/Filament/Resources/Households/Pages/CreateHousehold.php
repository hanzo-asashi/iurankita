<?php

declare(strict_types=1);

namespace App\Filament\Resources\Households\Pages;

use App\Filament\Resources\Households\HouseholdResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateHousehold extends CreateRecord
{
    protected static string $resource = HouseholdResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
