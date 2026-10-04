<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasColor, HasLabel
{
    case Admin = 'admin';
    case Staff = 'staff';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Staff => 'Petugas',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Admin => 'primary',
            self::Staff => 'info',
        };
    }
}
