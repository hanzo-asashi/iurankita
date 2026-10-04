<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Filament\Resources\Households\Pages\ListHouseholds;
use App\Filament\Resources\Households\Pages\ViewHousehold;
use App\Models\Household;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $this->actingAs($this->admin);
});

it('can render the households index page and view action', function () {
    $household = Household::factory()->create([
        'house_code' => 'TEST-01',
    ]);

    $this->get('/admin/households')
        ->assertOk();

    livewire(ListHouseholds::class)
        ->assertOk()
        ->loadTable()
        ->assertCanRenderTableColumn('house_code')
        ->assertCanRenderTableColumn('head_of_family');
});

it('can render the view household page with relation managers', function () {
    $household = Household::factory()->create([
        'house_code' => 'TEST-02',
    ]);

    $this->get("/admin/households/{$household->id}")
        ->assertOk();

    livewire(ViewHousehold::class, [
        'record' => $household->id,
    ])
        ->assertOk();
});
