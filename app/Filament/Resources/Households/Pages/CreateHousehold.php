<?php

declare(strict_types=1);

namespace App\Filament\Resources\Households\Pages;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Filament\Resources\Households\HouseholdResource;
use App\Models\AppSetting;
use App\Models\Household;
use App\Models\Invoice;
use App\Services\Billing\MonthlyBillingCalculatorService;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

final class CreateHousehold extends CreateRecord
{
    protected static string $resource = HouseholdResource::class;

    public function mount(): void
    {
        parent::mount();

        $houseCode = request()->query('house_code');
        $block = request()->query('block');
        $houseNumber = request()->query('house_number') ?? request()->query('number');

        if ($houseCode && (! $block || ! $houseNumber) && preg_match('/^([A-Za-z]+)[-\s]?0*([0-9]+)$/', (string) $houseCode, $m)) {
            $block = $block ?: mb_strtoupper($m[1]);
            $houseNumber = $houseNumber ?: sprintf('%02d', (int) $m[2]);
        }

        $fills = array_filter([
            'house_code' => $houseCode,
            'block' => $block ? mb_strtoupper((string) $block) : null,
            'house_number' => $houseNumber ? sprintf('%02d', (int) $houseNumber) : null,
        ]);

        if (! empty($fills)) {
            $this->form->fill(array_merge($this->form->getState(), $fills));
        }
    }

    protected function afterCreate(): void
    {
        /** @var Household $household */
        $household = $this->record;

        if ($household->is_active) {
            $period = Carbon::now();
            $calculator = app(MonthlyBillingCalculatorService::class);
            $setting = AppSetting::current();
            $dueDay = $setting->monthly_due_day ?? 10;
            $dueDate = Carbon::create($period->year, $period->month, $dueDay)->endOfDay();
            $billingResult = $calculator->calculateMonthlyInvoice($household, $period);

            $lastSeq = Invoice::query()->whereYear('issue_date', $period->year)->count() + 1;
            $invoiceNumber = sprintf('INV/%s/%04d', $period->format('Ym'), $lastSeq);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'household_id' => $household->id,
                'invoice_type' => InvoiceType::Monthly,
                'billing_period' => $period->format('Y-m'),
                'issue_date' => $period->copy()->startOfMonth(),
                'due_date' => $dueDate,
                'subtotal' => $billingResult->amount,
                'total_amount' => $billingResult->amount,
                'amount_paid' => 0,
                'balance' => $billingResult->amount,
                'status' => InvoiceStatus::Unpaid,
                'notes' => 'Tagihan otomatis awal pendaftaran rumah.',
            ]);

            Notification::make()
                ->title('Tagihan Bulan Berjalan Diterbitkan')
                ->body("Tagihan {$invoice->invoice_number} sebesar Rp".number_format($invoice->total_amount, 0, ',', '.').' berhasil dibuat otomatis.')
                ->info()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
