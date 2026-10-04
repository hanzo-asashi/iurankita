<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ConstructionStatus;
use App\Enums\ConstructionType;
use App\Enums\OccupancyStatus;
use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\AppSetting;
use App\Models\ConstructionProject;
use App\Models\FeeRate;
use App\Models\Household;
use App\Models\User;
use App\Services\Billing\ConstructionInvoiceGeneratorService;
use App\Services\Billing\MonthlyInvoiceGeneratorService;
use App\Services\Payment\PaymentRecorderService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@iurankita.test'],
            [
                'name' => 'Administrator IuranKita',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'phone' => '0812-3456-7890',
            ]
        );

        $staff = User::firstOrCreate(
            ['email' => 'petugas@iurankita.test'],
            [
                'name' => 'Budi Hartono (Petugas)',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'phone' => '0812-9876-5432',
            ]
        );

        // 2. Default Fee Rates
        FeeRate::firstOrCreate(
            ['code' => 'monthly_occupied'],
            [
                'name' => 'Iuran Rutin Rumah Dihuni',
                'amount' => 50000,
                'effective_from' => '2026-01-01',
                'is_active' => true,
                'description' => 'Iuran rutin bulanan untuk rumah yang telah ditinggali / berpenghuni.',
            ]
        );

        FeeRate::firstOrCreate(
            ['code' => 'monthly_unoccupied'],
            [
                'name' => 'Iuran Rutin Rumah Belum Dihuni',
                'amount' => 35000,
                'effective_from' => '2026-01-01',
                'is_active' => true,
                'description' => 'Iuran rutin bulanan untuk rumah kosong / belum ditinggali.',
            ]
        );

        FeeRate::firstOrCreate(
            ['code' => 'construction_one_time'],
            [
                'name' => 'Iuran Pembangunan — Sekali Bayar',
                'amount' => 100000,
                'effective_from' => '2026-01-01',
                'is_active' => true,
                'description' => 'Iuran pembangunan satu kali bayar per kegiatan pembangunan (dapur, renovasi, dsb). Bukan biaya bulanan.',
            ]
        );

        // 3. App Settings (Del Mattappa Residence)
        $setting = AppSetting::current();
        $setting->update([
            'complex_name' => 'Del Mattappa Residence',
            'app_name' => 'IuranKita',
            'address' => 'Jl. Poros Del Mattappa Residence, Kab. Soppeng',
        ]);

        // 4. Data Warga Del Matappa Residence (32 Rumah/KK)
        $wargaDelMatappa = [
            // Blok A
            ['house_code' => 'A-01', 'block' => 'A', 'house_number' => '01', 'head_of_family' => 'Widyawati Jafar', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4001-0001'],
            ['house_code' => 'A-02', 'block' => 'A', 'house_number' => '02', 'head_of_family' => 'Alwi', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4001-0002'],
            ['house_code' => 'A-03', 'block' => 'A', 'house_number' => '03', 'head_of_family' => 'Wandi Zainuddin', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4001-0003'],
            ['house_code' => 'A-04', 'block' => 'A', 'house_number' => '04', 'head_of_family' => 'Rahim', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4001-0004'],
            ['house_code' => 'A-07', 'block' => 'A', 'house_number' => '07', 'head_of_family' => 'Mustari', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4001-0007'],
            ['house_code' => 'A-08', 'block' => 'A', 'house_number' => '08', 'head_of_family' => 'Nurul Fadillah', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4001-0008'],
            ['house_code' => 'A-09', 'block' => 'A', 'house_number' => '09', 'head_of_family' => 'Nurfaidah', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4001-0009'],

            // Blok B
            ['house_code' => 'B-02', 'block' => 'B', 'house_number' => '02', 'head_of_family' => 'Heka Saputri', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4002-0002'],
            ['house_code' => 'B-09', 'block' => 'B', 'house_number' => '09', 'head_of_family' => 'Ilham', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4002-0009'],
            ['house_code' => 'B-13', 'block' => 'B', 'house_number' => '13', 'head_of_family' => 'Harumin', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4002-0013'],

            // Blok C
            ['house_code' => 'C-01', 'block' => 'C', 'house_number' => '01', 'head_of_family' => 'A. Erfina', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4003-0001'],
            ['house_code' => 'C-04', 'block' => 'C', 'house_number' => '04', 'head_of_family' => 'Adil', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4003-0004'],
            ['house_code' => 'C-06', 'block' => 'C', 'house_number' => '06', 'head_of_family' => 'Wahyu', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4003-0006'],
            ['house_code' => 'C-09', 'block' => 'C', 'house_number' => '09', 'head_of_family' => 'Mirwang', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4003-0009'],
            ['house_code' => 'C-10', 'block' => 'C', 'house_number' => '10', 'head_of_family' => 'Irwan', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4003-0010', 'monthly_fee_override' => 100000],
            ['house_code' => 'C-12', 'block' => 'C', 'house_number' => '12', 'head_of_family' => 'Abdul Kadir', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4003-0012'],
            ['house_code' => 'C-14', 'block' => 'C', 'house_number' => '14', 'head_of_family' => 'Asniar', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4003-0014'],
            ['house_code' => 'C-15', 'block' => 'C', 'house_number' => '15', 'head_of_family' => 'Irma Kismala Dewi', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4003-0015'],
            ['house_code' => 'C-16', 'block' => 'C', 'house_number' => '16', 'head_of_family' => 'Dewi', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4003-0016'],
            ['house_code' => 'C-17', 'block' => 'C', 'house_number' => '17', 'head_of_family' => 'Adzan', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4003-0017'],
            ['house_code' => 'C-19', 'block' => 'C', 'house_number' => '19', 'head_of_family' => 'Tara', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4003-0019'],

            // Blok D
            ['house_code' => 'D-01', 'block' => 'D', 'house_number' => '01', 'head_of_family' => 'Umar', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4004-0001'],
            ['house_code' => 'D-03', 'block' => 'D', 'house_number' => '03', 'head_of_family' => 'Andi Kaharmawi', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4004-0003'],
            ['house_code' => 'D-04', 'block' => 'D', 'house_number' => '04', 'head_of_family' => 'Feby', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4004-0004'],
            ['house_code' => 'D-05', 'block' => 'D', 'house_number' => '05', 'head_of_family' => 'Hansen', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4004-0005'],
            ['house_code' => 'D-06', 'block' => 'D', 'house_number' => '06', 'head_of_family' => 'Angga Saputra', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4004-0006'],
            ['house_code' => 'D-07', 'block' => 'D', 'house_number' => '07', 'head_of_family' => 'Muh Takdir', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4004-0007'],
            ['house_code' => 'D-08', 'block' => 'D', 'house_number' => '08', 'head_of_family' => 'Yusril', 'occupancy_status' => OccupancyStatus::Occupied, 'phone' => '0812-4004-0008'],
            ['house_code' => 'D-09', 'block' => 'D', 'house_number' => '09', 'head_of_family' => 'Hj Lina', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4004-0009'],
            ['house_code' => 'D-10', 'block' => 'D', 'house_number' => '10', 'head_of_family' => 'Irwan', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4004-0010'],
            ['house_code' => 'D-11', 'block' => 'D', 'house_number' => '11', 'head_of_family' => 'A. Rezka', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4004-0011'],
            ['house_code' => 'D-14', 'block' => 'D', 'house_number' => '14', 'head_of_family' => 'Nurul', 'occupancy_status' => OccupancyStatus::Unoccupied, 'phone' => '0812-4004-0014'],
        ];

        /** @var array<string, Household> $createdHouseholds */
        $createdHouseholds = [];
        foreach ($wargaDelMatappa as $data) {
            $createdHouseholds[$data['house_code']] = Household::firstOrCreate(
                ['house_code' => $data['house_code']],
                [
                    'block' => $data['block'],
                    'house_number' => $data['house_number'],
                    'head_of_family' => $data['head_of_family'],
                    'occupancy_status' => $data['occupancy_status'],
                    'phone' => $data['phone'],
                    'is_active' => true,
                ]
            );
        }

        // 5. Generate Monthly Invoices for September 2026 & October 2026
        $monthlyGenerator = app(MonthlyInvoiceGeneratorService::class);
        $prevMonth = Carbon::create(2026, 9, 1);
        $currMonth = Carbon::create(2026, 10, 1);

        $prevResult = $monthlyGenerator->generate($prevMonth);
        $currResult = $monthlyGenerator->generate($currMonth);

        // 6. Record Payments for September 2026
        $paymentRecorder = app(PaymentRecorderService::class);

        foreach ($prevResult['invoices'] as $invoice) {
            $houseCode = $invoice->household->house_code;

            // Untuk Irwan C-10: Pembayaran September adalah Iuran Pembangunan (100rb).
            // Tagihan rutin C-10 dibiarkan unpaid sebagai contoh status tagihan rutin berjalan.
            if ($houseCode === 'C-10') {
                continue;
            }

            $method = in_array($houseCode, ['A-03', 'B-13', 'C-01', 'D-04', 'D-08'], true)
                ? PaymentMethod::Cash
                : PaymentMethod::Transfer;

            rand(3, 10)
                |> $prevMonth->copy()(...)
                |> (fn ($x) => $paymentRecorder->recordPayment(invoice: $invoice, amount: $invoice->balance, paymentMethod: $method, paymentDate: $x, receivedBy: $staff->id, referenceNumber: 'TRX-SEP-'.$houseCode, notes: 'Pembayaran lunas iuran rutin bulan September 2026'));
        }

        // 7. Iuran Pembangunan Irwan C-10 (Rp100.000) & Proyek Tambahan
        $constructionInvoiceGenerator = app(ConstructionInvoiceGeneratorService::class);

        // Proyek 1: Irwan C-10 - Renovasi / Pembangunan (Aktif, Lunas Rp100.000)
        $pIrwan = ConstructionProject::firstOrCreate(
            ['household_id' => $createdHouseholds['C-10']->id, 'project_type' => ConstructionType::Renovation],
            [
                'description' => 'Pembangunan / renovasi rumah Blok C-10',
                'start_date' => Carbon::create(2026, 9, 1),
                'status' => ConstructionStatus::Active,
                'one_time_fee' => 100000,
                'created_by' => $admin->id,
                'notes' => 'Iuran pembangunan satu kali bayar (September 2026)',
            ]
        );

        $pIrwanInvoice = $constructionInvoiceGenerator->createConstructionInvoice($pIrwan);
        if ($pIrwanInvoice->balance > 0) {
            $paymentRecorder->recordPayment(
                invoice: $pIrwanInvoice,
                amount: 100000,
                paymentMethod: PaymentMethod::Transfer,
                paymentDate: Carbon::create(2026, 9, 8),
                receivedBy: $staff->id,
                referenceNumber: 'TRX-PEMB-C10',
                notes: 'Pembayaran lunas iuran pembangunan rumah Blok C10'
            );
        }

        // 8. Beberapa Pembayaran Awal Bulan Berjalan (Oktober 2026)
        $earlyPayers = ['A-03', 'D-01', 'C-15'];
        foreach ($earlyPayers as $code) {
            if (isset($createdHouseholds[$code])) {
                $currInvoice = $currResult['invoices']->firstWhere('household_id', $createdHouseholds[$code]->id);
                if ($currInvoice && $currInvoice->balance > 0) {
                    $paymentRecorder->recordPayment(
                        invoice: $currInvoice,
                        amount: $currInvoice->balance,
                        paymentMethod: PaymentMethod::Transfer,
                        paymentDate: Carbon::create(2026, 10, 2),
                        receivedBy: $staff->id,
                        referenceNumber: 'TRX-OKT-'.$code,
                        notes: 'Pembayaran awal bulan Oktober 2026'
                    );
                }
            }
        }
    }
}
