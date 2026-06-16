<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleType;
use App\Models\User;
use App\Models\Course;

class CoursePolicy
{
    /**
     * Executado antes de qualquer verificação de autorização.
     */
    public function before(User $user): ?bool
    {
        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Course $course): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Course $course): bool
    {
        return true; // Permitida a edição para usuários comuns (Manager, por exemplo)
    }

    public function delete(User $user, Course $course): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
