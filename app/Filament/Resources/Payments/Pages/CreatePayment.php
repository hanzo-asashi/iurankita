<?php

declare(strict_types=1);

namespace App\Filament\Resources\Payments\Pages;

use App\Enums\PaymentMethod;
use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Invoice;
use App\Services\Payment\PaymentRecorderService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

final class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $invoice = Invoice::findOrFail($data['invoice_id']);

        return app(PaymentRecorderService::class)->recordPayment(
            invoice: $invoice,
            amount: (int) $data['amount'],
            paymentMethod: PaymentMethod::from($data['payment_method']),
            paymentDate: $data['payment_date'],
            referenceNumber: $data['reference_number'] ?? null,
            proofPath: $data['proof_path'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
