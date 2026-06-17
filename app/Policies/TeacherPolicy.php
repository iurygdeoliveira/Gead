<?php

namespace App\Policies;

use App\Enums\RoleType;
use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy
{
    private function checkAccess(User $user): bool
    {
        return $user->hasRole(RoleType::ADMIN->value) ||
               $user->hasRole(RoleType::MANAGER->value) ||
               $user->hasRole(RoleType::TAE->value);
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

    public function deleteAny(User $user): bool
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
