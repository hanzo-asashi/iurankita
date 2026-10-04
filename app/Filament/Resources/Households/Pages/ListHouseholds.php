<?php

declare(strict_types=1);

namespace App\Filament\Resources\Households\Pages;

use App\Filament\Resources\Households\HouseholdResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

final class ListHouseholds extends ListRecords
{
    protected static string $resource = HouseholdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('denah')
                ->label('Denah Kawasan')
                ->icon('heroicon-o-map')
                ->color('info')
                ->modalHeading('Denah Kawasan Del Mattappa Residence')
                ->modalDescription('Lalabata Rilau - Soppeng | PT. Del Mapparenta Properti')
                ->modalContent(view('filament.components.denah-modal'))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),
            Action::make('export_csv')
                ->label('Ekspor Data Warga (CSV)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn (): string => route('admin.export.households'))
                ->openUrlInNewTab(),
            CreateAction::make()
                ->label('Tambah KK / Rumah'),
        ];
    }
}
