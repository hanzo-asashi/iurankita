<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\AppSetting;
use App\Models\Household;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PublicBillingCheckController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = mb_trim((string) $request->input('code', ''));

        if ($query === '') {
            return response()->json([
                'success' => false,
                'message' => 'Silakan masukkan kode rumah (contoh: A-05 atau A5).',
            ], 422);
        }

        $raw = $query;
        $normalized = mb_strtoupper(str_replace([' ', '-'], '', $raw));

        /** @var Household|null $household */
        $household = Household::query()
            ->where('is_active', true)
            ->where(function ($q) use ($raw, $normalized): void {
                $q->where('house_code', $raw)
                    ->orWhere('house_code', mb_strtoupper($raw))
                    ->orWhereRaw("UPPER(REPLACE(house_code, '-', '')) = ?", [$normalized]);

                if (preg_match('/^([A-Za-z]+)[-\s]?0*([0-9]+)$/', $raw, $matches)) {
                    $formatted = mb_strtoupper($matches[1]).'-'.sprintf('%02d', (int) $matches[2]);
                    $q->orWhere('house_code', $formatted);
                }
            })
            ->first();

        if ($household === null) {
            return response()->json([
                'success' => false,
                'message' => "Data rumah dengan kode \"{$query}\" tidak ditemukan atau belum aktif. Silakan hubungi pengurus lingkungan.",
            ], 404);
        }

        $setting = AppSetting::current();
        $currentPeriod = Carbon::now()->format('Y-m');

        /** @var Invoice|null $currentMonthInvoice */
        $currentMonthInvoice = $household->invoices()
            ->where('billing_period', $currentPeriod)
            ->where('invoice_type', InvoiceType::Monthly)
            ->first();

        $currentMonthStatus = match ($currentMonthInvoice?->status) {
            InvoiceStatus::Paid => 'Lunas',
            InvoiceStatus::Partial => 'Sebagian',
            InvoiceStatus::Overdue => 'Menunggak',
            InvoiceStatus::Unpaid => 'Belum Lunas',
            default => 'Belum Diterbitkan',
        };

        $currentMonthStatusColor = match ($currentMonthInvoice?->status) {
            InvoiceStatus::Paid => 'emerald',
            InvoiceStatus::Partial => 'amber',
            InvoiceStatus::Overdue, InvoiceStatus::Unpaid => 'rose',
            default => 'slate',
        };

        // Outstanding invoices (Unpaid, Partial, Overdue)
        $unpaidInvoices = $household->invoices()
            ->with(['constructionProject'])
            ->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Partial, InvoiceStatus::Overdue])
            ->orderBy('due_date', 'asc')
            ->get();

        $totalOutstanding = (int) $unpaidInvoices->sum('balance');

        // Recent paid invoices (for receipt links)
        $recentPaid = $household->invoices()
            ->with(['payments'])
            ->where('status', InvoiceStatus::Paid)
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get()
            ->map(function (Invoice $inv): array {
                $lastPayment = $inv->payments->sortByDesc('payment_date')->first();

                return [
                    'invoice_number' => $inv->invoice_number,
                    'period' => match (true) {
                        $inv->isMonthly() => Carbon::createFromFormat('Y-m', $inv->billing_period)->translatedFormat('F Y'),
                        $inv->isSpecial() => $inv->notes ?? 'Iuran Khusus',
                        default => 'Iuran Pembangunan',
                    },
                    'total_amount' => $inv->total_amount,
                    'formatted_amount' => 'Rp'.number_format($inv->total_amount, 0, ',', '.'),
                    'paid_at' => $lastPayment?->payment_date?->translatedFormat('d M Y') ?? '-',
                    'receipt_url' => $lastPayment ? route('receipt.print', $lastPayment) : null,
                ];
            });

        // WhatsApp payment confirmation link
        $adminPhone = preg_replace('/[^0-9]/', '', $setting->phone ?? '081234567890');
        if (str_starts_with($adminPhone, '0')) {
            $adminPhone = '62'.mb_substr($adminPhone, 1);
        }

        $waMsg = "Halo Pengurus {$setting->complex_name},\n\nSaya ingin konfirmasi pembayaran tagihan iuran untuk Rumah {$household->house_code} (Blok {$household->block} No. {$household->house_number}).\nTotal tagihan: Rp".number_format($totalOutstanding, 0, ',', '.').".\n\n(Berikut terlampir bukti transfer). Terima kasih.";
        $waConfirmUrl = 'https://wa.me/'.$adminPhone.'?text='.rawurlencode($waMsg);

        return response()->json([
            'success' => true,
            'household' => [
                'house_code' => $household->house_code,
                'block' => $household->block,
                'house_number' => $household->house_number,
                'masked_name' => $this->maskName($household->head_of_family),
                'occupancy_status' => $household->occupancy_status->getLabel(),
            ],
            'current_month' => [
                'period_name' => Carbon::now()->translatedFormat('F Y'),
                'status' => $currentMonthStatus,
                'color' => $currentMonthStatusColor,
            ],
            'total_outstanding' => $totalOutstanding,
            'formatted_total_outstanding' => 'Rp'.number_format($totalOutstanding, 0, ',', '.'),
            'unpaid_count' => $unpaidInvoices->count(),
            'unpaid_invoices' => $unpaidInvoices->map(fn (Invoice $inv): array => [
                'id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'title' => match (true) {
                    $inv->isMonthly() => 'Iuran Rutin '.Carbon::createFromFormat('Y-m', $inv->billing_period)->translatedFormat('F Y'),
                    $inv->isSpecial() => 'Iuran Khusus: '.($inv->notes ?? 'Kegiatan Warga'),
                    default => 'Iuran Pembangunan: '.($inv->constructionProject?->project_type?->getLabel() ?? 'Pembangunan'),
                },
                'type' => $inv->invoice_type->value,
                'due_date' => $inv->due_date?->translatedFormat('d M Y') ?? '-',
                'is_overdue' => $inv->status === InvoiceStatus::Overdue || ($inv->due_date && $inv->due_date->isPast()),
                'status_label' => $inv->status->getLabel(),
                'status_color' => $inv->status->getColor(),
                'balance' => $inv->balance,
                'formatted_balance' => 'Rp'.number_format($inv->balance, 0, ',', '.'),
            ]),
            'payment_destination' => [
                'bank_name' => $setting->bank_name ?? 'Bank BRI',
                'bank_account_number' => $setting->bank_account_number ?? '5012-01-002345-53-1',
                'bank_account_holder' => $setting->bank_account_holder ?? 'Kas Del Mattappa Residence',
                'qris_image_url' => $setting->qris_image ? asset('storage/'.$setting->qris_image) : null,
            ],
            'recent_paid' => $recentPaid,
            'wa_confirm_url' => $waConfirmUrl,
        ]);
    }

    private function maskName(string $name): string
    {
        $words = explode(' ', mb_trim($name));
        $maskedWords = array_map(function (string $word): string {
            $len = mb_strlen($word);
            if ($len <= 2) {
                return $word;
            }

            return mb_substr($word, 0, 1).str_repeat('*', min(4, $len - 2)).mb_substr($word, -1, 1);
        }, $words);

        return implode(' ', $maskedWords);
    }
}
