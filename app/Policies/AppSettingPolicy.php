<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AppSetting;
use App\Models\User;

final class AppSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AppSetting $setting): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, AppSetting $setting): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, AppSetting $setting): bool
    {
        return false;
    }
}
