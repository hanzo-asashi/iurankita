<?php

declare(strict_types=1);

namespace App\Filament\Resources\Expenses\Pages;

use App\Filament\Resources\Expenses\ExpenseResource;
use App\Models\Expense;
use App\Services\Audit\AuditService;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $date = Carbon::parse($data['expense_date']);
        $data['expense_number'] = Expense::generateExpenseNumber($date);
        $data['created_by'] = Auth::id();

        /** @var Expense $expense */
        $expense = parent::handleRecordCreation($data);

        AuditService::log(
            action: 'create_expense',
            model: $expense,
            newValues: $expense->toArray(),
            notes: "Pengeluaran kas {$expense->expense_number} sebesar Rp".number_format($expense->amount, 0, ',', '.')." ({$expense->title}) dicatat."
        );

        return $expense;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
