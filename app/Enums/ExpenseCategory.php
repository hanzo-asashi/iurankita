<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ExpenseCategory: string implements HasColor, HasLabel
{
    case WasteManagement = 'waste_management';
    case Security = 'security';
    case FacilityMaintenance = 'facility_maintenance';
    case StreetLighting = 'street_lighting';
    case Events = 'events';
    case Administration = 'administration';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::WasteManagement => 'Kebersihan & Pengangkutan Sampah',
            self::Security => 'Keamanan Lingkungan',
            self::FacilityMaintenance => 'Pemeliharaan Fasilitas & Jalan',
            self::StreetLighting => 'Penerangan Jalan Lingkungan',
            self::Events => 'Kegiatan Warga & Gotong Royong',
            self::Administration => 'Administrasi & Operasional Pengurus',
            self::Other => 'Lain-lain',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::WasteManagement => 'emerald',
            self::Security => 'indigo',
            self::FacilityMaintenance => 'amber',
            self::StreetLighting => 'warning',
            self::Events => 'purple',
            self::Administration => 'info',
            self::Other => 'gray',
        };
    }
}
