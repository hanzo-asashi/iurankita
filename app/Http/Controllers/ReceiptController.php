<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Payment;
use Illuminate\Contracts\View\View;

final class ReceiptController extends Controller
{
    public function show(Payment $payment): View
    {
        $payment->load(['invoice.household', 'invoice.constructionProject', 'receiver']);
        $setting = AppSetting::current();

        return view('receipts.print', [
            'payment' => $payment,
            'invoice' => $payment->invoice,
            'household' => $payment->invoice->household,
            'setting' => $setting,
        ]);
    }
}
