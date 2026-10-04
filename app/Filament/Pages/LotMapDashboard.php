<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\ConstructionStatus;
use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\Household;
use App\Models\Invoice;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

final class LotMapDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static string|UnitEnum|null $navigationGroup = 'Data Master';

    protected static ?string $navigationLabel = 'Peta Status Kavling';

    protected static ?string $title = 'Peta Kavling & Status Iuran Warga';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.lot-map-dashboard';

    /**
     * @return array{
     *     total_lots: int,
     *     registered_count: int,
     *     paid_count: int,
     *     unpaid_count: int,
     *     unregistered_count: int,
     *     blocks: array<string, array<int, array{
     *         code: string,
     *         block: string,
     *         number: string,
     *         household: ?Household,
     *         current_invoice_status: ?string,
     *         status_color: string,
     *         has_active_construction: bool
     *     }>>
     * }
     */
    public function getMapDataProperty(): array
    {
        $currentMonth = Carbon::now()->format('Y-m');

        // All households keyed by normalized code
        $households = Household::query()
            ->with(['constructionProjects' => fn ($q) => $q->where('status', ConstructionStatus::Active)])
            ->get()
            ->keyBy(fn (Household $h): string => mb_strtoupper(str_replace(['-', ' '], '', $h->house_code)));

        // Invoices for current month
        $currentInvoices = Invoice::query()
            ->where('invoice_type', InvoiceType::Monthly)
            ->where('billing_period', $currentMonth)
            ->get()
            ->keyBy('household_id');

        // Define all planned lots of Del Mattappa Residence (59 lots total)
        $lotDefinitions = [
            'A' => array_map(fn (int $i): array => ['block' => 'A', 'num' => sprintf('%02d', $i)], range(1, 10)),
            'B' => array_map(fn (int $i): array => ['block' => 'B', 'num' => sprintf('%02d', $i)], range(1, 14)),
            'C' => array_values(array_filter(
                array_map(fn (int $i): array => ['block' => 'C', 'num' => sprintf('%02d', $i)], range(1, 20)),
                fn (array $item): bool => $item['num'] !== '13'
            )),
            'D' => array_values(array_filter(
                array_map(fn (int $i): array => ['block' => 'D', 'num' => sprintf('%02d', $i)], range(1, 17)),
                fn (array $item): bool => $item['num'] !== '13'
            )),
        ];

        $blocks = [];
        $totalLots = 0;
        $registeredCount = 0;
        $paidCount = 0;
        $unpaidCount = 0;

        foreach ($lotDefinitions as $blockName => $lots) {
            $blocks[$blockName] = [];

            foreach ($lots as $lot) {
                $totalLots++;
                $numInt = (int) $lot['num'];
                $normalizedKey1 = "{$blockName}{$numInt}";
                $normalizedKey2 = sprintf('%s%02d', $blockName, $numInt);

                /** @var Household|null $hh */
                $hh = $households->get($normalizedKey1) ?? $households->get($normalizedKey2);

                $statusColor = 'slate';
                $invStatus = null;
                $hasActiveConstruction = false;

                if ($hh) {
                    $registeredCount++;
                    $hasActiveConstruction = $hh->constructionProjects->isNotEmpty();

                    $inv = $currentInvoices->get($hh->id);
                    if ($inv) {
                        $invStatus = $inv->status->value;
                        $statusColor = match ($inv->status) {
                            InvoiceStatus::Paid => 'emerald',
                            InvoiceStatus::Partial => 'amber',
                            InvoiceStatus::Overdue, InvoiceStatus::Unpaid => 'rose',
                            default => 'slate',
                        };

                        if ($inv->status === InvoiceStatus::Paid) {
                            $paidCount++;
                        } else {
                            $unpaidCount++;
                        }
                    } else {
                        $statusColor = 'sky';
                        $invStatus = 'no_invoice';
                    }
                }

                $blocks[$blockName][] = [
                    'code' => sprintf('%s-%02d', $blockName, $numInt),
                    'block' => $blockName,
                    'number' => (string) $numInt,
                    'household' => $hh,
                    'current_invoice_status' => $invStatus,
                    'status_color' => $statusColor,
                    'has_active_construction' => $hasActiveConstruction,
                ];
            }
        }

        return [
            'total_lots' => $totalLots,
            'registered_count' => $registeredCount,
            'paid_count' => $paidCount,
            'unpaid_count' => $unpaidCount,
            'unregistered_count' => $totalLots - $registeredCount,
            'blocks' => $blocks,
        ];
    }
}
