<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Role;
use Filament\Support\Contracts\HasLabel;

enum RoleType: string implements HasLabel
{
    case ADMIN = 'Admin';
    case MANAGER = 'Manager';
    case TEACHER = 'Teacher';
    case STUDENT = 'Student';
    case EMPLOYEE = 'Employee';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::MANAGER => 'Gerente',
            self::TEACHER => 'Professor',
            self::STUDENT => 'Aluno',
            self::EMPLOYEE => 'Funcionário',
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

    public static function ensureManagerRoleForTeam(int $teamId, string $guard): Role
    {
        return Role::firstOrCreate([
            'team_id' => $teamId,
            'name' => self::MANAGER->value,
            'guard_name' => $guard,
        ]);
    }

    public static function ensureTeacherRoleForTeam(int $teamId, string $guard): Role
    {
        return Role::firstOrCreate([
            'team_id' => $teamId,
            'name' => self::TEACHER->value,
            'guard_name' => $guard,
        ]);
    }

    public static function ensureStudentRoleForTeam(int $teamId, string $guard): Role
    {
        return Role::firstOrCreate([
            'team_id' => $teamId,
            'name' => self::STUDENT->value,
            'guard_name' => $guard,
        ]);
    }

    public static function ensureEmployeeRoleForTeam(int $teamId, string $guard): Role
    {
        return Role::firstOrCreate([
            'team_id' => $teamId,
            'name' => self::EMPLOYEE->value,
            'guard_name' => $guard,
        ]);
    }
}
