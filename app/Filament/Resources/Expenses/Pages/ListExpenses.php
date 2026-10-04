<?php

declare(strict_types=1);

namespace App\Filament\Resources\Expenses\Pages;

use App\Filament\Resources\Expenses\ExpenseResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

final class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Cetak Rekap Fisik (A4)')
                ->icon(Heroicon::OutlinedPrinter)
                ->color('gray')
                ->url(fn (): string => route('reports.print.expenses', ['month' => now()->format('Y-m')]))
                ->openUrlInNewTab(),
            CreateAction::make(),
        ];
    }
}
