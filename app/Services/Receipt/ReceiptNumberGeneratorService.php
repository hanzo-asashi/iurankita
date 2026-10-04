<?php

declare(strict_types=1);

namespace App\Services\Receipt;

use App\Models\Payment;
use Carbon\Carbon;
use Carbon\CarbonInterface;

final class ReceiptNumberGeneratorService
{
    public function generate(?CarbonInterface $date = null): string
    {
        $targetDate = $date ?? Carbon::now();
        $prefix = 'KWT-'.$targetDate->format('Ym');

        $count = Payment::query()
            ->where('receipt_number', 'like', "{$prefix}-%")
            ->count() + 1;

        $receiptNumber = sprintf('%s-%05d', $prefix, $count);

        while (Payment::where('receipt_number', $receiptNumber)->exists()) {
            $count++;
            $receiptNumber = sprintf('%s-%05d', $prefix, $count);
        }

        return $receiptNumber;
    }
}
