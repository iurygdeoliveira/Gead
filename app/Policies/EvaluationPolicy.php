<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RoleType;
use App\Models\User;
use App\Models\Evaluation;

class EvaluationPolicy
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

    public function view(User $user, Evaluation $evaluation): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Evaluation $evaluation): bool
    {
        // Apenas o Admin pode editar, o que já é coberto pelo before()
        // Para qualquer outro usuário, retorna false.
        return false;
    }

    public function delete(User $user, Evaluation $evaluation): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }
}
