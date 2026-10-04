<?php

declare(strict_types=1);

use App\Enums\ExpenseCategory;
use App\Enums\UserRole;
use App\Filament\Resources\Expenses\Pages\CreateExpense;
use App\Filament\Resources\Expenses\Pages\ListExpenses;
use App\Models\Expense;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $this->actingAs($this->admin);
});

it('can render the expenses index page', function () {
    Expense::create([
        'expense_number' => 'EXP/202610/0001',
        'category' => ExpenseCategory::WasteManagement,
        'title' => 'Honor Petugas Sampah',
        'amount' => 300000,
        'expense_date' => now(),
        'recipient' => 'Pak Supri',
        'created_by' => $this->admin->id,
    ]);

    $this->get('/admin/expenses')
        ->assertOk();

    livewire(ListExpenses::class)
        ->assertOk()
        ->loadTable()
        ->assertCanRenderTableColumn('expense_number')
        ->assertCanRenderTableColumn('title')
        ->assertCanRenderTableColumn('amount');
});

it('can render the create expense page and record an expense', function () {
    $this->get('/admin/expenses/create')
        ->assertOk();

    livewire(CreateExpense::class)
        ->fillForm([
            'category' => ExpenseCategory::Security,
            'title' => 'Pengadaan Lampu Pos Satpam',
            'amount' => 150000,
            'expense_date' => now()->toDateString(),
            'recipient' => 'Toko Listrik Terang',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Expense::where('title', 'Pengadaan Lampu Pos Satpam')->exists())->toBeTrue();
});
