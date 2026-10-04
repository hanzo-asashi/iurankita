<?php

declare(strict_types=1);

namespace App\Filament\Resources\FeeRates\Pages;

use App\Filament\Resources\FeeRates\FeeRateResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateFeeRate extends CreateRecord
{
    protected static string $resource = FeeRateResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
