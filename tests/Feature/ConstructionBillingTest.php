<?php

declare(strict_types=1);

use App\Enums\ConstructionStatus;
use App\Enums\ConstructionType;
use App\Enums\InvoiceType;
use App\Enums\OccupancyStatus;
use App\Models\ConstructionProject;
use App\Models\FeeRate;
use App\Models\Household;
use App\Models\Invoice;
use App\Services\Billing\ConstructionInvoiceGeneratorService;
use App\Services\Billing\MonthlyInvoiceGeneratorService;
use App\Services\Construction\ConstructionProjectService;
use Carbon\Carbon;

beforeEach(function (): void {
    FeeRate::firstOrCreate(
        ['code' => 'monthly_occupied'],
        ['name' => 'Iuran Dihuni', 'amount' => 50000, 'effective_from' => '2026-01-01', 'is_active' => true]
    );

    FeeRate::firstOrCreate(
        ['code' => 'monthly_unoccupied'],
        ['name' => 'Iuran Belum Dihuni', 'amount' => 35000, 'effective_from' => '2026-01-01', 'is_active' => true]
    );

    FeeRate::firstOrCreate(
        ['code' => 'construction_one_time'],
        ['name' => 'Iuran Pembangunan', 'amount' => 100000, 'effective_from' => '2026-01-01', 'is_active' => true]
    );
});

it('creates one construction invoice when project is active with default Rp100.000', function (): void {
    $household = Household::factory()->create();
    $project = ConstructionProject::factory()->create([
        'household_id' => $household->id,
        'project_type' => ConstructionType::Kitchen,
        'status' => ConstructionStatus::Active,
        'start_date' => '2026-09-01',
    ]);

    $service = app(ConstructionInvoiceGeneratorService::class);
    $invoice = $service->createConstructionInvoice($project);

    expect($invoice)->not->toBeNull()
        ->and($invoice->invoice_type)->toBe(InvoiceType::Construction)
        ->and($invoice->total_amount)->toBe(100000)
        ->and($invoice->balance)->toBe(100000)
        ->and($invoice->construction_project_id)->toBe($project->id);

    // Call again to verify idempotency: returns exact same invoice, does not duplicate
    $duplicateAttempt = $service->createConstructionInvoice($project);
    expect($duplicateAttempt->id)->toBe($invoice->id)
        ->and(Invoice::where('construction_project_id', $project->id)->count())->toBe(1);
});

it('does not create construction invoice when project is planned or cancelled before active', function (): void {
    $household = Household::factory()->create();
    $project = ConstructionProject::factory()->planned()->create([
        'household_id' => $household->id,
    ]);

    // Planned project has no invoice
    expect(Invoice::where('construction_project_id', $project->id)->count())->toBe(0);

    // Cancel project
    app(ConstructionProjectService::class)->cancelProject($project, 'Dibatalkan warga');

    expect(Invoice::where('construction_project_id', $project->id)->count())->toBe(0);
});

it('does not create new invoice when project is completed', function (): void {
    $household = Household::factory()->create();
    $project = ConstructionProject::factory()->active()->create([
        'household_id' => $household->id,
        'start_date' => '2026-09-01',
    ]);

    $invoice = app(ConstructionInvoiceGeneratorService::class)->createConstructionInvoice($project);
    expect(Invoice::where('construction_project_id', $project->id)->count())->toBe(1);

    // Complete project
    app(ConstructionProjectService::class)->completeProject($project, '2026-12-01', 'Pembangunan selesai');

    expect($project->fresh()->status)->toBe(ConstructionStatus::Completed)
        ->and($project->fresh()->completion_date->format('Y-m-d'))->toBe('2026-12-01')
        ->and(Invoice::where('construction_project_id', $project->id)->count())->toBe(1);
});

it('creates a new construction invoice if the household starts a second project later', function (): void {
    $household = Household::factory()->create(['house_code' => 'A-01']);

    // Project 1 (2026: Dapur)
    $project1 = ConstructionProject::factory()->active()->create([
        'household_id' => $household->id,
        'project_type' => ConstructionType::Kitchen,
        'start_date' => '2026-09-01',
    ]);
    $invoice1 = app(ConstructionInvoiceGeneratorService::class)->createConstructionInvoice($project1);

    // Project 2 (2028: Penambahan Bangunan)
    $project2 = ConstructionProject::factory()->active()->create([
        'household_id' => $household->id,
        'project_type' => ConstructionType::BuildingAddition,
        'start_date' => '2028-03-01',
    ]);
    $invoice2 = app(ConstructionInvoiceGeneratorService::class)->createConstructionInvoice($project2);

    expect($invoice1->id)->not->toBe($invoice2->id)
        ->and(Invoice::where('household_id', $household->id)->where('invoice_type', InvoiceType::Construction)->count())->toBe(2);
});

it('satisfies PRD Section 92 mandatory scenario: occupied house with active construction for 3 months', function (): void {
    $household = Household::factory()->create([
        'house_code' => 'A-01',
        'occupancy_status' => OccupancyStatus::Occupied,
    ]);

    // 1 September: starts building kitchen
    $project = ConstructionProject::factory()->active()->create([
        'household_id' => $household->id,
        'project_type' => ConstructionType::Kitchen,
        'start_date' => '2026-09-01',
    ]);
    $constructionInvoice = app(ConstructionInvoiceGeneratorService::class)->createConstructionInvoice($project);

    $monthlyGenerator = app(MonthlyInvoiceGeneratorService::class);

    // Month 1: September 2026
    $sepResult = $monthlyGenerator->generate(Carbon::parse('2026-09-01'), $household->id);
    expect($sepResult['total_amount'])->toBe(50000);
    // Construction invoice created this month: Rp100.000
    expect($constructionInvoice->total_amount)->toBe(100000);

    // Month 2: October 2026 (Construction is still active!)
    $octResult = $monthlyGenerator->generate(Carbon::parse('2026-10-01'), $household->id);
    expect($octResult['total_amount'])->toBe(50000); // Only Rp50.000!

    // Month 3: November 2026 (Construction is still active!)
    $novResult = $monthlyGenerator->generate(Carbon::parse('2026-11-01'), $household->id);
    expect($novResult['total_amount'])->toBe(50000); // Only Rp50.000!

    // Total construction invoices for the entire 3 months MUST BE EXACTLY Rp100.000 (NOT Rp300.000!)
    $totalConstructionBilled = Invoice::query()
        ->where('household_id', $household->id)
        ->where('invoice_type', InvoiceType::Construction)
        ->sum('total_amount');

    expect((int) $totalConstructionBilled)->toBe(100000);
});

it('satisfies PRD Section 93 mandatory scenario: unoccupied house with active construction for 5 months', function (): void {
    $household = Household::factory()->create([
        'house_code' => 'B-02',
        'occupancy_status' => OccupancyStatus::Unoccupied,
    ]);

    $project = ConstructionProject::factory()->active()->create([
        'household_id' => $household->id,
        'project_type' => ConstructionType::BuildingAddition,
        'start_date' => '2026-01-01',
    ]);
    app(ConstructionInvoiceGeneratorService::class)->createConstructionInvoice($project);

    $monthlyGenerator = app(MonthlyInvoiceGeneratorService::class);

    // 5 months of routine billing
    for ($m = 1; $m <= 5; $m++) {
        $date = Carbon::create(2026, $m, 1);
        $result = $monthlyGenerator->generate($date, $household->id);
        expect($result['total_amount'])->toBe(35000);
    }

    // Total construction invoices across all 5 months MUST BE EXACTLY Rp100.000
    $totalConstructionBilled = Invoice::query()
        ->where('household_id', $household->id)
        ->where('invoice_type', InvoiceType::Construction)
        ->sum('total_amount');

    expect((int) $totalConstructionBilled)->toBe(100000);
});

it('cancels associated unpaid construction invoice when project is cancelled', function (): void {
    $household = Household::factory()->create();
    $project = ConstructionProject::factory()->active()->create([
        'household_id' => $household->id,
        'start_date' => '2026-09-01',
    ]);

    $invoice = app(ConstructionInvoiceGeneratorService::class)->createConstructionInvoice($project);
    expect($invoice->status)->toBe(App\Enums\InvoiceStatus::Unpaid);

    app(ConstructionProjectService::class)->cancelProject($project, 'Pemilik rumah membatalkan rencana renovasi');

    expect($project->fresh()->status)->toBe(ConstructionStatus::Cancelled)
        ->and($invoice->fresh()->status)->toBe(App\Enums\InvoiceStatus::Cancelled);
});
