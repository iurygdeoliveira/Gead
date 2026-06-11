<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeacherPolicy
{
    private function checkAccess(User $user): bool
    {
        return $user->hasRole(\App\Enums\RoleType::ADMIN->value) ||
               $user->hasRole(\App\Enums\RoleType::MANAGER->value) ||
               $user->hasRole(\App\Enums\RoleType::TAE->value);
    }

    public function viewAny(User $user): bool
    {
        return $this->checkAccess($user);
    }

    public function view(User $user, Teacher $teacher): bool
    {
        return $this->checkAccess($user);
    }

    public function create(User $user): bool
    {
        return $this->checkAccess($user);
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $this->checkAccess($user);
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $this->checkAccess($user);
    }

    public function restore(User $user, Teacher $teacher): bool
    {
        return $this->checkAccess($user);
    }

    public function forceDelete(User $user, Teacher $teacher): bool
    {
        return $this->checkAccess($user);
    }
}
