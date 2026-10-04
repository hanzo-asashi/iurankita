<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\AppSetting;
use App\Models\ConstructionProject;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ReportPrintController extends Controller
{
    public function monthly(Request $request): View
    {
        $setting = AppSetting::current();
        $period = $request->query('period', Carbon::now()->format('Y-m'));

        $invoices = Invoice::query()
            ->where('invoice_type', InvoiceType::Monthly)
            ->where('billing_period', $period)
            ->with('household')
            ->orderBy('household_id')
            ->get();

        $totalBilled = (int) $invoices->sum('total_amount');
        $totalPaid = (int) $invoices->sum('amount_paid');
        $totalBalance = (int) $invoices->sum('balance');

        return view('reports.print-monthly', [
            'setting' => $setting,
            'period' => $period,
            'periodLabel' => Carbon::createFromFormat('Y-m', $period)->translatedFormat('F Y'),
            'invoices' => $invoices,
            'totalBilled' => $totalBilled,
            'totalPaid' => $totalPaid,
            'totalBalance' => $totalBalance,
        ]);
    }

    public function outstanding(Request $request): View
    {
        $setting = AppSetting::current();

        $invoices = Invoice::query()
            ->whereIn('status', [InvoiceStatus::Unpaid, InvoiceStatus::Partial, InvoiceStatus::Overdue])
            ->with(['household', 'constructionProject'])
            ->orderBy('household_id')
            ->get();

        $totalOutstanding = (int) $invoices->sum('balance');

        return view('reports.print-outstanding', [
            'setting' => $setting,
            'invoices' => $invoices,
            'totalOutstanding' => $totalOutstanding,
        ]);
    }

    public function payments(Request $request): View
    {
        $setting = AppSetting::current();
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $payments = Payment::query()
            ->whereYear('payment_date', Carbon::createFromFormat('Y-m', $month)->year)
            ->whereMonth('payment_date', Carbon::createFromFormat('Y-m', $month)->month)
            ->with(['invoice.household', 'receiver'])
            ->orderBy('payment_date', 'asc')
            ->get();

        $totalAmount = (int) $payments->sum('amount');

        return view('reports.print-payments', [
            'setting' => $setting,
            'month' => $month,
            'monthLabel' => Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y'),
            'payments' => $payments,
            'totalAmount' => $totalAmount,
        ]);
    }

    public function construction(Request $request): View
    {
        $setting = AppSetting::current();

        $projects = ConstructionProject::query()
            ->with(['household', 'feeInvoice.payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalFee = (int) $projects->sum('one_time_fee');

        return view('reports.print-construction', [
            'setting' => $setting,
            'projects' => $projects,
            'totalFee' => $totalFee,
        ]);
    }
}
