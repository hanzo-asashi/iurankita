<?php

declare(strict_types=1);

namespace App\Filament\Resources\AppSettings\Pages;

use App\Filament\Resources\AppSettings\AppSettingResource;
use Filament\Resources\Pages\EditRecord;

final class EditAppSetting extends EditRecord
{
    protected static string $resource = AppSettingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
