<?php

declare(strict_types=1);

use App\Enums\ConstructionStatus;
use App\Enums\ConstructionType;
use App\Enums\InvoiceType;
use App\Enums\UserRole;
use App\Filament\Pages\GenerateMonthlyInvoices;
use App\Filament\Pages\Reports\CashBookReport;
use App\Filament\Pages\Reports\ConstructionBillingReport;
use App\Filament\Pages\Reports\MonthlyBillingReport;
use App\Filament\Pages\Reports\OutstandingReport;
use App\Filament\Pages\Reports\PaymentReport;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->admin = User::factory()->create([
        'role' => UserRole::Admin,
    ]);
});

it('can render construction billing report page without errors', function (): void {
    $this->actingAs($this->admin)
        ->get('/admin/construction-billing-report')
        ->assertOk();

    livewire(ConstructionBillingReport::class)
        ->assertSuccessful()
        ->filterTable('construction_status', ConstructionStatus::Active->value)
        ->assertSuccessful()
        ->filterTable('project_type', ConstructionType::Kitchen->value)
        ->assertSuccessful();
});

it('can render payment report page without errors', function (): void {
    $this->actingAs($this->admin)
        ->get('/admin/payment-report')
        ->assertOk();

    livewire(PaymentReport::class)
        ->assertSuccessful()
        ->filterTable('invoice_type', InvoiceType::Monthly->value)
        ->assertSuccessful();
});

it('can render monthly billing report page without errors', function (): void {
    $this->actingAs($this->admin)
        ->get('/admin/monthly-billing-report')
        ->assertOk();

    livewire(MonthlyBillingReport::class)
        ->assertSuccessful();
});

it('can render outstanding report page without errors', function (): void {
    $this->actingAs($this->admin)
        ->get('/admin/outstanding-report')
        ->assertOk();

    livewire(OutstandingReport::class)
        ->assertSuccessful();
});

it('can render generate monthly invoices page without errors and fill form', function (): void {
    $this->actingAs($this->admin)
        ->get('/admin/generate-monthly-invoices')
        ->assertOk();

    livewire(GenerateMonthlyInvoices::class)
        ->assertSuccessful()
        ->fillForm([
            'month' => 10,
            'year' => 2026,
        ])
        ->assertSuccessful()
        ->assertSee('Oktober 2026');
});

it('can render cash book report page without errors', function (): void {
    $this->actingAs($this->admin)
        ->get('/admin/cash-book-report')
        ->assertOk();

    livewire(CashBookReport::class)
        ->assertSuccessful()
        ->assertSee('Buku Kas Umum & Mutasi Keuangan RT');
});

it('can render physical print report pages for monthly, outstanding, payments, and construction', function (): void {
    $this->actingAs($this->admin)
        ->get(route('reports.print.monthly'))
        ->assertOk()
        ->assertSee('Rekap Tagihan Iuran Bulanan');

    $this->actingAs($this->admin)
        ->get(route('reports.print.outstanding'))
        ->assertOk()
        ->assertSee('Laporan Daftar Tunggakan Iuran Warga');

    $this->actingAs($this->admin)
        ->get(route('reports.print.payments'))
        ->assertOk()
        ->assertSee('Laporan Penerimaan Kas Iuran');

    $this->actingAs($this->admin)
        ->get(route('reports.print.construction'))
        ->assertOk()
        ->assertSee('Laporan Rekapitulasi Iuran Pembangunan & Renovasi');

    $this->actingAs($this->admin)
        ->get(route('reports.print.expenses'))
        ->assertOk()
        ->assertSee('Laporan Buku Pengeluaran Kas RT');

    $this->actingAs($this->admin)
        ->get(route('reports.print.cash-book'))
        ->assertOk()
        ->assertSee('Buku Kas Umum');
});
