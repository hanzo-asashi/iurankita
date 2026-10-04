<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class ReceiptController extends Controller
{
    public function show(Request $request, Payment $payment): View
    {
        $payment->load(['invoice.household', 'invoice.constructionProject', 'receiver']);
        $setting = AppSetting::current();

        $view = $request->query('format') === 'thermal' ? 'receipts.thermal' : 'receipts.print';

        return view($view, [
            'payment' => $payment,
            'invoice' => $payment->invoice,
            'household' => $payment->invoice->household,
            'setting' => $setting,
        ]);
    }
}
