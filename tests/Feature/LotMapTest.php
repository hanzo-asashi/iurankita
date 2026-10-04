<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Filament\Pages\LotMapDashboard;
use App\Models\Household;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
});

it('can render the lot map dashboard page with all blocks and lot statistics', function (): void {
    Household::factory()->create(['house_code' => 'A-01', 'block' => 'A', 'house_number' => '01']);
    Household::factory()->create(['house_code' => 'B-02', 'block' => 'B', 'house_number' => '02']);

    $this->actingAs($this->admin)
        ->get('/admin/lot-map-dashboard')
        ->assertOk();

    livewire(LotMapDashboard::class)
        ->assertSuccessful()
        ->assertSee('BLOK A')
        ->assertSee('BLOK B')
        ->assertSee('BLOK C')
        ->assertSee('BLOK D')
        ->assertSee('Total Kavling');
});
