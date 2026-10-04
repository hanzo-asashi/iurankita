<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\InvoiceType;
use App\Enums\OccupancyStatus;
use App\Models\FeeRate;
use App\Models\Household;
use App\Models\Invoice;
use App\Services\Billing\MonthlyInvoiceGeneratorService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

final class GenerateMonthlyInvoices extends Page
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?string $navigationLabel = 'Generate Tagihan Bulanan';

    protected static ?string $title = 'Generate Tagihan Iuran Bulanan';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.generate-monthly-invoices';

    public function mount(): void
    {
        $this->form->fill([
            'month' => (int) now()->format('m'),
            'year' => (int) now()->format('Y'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->statePath('data')
            ->components([
                Section::make('Pilih Periode Tagihan')
                    ->description('Tentukan bulan dan tahun tagihan rutin yang akan dipratinjau dan diterbitkan.')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('month')
                                ->label('Bulan')
                                ->options([
                                    1 => 'Januari',
                                    2 => 'Februari',
                                    3 => 'Maret',
                                    4 => 'April',
                                    5 => 'Mei',
                                    6 => 'Juni',
                                    7 => 'Juli',
                                    8 => 'Agustus',
                                    9 => 'September',
                                    10 => 'Oktober',
                                    11 => 'November',
                                    12 => 'Desember',
                                ])
                                ->required()
                                ->live()
                                ->native(false),
                            Select::make('year')
                                ->label('Tahun')
                                ->options(fn (): array => collect(range((int) now()->format('Y') - 1, (int) now()->format('Y') + 2))
                                    ->mapWithKeys(fn (int $y): array => [$y => (string) $y])
                                    ->all()
                                )
                                ->required()
                                ->live()
                                ->native(false),
                        ]),
                    ]),
            ]);
    }

    public function getSelectedMonth(): int
    {
        return (int) ($this->data['month'] ?? now()->format('m'));
    }

    public function getSelectedYear(): int
    {
        return (int) ($this->data['year'] ?? now()->format('Y'));
    }

    /**
     * @return array{
     *     period_string: string,
     *     period_label: string,
     *     total_households: int,
     *     occupied_count: int,
     *     unoccupied_count: int,
     *     occupied_rate: int,
     *     unoccupied_rate: int,
     *     occupied_total: int,
     *     unoccupied_total: int,
     *     grand_total: int,
     *     already_generated_count: int,
     *     pending_to_generate: int
     * }
     */
    public function getPreviewDataProperty(): array
    {
        $period = Carbon::createFromDate($this->getSelectedYear(), $this->getSelectedMonth(), 1);
        $periodString = $period->format('Y-m');

        $activeHouseholds = Household::query()->active()->get();
        $occupiedCount = $activeHouseholds->where('occupancy_status', OccupancyStatus::Occupied)->count();
        $unoccupiedCount = $activeHouseholds->where('occupancy_status', OccupancyStatus::Unoccupied)->count();

        $occupiedRate = FeeRate::getActiveRate('monthly_occupied', $period)?->amount ?? 50000;
        $unoccupiedRate = FeeRate::getActiveRate('monthly_unoccupied', $period)?->amount ?? 35000;

        $occupiedTotal = $occupiedCount * $occupiedRate;
        $unoccupiedTotal = $unoccupiedCount * $unoccupiedRate;
        $grandTotal = $occupiedTotal + $unoccupiedTotal;

        $alreadyGeneratedCount = Invoice::query()
            ->where('invoice_type', InvoiceType::Monthly)
            ->where('billing_period', $periodString)
            ->count();

        $pendingToGenerate = max(0, $activeHouseholds->count() - $alreadyGeneratedCount);

        return [
            'period_string' => $periodString,
            'period_label' => $period->translatedFormat('F Y'),
            'total_households' => $activeHouseholds->count(),
            'occupied_count' => $occupiedCount,
            'unoccupied_count' => $unoccupiedCount,
            'occupied_rate' => $occupiedRate,
            'unoccupied_rate' => $unoccupiedRate,
            'occupied_total' => $occupiedTotal,
            'unoccupied_total' => $unoccupiedTotal,
            'grand_total' => $grandTotal,
            'already_generated_count' => $alreadyGeneratedCount,
            'pending_to_generate' => $pendingToGenerate,
        ];
    }

    public function generateInvoices(MonthlyInvoiceGeneratorService $generator): void
    {
        $period = Carbon::createFromDate($this->getSelectedYear(), $this->getSelectedMonth(), 1);
        $result = $generator->generate($period);

        Notification::make()
            ->title('Generate Tagihan Selesai')
            ->body("Berhasil menerbitkan {$result['generated_count']} tagihan baru ({$result['skipped_count']} dilewati karena sudah ada). Total nominal: Rp".number_format($result['total_amount'], 0, ',', '.'))
            ->success()
            ->send();
    }
}
