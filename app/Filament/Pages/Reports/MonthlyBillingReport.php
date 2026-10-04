<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\AppSetting;
use App\Models\Invoice;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

final class MonthlyBillingReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Rekap Iuran Bulanan';

    protected static ?string $title = 'Laporan Rekap Iuran Rutin Bulanan';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.reports.monthly-billing-report';

    public static function generateBroadcastText(?string $period = null): string
    {
        $period = $period ?? now()->format('Y-m');
        $setting = AppSetting::current();
        $complexName = $setting->complex_name ?? 'Del Mattappa Residence';
        $periodLabel = Carbon::createFromFormat('Y-m', $period)->translatedFormat('F Y');
        $todayStr = now()->translatedFormat('d F Y');

        $invoices = Invoice::query()
            ->where('invoice_type', InvoiceType::Monthly)
            ->where('billing_period', $period)
            ->with('household')
            ->orderBy('household_id')
            ->get();

        $totalHouseholds = $invoices->count();
        $paidInvoices = $invoices->where('status', InvoiceStatus::Paid);
        $unpaidInvoices = $invoices->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Partial, InvoiceStatus::Overdue]);

        $paidCodes = $paidInvoices->map(fn (Invoice $i): string => $i->household->house_code)->values()->implode(', ');
        $unpaidCodes = $unpaidInvoices->map(fn (Invoice $i): string => $i->household->house_code)->values()->implode(', ');

        $totalPaid = (int) $invoices->sum('amount_paid');
        $totalBalance = (int) $invoices->sum('balance');

        $text = "*PENGUMUMAN IURAN WARGA {$complexName}*\n";
        $text .= "*Periode: {$periodLabel}*\n";
        $text .= "Update data per: {$todayStr}\n\n";
        $text .= "--------------------------------------\n";
        $text .= "📊 *RINGKASAN OPERASIONAL:*\n";
        $text .= "• Total Rumah: {$totalHouseholds} Unit\n";
        $text .= "• ✅ Sudah Lunas: {$paidInvoices->count()} Rumah (Rp".number_format($totalPaid, 0, ',', '.').")\n";
        $text .= "• ⏳ Belum Lunas: {$unpaidInvoices->count()} Rumah (Rp".number_format($totalBalance, 0, ',', '.').")\n";
        $text .= "--------------------------------------\n\n";

        if ($paidCodes !== '') {
            $text .= "✅ *RUMAH YANG SUDAH LUNAS:*\n";
            $text .= "{$paidCodes}\n\n";
        }

        if ($unpaidCodes !== '') {
            $text .= "⏳ *RUMAH DALAM PROSES / BELUM LUNAS:*\n";
            $text .= "{$unpaidCodes}\n\n";
        }

        $text .= "💳 *REKENING KAS RESMI PEMBAYARAN:*\n";
        $text .= '• Bank: '.($setting->bank_name ?? 'Bank BRI')."\n";
        $text .= '• No. Rekening: '.($setting->bank_account_number ?? '-')."\n";
        $text .= '• Atas Nama: '.($setting->bank_account_holder ?? 'Kas '.$complexName)."\n\n";
        $text .= 'Bagi warga yang telah melakukan pembayaran, mohon kirimkan konfirmasi bukti transfer ke Pengurus/Bendahara. Terima kasih atas kerja sama dan partisipasi aktif Bapak/Ibu sekalian demi kelancaran lingkungan perumahan kita bersama.';

        return $text;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Invoice::query()->where('invoice_type', InvoiceType::Monthly)->with('household'))
            ->columns([
                TextColumn::make('household.house_code')
                    ->label('Kode Rumah')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('household.head_of_family')
                    ->label('Kepala Keluarga')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('household.occupancy_status')
                    ->label('Status Hunian')
                    ->badge()
                    ->sortable(),
                TextColumn::make('billing_period')
                    ->label('Periode')
                    ->formatStateUsing(fn (?string $state): string => $state ? Carbon::createFromFormat('Y-m', $state)->translatedFormat('F Y') : '-')
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Tagihan')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('amount_paid')
                    ->label('Pembayaran')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->color('success')
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('balance')
                    ->label('Sisa Tagihan')
                    ->formatStateUsing(fn (int $state): string => 'Rp'.number_format($state, 0, ',', '.'))
                    ->color(fn (int $state): string => $state > 0 ? 'danger' : 'gray')
                    ->summarize(Sum::make()->formatStateUsing(fn ($state): string => 'Total: Rp'.number_format((int) $state, 0, ',', '.')))
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('billing_period', 'desc')
            ->emptyStateHeading('Belum Ada Data Rekap Bulanan')
            ->emptyStateDescription('Data rekapitulasi iuran bulanan akan muncul setelah tagihan bulanan dibuat.')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options(InvoiceStatus::class)
                    ->native(false),
                SelectFilter::make('billing_period')
                    ->label('Periode Tagihan')
                    ->native(false)
                    ->options(fn (): array => Invoice::query()
                        ->where('invoice_type', InvoiceType::Monthly)
                        ->whereNotNull('billing_period')
                        ->distinct()
                        ->orderByDesc('billing_period')
                        ->pluck('billing_period')
                        ->mapWithKeys(fn (string $period): array => [
                            $period => Carbon::createFromFormat('Y-m', $period)->translatedFormat('F Y'),
                        ])
                        ->toArray()
                    ),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('whatsapp_broadcast')
                ->label('Siaran WA Group')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('success')
                ->modalHeading('Format Pengumuman Rekap Iuran untuk Grup WhatsApp')
                ->modalDescription('Salin teks pengumuman siap kirim berikut untuk dibagikan ke WhatsApp Group warga Del Mattappa Residence.')
                ->form([
                    Textarea::make('broadcast_text')
                        ->label('Teks Siaran WhatsApp (Siap Salin)')
                        ->rows(14)
                        ->default(fn (): string => self::generateBroadcastText())
                        ->helperText('Klik di dalam kotak untuk menyalin atau mengedit teks sebelum dikirim ke grup warga.'),
                ])
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup'),

            Action::make('print')
                ->label('Cetak Rekap Fisik (A4)')
                ->icon(Heroicon::OutlinedPrinter)
                ->color('primary')
                ->url(fn (): string => route('reports.print.monthly', ['period' => now()->format('Y-m')]))
                ->openUrlInNewTab(),
        ];
    }
}
