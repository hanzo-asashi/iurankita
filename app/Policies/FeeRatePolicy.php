<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\FeeRate;
use App\Models\User;

final class FeeRatePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, FeeRate $feeRate): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, FeeRate $feeRate): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, FeeRate $feeRate): bool
    {
        return $user->isAdmin();
    }
}
