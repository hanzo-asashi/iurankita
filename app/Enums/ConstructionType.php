<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ConstructionType: string implements HasLabel
{
    case Kitchen = 'kitchen';
    case BuildingAddition = 'building_addition';
    case Expansion = 'expansion';
    case Renovation = 'renovation';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Kitchen => 'Dapur',
            self::BuildingAddition => 'Penambahan Bangunan',
            self::Expansion => 'Perluasan Bangunan',
            self::Renovation => 'Renovasi',
            self::Other => 'Lainnya',
        };
    }
}
