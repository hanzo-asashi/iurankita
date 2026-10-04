<?php

declare(strict_types=1);

namespace App\Filament\Resources\PaymentConfirmations\Pages;

use App\Filament\Resources\PaymentConfirmations\PaymentConfirmationResource;
use Filament\Resources\Pages\ListRecords;

final class ListPaymentConfirmations extends ListRecords
{
    protected static string $resource = PaymentConfirmationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
