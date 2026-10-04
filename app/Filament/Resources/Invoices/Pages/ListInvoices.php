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
            Action::make('generate_special')
                ->label('Terbitkan Iuran Khusus / Kegiatan')
                ->icon(Heroicon::OutlinedMegaphone)
                ->color('success')
                ->modalHeading('Terbitkan Iuran Khusus / Kegiatan Warga')
                ->modalDescription('Terbitkan tagihan serentak untuk seluruh rumah aktif (misal: HUT RI, Gotong Royong, Perbaikan Fasilitas Bersama).')
                ->form([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->label('Nama Kegiatan / Jenis Iuran')
                        ->placeholder('Contoh: Partisipasi Peringatan HUT Kemerdekaan RI Ke-81')
                        ->required()
                        ->maxLength(100),
                    \Filament\Forms\Components\TextInput::make('amount')
                        ->label('Nominal per Rumah (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->default(50000)
                        ->minValue(1000)
                        ->required(),
                    \Filament\Forms\Components\DatePicker::make('due_date')
                        ->label('Batas Akhir Pembayaran')
                        ->default(now()->addDays(14))
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('description')
                        ->label('Deskripsi / Catatan Tambahan')
                        ->placeholder('Contoh: Untuk pengadaan hadiah lomba anak-anak dan panggung hiburan warga Del Mattappa.')
                        ->rows(2),
                ])
                ->action(function (array $data, \App\Services\Billing\SpecialInvoiceGeneratorService $generator): void {
                    $result = $generator->generate(
                        title: $data['title'],
                        amount: (int) $data['amount'],
                        dueDate: $data['due_date'],
                        description: $data['description'] ?? null
                    );

                    \Filament\Notifications\Notification::make()
                        ->title('Iuran Khusus Berhasil Diterbitkan')
                        ->body("Berhasil menerbitkan {$result['generated_count']} tagihan senilai total Rp".number_format($result['total_amount'], 0, ',', '.')." ({$result['skipped_count']} dilewati karena sudah ada).")
                        ->success()
                        ->send();
                }),
        ];
    }
}
