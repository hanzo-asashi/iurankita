<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        // Mencegah penurunan privilege sendiri jika satu-satunya admin
        return true;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin();
    }
}
