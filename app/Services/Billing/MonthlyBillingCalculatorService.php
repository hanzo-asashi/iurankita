<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\DTO\BillingResult;
use App\Enums\OccupancyStatus;
use App\Models\FeeRate;
use App\Models\Household;
use Carbon\CarbonInterface;

final class MonthlyBillingCalculatorService
{
    /**
     * Calculate only monthly routine fee.
     * Crucial: NEVER add construction fee here.
     */
    public function calculateMonthlyInvoice(Household $household, CarbonInterface $period): BillingResult
    {
        $periodFormatted = $period->translatedFormat('F Y');

        if ($household->monthly_fee_override !== null && $household->monthly_fee_override > 0) {
            $amount = (int) $household->monthly_fee_override;
            $feeCode = 'monthly_custom';
            $feeName = 'Iuran Khusus Unit';
            $description = "{$feeName} - Periode {$periodFormatted}";

            return new BillingResult(
                amount: $amount,
                feeCode: $feeCode,
                feeName: $feeName,
                description: $description,
            );
        }

        $isOccupied = $household->occupancy_status === OccupancyStatus::Occupied;
        $feeCode = $isOccupied ? 'monthly_occupied' : 'monthly_unoccupied';
        $defaultAmount = $isOccupied ? 50000 : 35000;
        $defaultName = $isOccupied ? 'Iuran Rutin Rumah Dihuni' : 'Iuran Rutin Rumah Belum Dihuni';

        $activeRate = FeeRate::getActiveRate($feeCode, $period);
        $amount = $activeRate ? $activeRate->amount : $defaultAmount;
        $feeName = $activeRate ? $activeRate->name : $defaultName;

        $periodFormatted = $period->translatedFormat('F Y');
        $description = "{$feeName} - Periode {$periodFormatted}";

        return new BillingResult(
            amount: $amount,
            feeCode: $feeCode,
            feeName: $feeName,
            description: $description,
        );
    }
}
