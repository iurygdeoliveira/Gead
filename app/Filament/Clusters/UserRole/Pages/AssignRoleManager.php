<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole\Pages;

use App\Enums\RoleType;
use App\Models\TenantUser;
use Filament\Facades\Filament;
use Filament\Tables\Columns\ToggleColumn;

class AssignRoleManager extends BaseAssignRolePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'icon-admin';

    protected string $view = 'filament.clusters.user-role.pages.assign-role-manager';

    protected static ?string $title = 'Definir Gerentes';

    protected static ?string $navigationLabel = 'Usuários Gerentes';

    protected function getExtraColumns(): array
    {
        return [
            ToggleColumn::make('role_manager')
                ->label('Gerente')
                ->onColor('primary')
                ->offColor('danger')
                ->onIcon('heroicon-c-check')
                ->offIcon('heroicon-c-x-mark')
                ->getStateUsing(static function (TenantUser $record): bool {
                    return $record->user->isManagerOfTenant($record->tenant);
                })
                ->disabled(fn (TenantUser $record): bool => $record->user_id === Filament::auth()->id())
                ->tooltip(fn (TenantUser $record): ?string => $record->user_id === Filament::auth()->id()
                        ? 'Você não pode alterar a própria role'
                        : null
                )
                ->updateStateUsing(static function (TenantUser $record, bool $state): void {
                    if ($state) {
                        // Remove qualquer outra role que possa existir
                        $allRoles = $record->user->getRolesForTenant($record->tenant);
                        foreach ($allRoles as $role) {
                            if ($role->name !== RoleType::MANAGER->value) {
                                $record->user->removeRoleFromTenant($role->name, $record->tenant);
                            }
                        }

                        // Atribui role Manager no tenant específico
                        $roleManager = RoleType::ensureManagerRoleForTeam($record->tenant_id, 'web');
                        $record->user->assignRoleInTenant($roleManager, $record->tenant);
                    } else {
                        $record->user->removeRoleFromTenant(RoleType::MANAGER->value, $record->tenant);
                    }
                }),
        ];
    }
}
