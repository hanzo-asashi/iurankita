<?php

declare(strict_types=1);

namespace App\Filament\Resources\ConstructionProjects\Pages;

use App\Enums\ConstructionStatus;
use App\Filament\Resources\ConstructionProjects\ConstructionProjectResource;
use App\Services\Billing\ConstructionInvoiceGeneratorService;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

final class EditConstructionProject extends EditRecord
{
    protected static string $resource = ConstructionProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        /** @var \App\Models\ConstructionProject $project */
        $project = $this->record;

        if ($project->status === ConstructionStatus::Active && ! $project->invoice()->exists() && ! $project->feeInvoice()->exists()) {
            $invoice = app(ConstructionInvoiceGeneratorService::class)->createConstructionInvoice($project);

            Notification::make()
                ->title('Invoice Dibuat')
                ->body('Status diubah ke Aktif. Invoice iuran pembangunan telah dibuat sebesar Rp'.number_format($invoice->total_amount, 0, ',', '.')." ({$invoice->invoice_number}).")
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
