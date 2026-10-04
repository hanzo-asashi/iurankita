<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Models\AuditLog;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
    $this->staff = User::factory()->create([
        'role' => UserRole::Staff,
    ]);
});

it('allows admin to view audit logs', function () {
    AuditLog::create([
        'user_id' => $this->admin->id,
        'action' => 'test_action',
        'model_type' => 'App\Models\Payment',
        'model_id' => 1,
        'notes' => 'Catatan tes audit log.',
    ]);

    $this->actingAs($this->admin);

    $this->get('/admin/audit-logs')
        ->assertOk();

    livewire(ListAuditLogs::class)
        ->assertOk()
        ->loadTable()
        ->assertCanRenderTableColumn('action')
        ->assertCanRenderTableColumn('notes');
});

it('forbids staff from viewing audit logs', function () {
    $this->actingAs($this->staff);

    $this->get('/admin/audit-logs')
        ->assertForbidden();
});
