<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole\Pages;

use App\Enums\RoleType;
use App\Models\Role;
use App\Models\TenantUser;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ToggleColumn;

class AssignRoleUser extends BaseAssignRolePage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::User;

    protected string $view = 'filament.clusters.user-role.pages.assign-role-user';

    protected static ?string $title = 'Atribuir Funções de Usuário';

    protected static ?string $navigationLabel = 'Atribuir Funções';

    protected function getExtraColumns(): array
    {
        return [
            ToggleColumn::make('role_teacher')
                ->label('Professor')
                ->onColor('primary')
                ->offColor('danger')
                ->onIcon('heroicon-c-check')
                ->offIcon('heroicon-c-x-mark')
                ->getStateUsing(fn (TenantUser $record): bool => $record->user->getRolesForTenant($record->tenant)->contains('name', RoleType::TEACHER->value))
                ->disabled(fn (TenantUser $record): bool => $record->user_id === Filament::auth()->id())
                ->tooltip(fn (TenantUser $record): ?string => $record->user_id === Filament::auth()->id()
                        ? 'Você não pode alterar a própria role'
                        : null
                )
                ->updateStateUsing(function (TenantUser $record, bool $state): void {
                    $this->toggleRole($record, $state, RoleType::TEACHER);
                }),

            ToggleColumn::make('role_student')
                ->label('Aluno')
                ->onColor('primary')
                ->offColor('danger')
                ->onIcon('heroicon-c-check')
                ->offIcon('heroicon-c-x-mark')
                ->getStateUsing(fn (TenantUser $record): bool => $record->user->getRolesForTenant($record->tenant)->contains('name', RoleType::STUDENT->value))
                ->disabled(fn (TenantUser $record): bool => $record->user_id === Filament::auth()->id())
                ->tooltip(fn (TenantUser $record): ?string => $record->user_id === Filament::auth()->id()
                        ? 'Você não pode alterar a própria role'
                        : null
                )
                ->updateStateUsing(function (TenantUser $record, bool $state): void {
                    $this->toggleRole($record, $state, RoleType::STUDENT);
                }),

            ToggleColumn::make('role_employee')
                ->label('Funcionário')
                ->onColor('primary')
                ->offColor('danger')
                ->onIcon('heroicon-c-check')
                ->offIcon('heroicon-c-x-mark')
                ->getStateUsing(fn (TenantUser $record): bool => $record->user->getRolesForTenant($record->tenant)->contains('name', RoleType::EMPLOYEE->value))
                ->disabled(fn (TenantUser $record): bool => $record->user_id === Filament::auth()->id())
                ->tooltip(fn (TenantUser $record): ?string => $record->user_id === Filament::auth()->id()
                        ? 'Você não pode alterar a própria role'
                        : null
                )
                ->updateStateUsing(function (TenantUser $record, bool $state): void {
                    $this->toggleRole($record, $state, RoleType::EMPLOYEE);
                }),
        ];
    }

    private function toggleRole(TenantUser $record, bool $state, RoleType $roleType): void
    {
        if ($state) {
            $role = Role::firstOrCreate(
                ['team_id' => $record->tenant_id, 'name' => $roleType->value, 'guard_name' => 'web']
            );
            $record->user->assignRoleInTenant($role, $record->tenant);
        } else {
            $record->user->removeRoleFromTenant($roleType->value, $record->tenant);
        }
    }
}