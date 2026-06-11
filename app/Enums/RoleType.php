<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Role;
use Filament\Support\Contracts\HasLabel;

enum RoleType: string implements HasLabel
{
    case ADMIN = 'Admin';
    case MANAGER = 'Manager';
    case TAE = 'Tae';
    case TEACHER = 'Teacher';
    case STUDENT = 'Student';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Super Administrador',
            self::MANAGER => 'Gerente de Ensino',
            self::TAE => 'TAE',
            self::TEACHER => 'Docente',
            self::STUDENT => 'Discente',
        };
    }

    public static function ensureGlobalRoles(string $guard): void
    {
        // Apenas Admin deve existir de forma global
        Role::firstOrCreate([
            'name' => self::ADMIN->value,
            'guard_name' => $guard,
        ]);
    }

    public static function ensureRoleForTeam(self $role, int $teamId, string $guard): Role
    {
        return Role::firstOrCreate([
            'team_id' => $teamId,
            'name' => $role->value,
            'guard_name' => $guard,
        ]);
    }
}
