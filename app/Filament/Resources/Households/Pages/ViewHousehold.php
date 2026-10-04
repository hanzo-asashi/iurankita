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
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

final class ViewHousehold extends ViewRecord
{
    protected static string $resource = HouseholdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_denah')
                ->label('Denah Kawasan')
                ->icon(Heroicon::OutlinedMapPin)
                ->color('info')
                ->modalHeading('Denah Kawasan Del Mattappa Residence')
                ->modalWidth('7xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->modalContent(fn () => view('filament.components.denah-modal')),
            Action::make('generate_invoice')
                ->label('Terbitkan Tagihan Bulan Ini')
                ->icon(Heroicon::OutlinedDocumentPlus)
                ->color('success')
                ->visible(function (Household $record): bool {
                    $currentPeriod = Carbon::now()->format('Y-m');

                    return ! $record->invoices()
                        ->where('billing_period', $currentPeriod)
                        ->where('invoice_type', InvoiceType::Monthly)
                        ->exists();
                })
                ->requiresConfirmation()
                ->modalHeading('Terbitkan Tagihan Bulan Berjalan')
                ->modalDescription('Terbitkan invoice iuran rutin bulanan untuk rumah ini berdasarkan status hunian saat ini?')
                ->action(function (Household $record, MonthlyBillingCalculatorService $calculator): void {
                    $period = Carbon::now();
                    $dueDay = AppSetting::current()->monthly_due_day ?? 10;
                    $dueDate = Carbon::create($period->year, $period->month, $dueDay)->endOfDay();
                    $billingResult = $calculator->calculateMonthlyInvoice($record, $period);

                    $lastSeq = Invoice::query()->whereYear('issue_date', $period->year)->count() + 1;
                    $invoiceNumber = sprintf('INV/%s/%04d', $period->format('Ym'), $lastSeq);

                    $invoice = Invoice::create([
                        'invoice_number' => $invoiceNumber,
                        'household_id' => $record->id,
                        'invoice_type' => InvoiceType::Monthly,
                        'billing_period' => $period->format('Y-m'),
                        'issue_date' => $period->copy()->startOfMonth(),
                        'due_date' => $dueDate,
                        'subtotal' => $billingResult->amount,
                        'total_amount' => $billingResult->amount,
                        'amount_paid' => 0,
                        'balance' => $billingResult->amount,
                        'status' => InvoiceStatus::Unpaid,
                        'notes' => 'Diterbitkan manual melalui profil rumah.',
                    ]);

                    Notification::make()
                        ->title('Tagihan Diterbitkan')
                        ->body("Tagihan {$invoice->invoice_number} sebesar Rp".number_format($invoice->total_amount, 0, ',', '.').' berhasil dibuat.')
                        ->success()
                        ->send();
                }),
            Action::make('advance_payment')
                ->label('Bayar di Muka')
                ->icon(Heroicon::OutlinedSparkles)
                ->color('success')
                ->modalHeading(fn (Household $record) => "Bayar Iuran di Muka — {$record->house_code} ({$record->head_of_family})")
                ->modalDescription('Terbitkan tagihan dan langsung catat pembayaran lunas sekaligus untuk beberapa bulan ke depan.')
                ->schema(fn (Household $record): array => [
                    \Filament\Forms\Components\Select::make('months')
                        ->label('Berapa Bulan ke Depan?')
                        ->options([
                            1 => '1 Bulan ke Depan',
                            2 => '2 Bulan ke Depan',
                            3 => '3 Bulan ke Depan',
                            6 => '6 Bulan (Setengah Tahun)',
                            12 => '12 Bulan (1 Tahun Penuh)',
                        ])
                        ->default(3)
                        ->required()
                        ->native(false)
                        ->live(),
                    \Filament\Forms\Components\Placeholder::make('rate_info')
                        ->label('Perhitungan Estimasi')
                        ->content(function (\Filament\Forms\Get $get) use ($record): string {
                            $months = (int) ($get('months') ?? 3);
                            $rate = $record->occupancy_status === \App\Enums\OccupancyStatus::Occupied ? 50000 : 35000;
                            $total = $rate * $months;
                            $label = $record->occupancy_status->getLabel();

                            return 'Total: Rp'.number_format($total, 0, ',', '.')." ({$months} bulan x Rp".number_format($rate, 0, ',', '.')." [{$label}])";
                        }),
                    \Filament\Forms\Components\Select::make('payment_method')
                        ->label('Metode Pembayaran')
                        ->options(\App\Enums\PaymentMethod::class)
                        ->default(\App\Enums\PaymentMethod::Cash)
                        ->native(false)
                        ->required(),
                    \Filament\Forms\Components\DatePicker::make('payment_date')
                        ->label('Tanggal Pembayaran')
                        ->default(now())
                        ->required(),
                    \Filament\Forms\Components\TextInput::make('reference_number')
                        ->label('Nomor Bukti Transfer / Referensi'),
                    \Filament\Forms\Components\FileUpload::make('proof_path')
                        ->label('Bukti Pembayaran / Struk')
                        ->image()
                        ->directory('payment-proofs'),
                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Catatan Pembayaran')
                        ->rows(2),
                ])
                ->action(function (Household $record, array $data, \App\Services\Billing\AdvanceBillingService $advanceService): void {
                    $result = $advanceService->payInAdvance(
                        household: $record,
                        monthsCount: (int) $data['months'],
                        paymentMethod: \App\Enums\PaymentMethod::from($data['payment_method']),
                        paymentDate: $data['payment_date'],
                        referenceNumber: $data['reference_number'] ?? null,
                        proofPath: $data['proof_path'] ?? null,
                        notes: $data['notes'] ?? null,
                    );

                    Notification::make()
                        ->title('Pembayaran di Muka Berhasil')
                        ->body("Sebanyak {$result['invoices_count']} bulan tagihan iuran ({$record->house_code}) senilai Rp".number_format($result['total_amount'], 0, ',', '.').' berhasil dilunasi.')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }
}
