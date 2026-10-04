<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PaymentConfirmation;
use App\Models\User;

final class PaymentConfirmationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function view(User $user, PaymentConfirmation $confirmation): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function create(User $user): bool
    {
        return false; // Created from public portal
    }

    public function update(User $user, PaymentConfirmation $confirmation): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    public function delete(User $user, PaymentConfirmation $confirmation): bool
    {
        return $user->isAdmin();
    }
}
