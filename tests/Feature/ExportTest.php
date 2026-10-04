<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Household;
use App\Models\User;

beforeEach(function (): void {
    $this->admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
});

it('can download households CSV export', function (): void {
    Household::factory()->create(['house_code' => 'A-01']);

    $response = $this->actingAs($this->admin)
        ->get(route('admin.export.households'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->streamedContent())->toContain('Kode Rumah', 'A-01');
});

it('can download payments CSV export', function (): void {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.export.payments'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->streamedContent())->toContain('No Kwitansi', 'Nominal (Rp)');
});

it('can download expenses CSV export', function (): void {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.export.expenses'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->streamedContent())->toContain('No Bukti Pengeluaran', 'Kategori');
});

it('can download outstanding invoices CSV export', function (): void {
    $response = $this->actingAs($this->admin)
        ->get(route('admin.export.outstanding'));

    $response->assertOk();
    $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    expect($response->streamedContent())->toContain('No Tagihan', 'Sisa Tunggakan (Rp)');
});
