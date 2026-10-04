<?php

declare(strict_types=1);

use App\Filament\Resources\FeeRates\Pages\CreateFeeRate;
use App\Filament\Resources\FeeRates\Pages\EditFeeRate;
use App\Filament\Resources\FeeRates\Pages\ListFeeRates;
use App\Models\FeeRate;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => App\Enums\UserRole::Admin,
    ]);
    $this->actingAs($this->admin);
});

it('can render the fee rates index page without table column errors', function () {
    FeeRate::firstOrCreate(
        ['code' => 'monthly_occupied'],
        ['name' => 'Iuran Dihuni', 'amount' => 50000, 'effective_from' => '2026-01-01', 'is_active' => true]
    );

    $this->get('/admin/fee-rates')
        ->assertOk();

    livewire(ListFeeRates::class)
        ->assertOk()
        ->loadTable()
        ->assertCanRenderTableColumn('code')
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('amount')
        ->assertCanRenderTableColumn('effective_from')
        ->assertCanRenderTableColumn('is_active');
});

it('can render the create fee rate page', function () {
    $this->get('/admin/fee-rates/create')
        ->assertOk();

    livewire(CreateFeeRate::class)
        ->assertOk();
});

it('can render the edit fee rate page', function () {
    $rate = FeeRate::firstOrCreate(
        ['code' => 'monthly_occupied'],
        ['name' => 'Iuran Dihuni', 'amount' => 50000, 'effective_from' => '2026-01-01', 'is_active' => true]
    );

    $this->get("/admin/fee-rates/{$rate->id}/edit")
        ->assertOk();

    livewire(EditFeeRate::class, [
        'record' => $rate->id,
    ])
        ->assertOk();
});

it('can create a new custom fee rate with arbitrary code and name', function () {
    livewire(CreateFeeRate::class)
        ->fillForm([
            'name' => 'Iuran Pengelolaan Sampah',
            'code' => 'iuran_sampah',
            'amount' => 20000,
            'effective_from' => now()->toDateString(),
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('fee_rates', [
        'code' => 'iuran_sampah',
        'name' => 'Iuran Pengelolaan Sampah',
        'amount' => 20000,
    ]);
});
