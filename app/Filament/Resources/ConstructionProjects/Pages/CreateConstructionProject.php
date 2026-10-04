<?php

declare(strict_types=1);

namespace App\Filament\Resources\ConstructionProjects\Pages;

use App\Enums\ConstructionStatus;
use App\Filament\Resources\ConstructionProjects\ConstructionProjectResource;
use App\Services\Billing\ConstructionInvoiceGeneratorService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

final class CreateConstructionProject extends CreateRecord
{
    protected static string $resource = ConstructionProjectResource::class;

    protected function afterCreate(): void
    {
        /** @var \App\Models\ConstructionProject $project */
        $project = $this->record;

        if ($project->status === ConstructionStatus::Active) {
            $invoice = app(ConstructionInvoiceGeneratorService::class)->createConstructionInvoice($project);

            Notification::make()
                ->title('Invoice Dibuat')
                ->body('Pembangunan berhasil dicatat. Invoice iuran pembangunan telah dibuat sebesar Rp'.number_format($invoice->total_amount, 0, ',', '.')." ({$invoice->invoice_number}).")
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
