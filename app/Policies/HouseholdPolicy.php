<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Household;
use App\Models\User;

final class HouseholdPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Household $household): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Household $household): bool
    {
        return true;
    }

    public function delete(User $user, Household $household): bool
    {
        return $user->isAdmin() && ! $household->invoices()->exists();
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
