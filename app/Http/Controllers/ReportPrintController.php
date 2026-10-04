<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\InvoiceType;
use App\Models\AppSetting;
use App\Models\ConstructionProject;
use App\Models\Expense;
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

    public function expenses(Request $request): View
    {
        $setting = AppSetting::current();
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $expenses = Expense::query()
            ->whereYear('expense_date', Carbon::createFromFormat('Y-m', $month)->year)
            ->whereMonth('expense_date', Carbon::createFromFormat('Y-m', $month)->month)
            ->with('recorder')
            ->orderBy('expense_date', 'asc')
            ->get();

        $totalExpense = (int) $expenses->sum('amount');

        return view('reports.print-expenses', [
            'setting' => $setting,
            'month' => $month,
            'monthLabel' => Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y'),
            'expenses' => $expenses,
            'totalExpense' => $totalExpense,
        ]);
    }

    public function cashBook(Request $request): View
    {
        $setting = AppSetting::current();
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $prevIncome = (int) Payment::where('payment_date', '<', $startDate)->sum('amount');
        $prevExpense = (int) Expense::where('expense_date', '<', $startDate)->sum('amount');
        $openingBalance = $prevIncome - $prevExpense;

        $payments = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->with('invoice.household')
            ->get()
            ->map(fn (Payment $p): array => [
                'date' => $p->payment_date,
                'date_formatted' => $p->payment_date ? $p->payment_date->format('d/m/Y') : '-',
                'ref' => $p->receipt_number,
                'type' => 'in',
                'description' => ($p->invoice->isMonthly() ? 'Iuran Rutin' : 'Iuran Pembangunan').' - '.$p->invoice->household->house_code.' ('.$p->invoice->household->head_of_family.')',
                'method' => $p->payment_method->getLabel(),
                'debit' => $p->amount,
                'credit' => 0,
            ]);

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->get()
            ->map(fn (Expense $e): array => [
                'date' => $e->expense_date,
                'date_formatted' => $e->expense_date ? $e->expense_date->format('d/m/Y') : '-',
                'ref' => $e->expense_number,
                'type' => 'out',
                'description' => '['.$e->category->getLabel().'] '.$e->title.($e->recipient ? ' ('.$e->recipient.')' : ''),
                'method' => 'Kas Operasional',
                'debit' => 0,
                'credit' => $e->amount,
            ]);

        $entries = $payments->concat($expenses)->sortBy(fn (array $item) => $item['date']?->timestamp ?? 0)->values();

        $running = $openingBalance;
        $ledger = $entries->map(function (array $item) use (&$running): array {
            $running += ($item['debit'] - $item['credit']);
            $item['balance'] = $running;

            return $item;
        });

        $totalDebit = (int) $payments->sum('debit');
        $totalCredit = (int) $expenses->sum('credit');
        $closingBalance = $openingBalance + $totalDebit - $totalCredit;

        return view('reports.print-cash-book', [
            'setting' => $setting,
            'month' => $month,
            'monthLabel' => Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y'),
            'openingBalance' => $openingBalance,
            'ledger' => $ledger,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'closingBalance' => $closingBalance,
        ]);
    }
}
