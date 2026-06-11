<?php

declare(strict_types=1);

namespace App\Enums;

use BackedEnum;
use LaravelDaily\FilaTeams\Contracts\TeamRoleContract;

/**
 * Papel do usuário dentro de um Team (espelho de UI).
 *
 * Mapeamento para Spatie {@see RoleType}:
 *   - MANAGER ↔ RoleType::MANAGER
 *   - TAE     ↔ RoleType::TAE
 *   - TEACHER ↔ RoleType::TEACHER
 *   - STUDENT ↔ RoleType::STUDENT
 *
 * A autorização real continua via Spatie. Este enum só alimenta o pivot
 * `team_members.role` e a UI do FilaTeams (badges, dropdowns, convites).
 */
enum AppTeamRole: string implements TeamRoleContract
{
    case MANAGER = 'manager';
    case TAE = 'tae';
    case TEACHER = 'teacher';
    case STUDENT = 'student';

    public static function owner(): static
    {
        return self::MANAGER;
    }

    public static function default(): static
    {
        return self::STUDENT;
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function assignable(): array
    {
        $assignable = [];

        foreach (self::cases() as $role) {
            if ($role === self::MANAGER) {
                continue;
            }

            $assignable[] = [
                'value' => $role->value,
                'label' => $role->getLabel(),
            ];
        }

        return $assignable;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::MANAGER => RoleType::MANAGER->getLabel(),
            self::TAE => RoleType::TAE->getLabel(),
            self::TEACHER => RoleType::TEACHER->getLabel(),
            self::STUDENT => RoleType::STUDENT->getLabel(),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::MANAGER => 'danger',
            self::TAE => 'warning',
            self::TEACHER => 'success',
            self::STUDENT => 'info',
        };
    }

    /**
     * @return array<int, string|AppTeamPermission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::MANAGER => AppTeamPermission::cases(),
            self::TAE => [],
            self::TEACHER => [],
            self::STUDENT => [],
        };
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, array_map(
            static fn (mixed $p): string => $p instanceof BackedEnum ? $p->value : $p,
            $this->permissions()
        ), strict: true);
    }

    public function level(): int
    {
        return match ($this) {
            self::MANAGER => 4,
            self::TAE => 3,
            self::TEACHER => 2,
            self::STUDENT => 1,
        };
    }

    public function isAtLeast(TeamRoleContract $role): bool
    {
        return $this->level() >= $role->level();
    }

    /**
     * Mapeia este papel de pivot para o nome da role Spatie correspondente.
     */
    public function toSpatieRoleName(): string
    {
        return match ($this) {
            self::MANAGER => RoleType::MANAGER->value,
            self::TAE => RoleType::TAE->value,
            self::TEACHER => RoleType::TEACHER->value,
            self::STUDENT => RoleType::STUDENT->value,
        };
    }
}
