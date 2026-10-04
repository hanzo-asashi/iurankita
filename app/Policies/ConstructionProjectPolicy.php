<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ConstructionProject;
use App\Models\User;

final class ConstructionProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ConstructionProject $project): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ConstructionProject $project): bool
    {
        return true;
    }

    public function delete(User $user, ConstructionProject $project): bool
    {
        return $user->isAdmin();
    }
}
