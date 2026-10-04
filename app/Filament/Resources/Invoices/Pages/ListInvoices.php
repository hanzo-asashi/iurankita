<?php

declare(strict_types=1);

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Pages\GenerateMonthlyInvoices;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

final class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_monthly')
                ->label('Generate Tagihan Bulanan')
                ->icon(Heroicon::OutlinedSparkles)
                ->color('primary')
                ->url(fn (): string => GenerateMonthlyInvoices::getUrl()),
        ];
    }
}
