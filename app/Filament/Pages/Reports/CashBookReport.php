<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Enums\PaymentMethod;
use App\Models\Expense;
use App\Models\Payment;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

final class CashBookReport extends Page implements HasForms
{
    use InteractsWithForms;

    public string $selectedMonth = '';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Buku Kas Umum';

    protected static ?string $title = 'Buku Kas Umum & Mutasi Keuangan RT';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.reports.cash-book-report';

    public function mount(): void
    {
        $this->selectedMonth = now()->format('Y-m');
    }

    /**
     * @return array<string, string>
     */
    public function getMonthOptionsProperty(): array
    {
        $options = [];
        $current = Carbon::now()->startOfMonth();

        for ($i = 0; $i < 12; $i++) {
            $date = $current->copy()->subMonths($i);
            $options[$date->format('Y-m')] = $date->translatedFormat('F Y');
        }

        return $options;
    }

    /**
     * @return array{
     *     opening_balance: int,
     *     total_debit: int,
     *     total_credit: int,
     *     closing_balance: int,
     *     cash_total: int,
     *     bank_total: int,
     *     ledger: array<int, array{
     *         date: ?Carbon,
     *         date_formatted: string,
     *         ref: string,
     *         type: string,
     *         description: string,
     *         method: string,
     *         debit: int,
     *         credit: int,
     *         balance: int
     *     }>
     * }
     */
    public function getLedgerDataProperty(): array
    {
        $month = $this->selectedMonth !== '' ? $this->selectedMonth : now()->format('Y-m');

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
                'payment_method_raw' => $p->payment_method,
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
                'payment_method_raw' => null,
                'debit' => 0,
                'credit' => $e->amount,
            ]);

        $entries = $payments->concat($expenses)->sortBy(fn (array $item) => $item['date']?->timestamp ?? 0)->values();

        $running = $openingBalance;
        $ledger = $entries->map(function (array $item) use (&$running): array {
            $running += ($item['debit'] - $item['credit']);
            $item['balance'] = $running;

            return $item;
        })->all();

        $totalDebit = (int) $payments->sum('debit');
        $totalCredit = (int) $expenses->sum('credit');
        $closingBalance = $openingBalance + $totalDebit - $totalCredit;

        $cashTotal = (int) $payments->filter(fn (array $p) => $p['payment_method_raw'] === PaymentMethod::Cash)->sum('debit');
        $bankTotal = (int) $payments->filter(fn (array $p) => in_array($p['payment_method_raw'], [PaymentMethod::Transfer, PaymentMethod::Qris], true))->sum('debit');

        return [
            'opening_balance' => $openingBalance,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'closing_balance' => $closingBalance,
            'cash_total' => $cashTotal,
            'bank_total' => $bankTotal,
            'ledger' => $ledger,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Cetak Rekap Fisik (A4)')
                ->icon(Heroicon::OutlinedPrinter)
                ->color('primary')
                ->url(fn (): string => route('reports.print.cash-book', ['month' => $this->selectedMonth]))
                ->openUrlInNewTab(),
        ];
    }
}
