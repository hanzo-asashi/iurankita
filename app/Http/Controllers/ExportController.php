<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\OccupancyStatus;
use App\Models\Expense;
use App\Models\Household;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExportController extends Controller
{
    public function households(): StreamedResponse
    {
        $filename = 'data-warga-del-mattappa-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'Kode Rumah',
                'Blok',
                'Nomor',
                'Nama Kepala Keluarga',
                'Status Hunian',
                'Status Kepemilikan',
                'Nama Pemilik',
                'Kontak Pemilik',
                'Tarif Iuran Bulanan (Rp)',
                'No Telepon / WA',
                'Status Rumah',
            ]);

            $households = Household::query()->orderBy('house_code', 'asc')->get();

            foreach ($households as $idx => $h) {
                $tariff = $h->monthly_fee_override ?? ($h->occupancy_status === OccupancyStatus::Occupied ? 50000 : 35000);

                fputcsv($handle, [
                    $idx + 1,
                    $h->house_code,
                    $h->block,
                    $h->house_number,
                    $h->head_of_family,
                    $h->occupancy_status->getLabel(),
                    $h->ownership_status === 'rent' ? 'Sewa / Kontrak' : 'Milik Sendiri',
                    $h->owner_name ?? '-',
                    $h->owner_phone ?? '-',
                    $tariff,
                    $h->phone ?? '-',
                    $h->is_active ? 'Aktif' : 'Nonaktif',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function payments(): StreamedResponse
    {
        $filename = 'data-penerimaan-kas-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'No Kwitansi',
                'No Tagihan',
                'Kode Rumah',
                'Kepala Keluarga',
                'Jenis Iuran',
                'Tanggal Pembayaran',
                'Nominal (Rp)',
                'Metode Pembayaran',
                'Nomor Referensi',
                'Petugas Penerima',
            ]);

            $payments = Payment::query()
                ->with(['invoice.household', 'receiver'])
                ->orderBy('payment_date', 'desc')
                ->get();

            foreach ($payments as $idx => $p) {
                fputcsv($handle, [
                    $idx + 1,
                    $p->receipt_number,
                    $p->invoice?->invoice_number ?? '-',
                    $p->invoice?->household?->house_code ?? '-',
                    $p->invoice?->household?->head_of_family ?? '-',
                    $p->invoice?->invoice_type?->getLabel() ?? '-',
                    $p->payment_date ? $p->payment_date->format('Y-m-d') : '-',
                    $p->amount,
                    $p->payment_method->getLabel(),
                    $p->reference_number ?? '-',
                    $p->receiver?->name ?? 'Sistem',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function expenses(): StreamedResponse
    {
        $filename = 'data-pengeluaran-kas-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'No Bukti Pengeluaran',
                'Tanggal',
                'Kategori',
                'Uraian / Keperluan',
                'Penerima / Toko',
                'Nominal Pengeluaran (Rp)',
                'Petugas Pencatat',
                'Catatan',
            ]);

            $expenses = Expense::query()->with('recorder')->orderBy('expense_date', 'desc')->get();

            foreach ($expenses as $idx => $e) {
                fputcsv($handle, [
                    $idx + 1,
                    $e->expense_number,
                    $e->expense_date ? $e->expense_date->format('Y-m-d') : '-',
                    $e->category?->getLabel() ?? '-',
                    $e->title,
                    $e->recipient ?? '-',
                    $e->amount,
                    $e->recorder?->name ?? '-',
                    $e->notes ?? '-',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function outstanding(): StreamedResponse
    {
        $filename = 'daftar-tunggakan-iuran-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'No',
                'No Tagihan',
                'Kode Rumah',
                'Kepala Keluarga',
                'No Telepon / WA',
                'Jenis Tagihan',
                'Periode / Keterangan',
                'Tanggal Jatuh Tempo',
                'Total Tagihan (Rp)',
                'Sudah Dibayar (Rp)',
                'Sisa Tunggakan (Rp)',
                'Status',
            ]);

            $invoices = Invoice::query()
                ->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Partial, InvoiceStatus::Overdue])
                ->with(['household', 'constructionProject'])
                ->orderBy('due_date', 'asc')
                ->get();

            foreach ($invoices as $idx => $inv) {
                $desc = $inv->isMonthly()
                    ? ($inv->billing_period ? Carbon::createFromFormat('Y-m', $inv->billing_period)->translatedFormat('F Y') : '-')
                    : ($inv->constructionProject?->project_type?->getLabel() ?? 'Pembangunan');

                fputcsv($handle, [
                    $idx + 1,
                    $inv->invoice_number,
                    $inv->household?->house_code ?? '-',
                    $inv->household?->head_of_family ?? '-',
                    $inv->household?->phone ?? '-',
                    $inv->invoice_type->getLabel(),
                    $desc,
                    $inv->due_date ? $inv->due_date->format('Y-m-d') : '-',
                    $inv->total_amount,
                    $inv->amount_paid,
                    $inv->balance,
                    $inv->status->getLabel(),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
