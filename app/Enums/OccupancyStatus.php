<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum OccupancyStatus: string implements HasColor, HasLabel
{
    case Occupied = 'occupied';
    case Unoccupied = 'unoccupied';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Occupied => 'Dihuni',
            self::Unoccupied => 'Belum Dihuni',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Occupied => 'success',
            self::Unoccupied => 'warning',
        };
    }
}
