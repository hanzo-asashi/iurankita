<?php

declare(strict_types=1);

namespace App\Filament\Resources\AppSettings\Pages;

use App\Filament\Resources\AppSettings\AppSettingResource;
use App\Models\AppSetting;
use Filament\Resources\Pages\ListRecords;

final class ListAppSettings extends ListRecords
{
    protected static string $resource = AppSettingResource::class;

    public function mount(): void
    {
        AppSetting::current(); // ensure setting exists
        parent::mount();
    }
}
