<?php

namespace App\Policies;

use App\Enums\RoleType;
use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    private function checkAccess(User $user): bool
    {
        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }
        if ($user->hasRole(RoleType::MANAGER->value)) {
            return true;
        }

        return $user->hasRole(RoleType::TAE->value);
    }

    public function viewAny(User $user): bool
    {
        return $this->checkAccess($user);
    }

    public function view(User $user, Student $student): bool
    {
        return $this->checkAccess($user);
    }

    public function create(User $user): bool
    {
        return $this->checkAccess($user);
    }

    public function update(User $user, Student $student): bool
    {
        return $this->checkAccess($user);
    }

    public function delete(User $user, Student $student): bool
    {
        return $this->checkAccess($user);
    }

    public function deleteAny(User $user): bool
    {
        return $this->checkAccess($user);
    }

    public function restore(User $user, Student $student): bool
    {
        return $this->checkAccess($user);
    }

    public function forceDelete(User $user, Student $student): bool
    {
        return $this->checkAccess($user);
    }
}
