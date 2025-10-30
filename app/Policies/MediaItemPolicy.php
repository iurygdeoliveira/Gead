<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\RoleType;
use App\Models\MediaItem;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Filament\Facades\Filament;
use Spatie\Permission\PermissionRegistrar;

class MediaItemPolicy
{
    /**
     * Verifica se o usuário tem acesso total (Admin ou Manager).
     * Retorna true se for Admin ou Manager do tenant atual.
     * Retorna null para continuar com verificações específicas de permissão.
     */
    private function hasAccess(User $user): ?bool
    {
        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }

        $currentTenant = Filament::getTenant();
        if ($currentTenant instanceof Tenant && $user->isManagerOfTenant($currentTenant)) {
            return true;
        }

        return null;
    }

    /**
     * Verifica se o usuário pode visualizar qualquer item de mídia.
     * Usado para controlar a exibição de listagens/tabelas.
     */
    public function viewAny(User $user): bool
    {
        $access = $this->hasAccess($user);
        if ($access !== null) {
            return $access;
        }

        $currentTenant = Filament::getTenant();
        if ($currentTenant instanceof Tenant) {
            return $this->hasPermission($user, Permission::VIEW, $currentTenant);
        }

        return $this->hasPermissionInAnyTenant($user, Permission::VIEW);
    }

    /**
     * Verifica se o usuário pode visualizar um item de mídia específico.
     * Usado para controlar acesso a páginas de detalhes.
     */
    public function view(User $user, MediaItem $record): bool
    {
        $access = $this->hasAccess($user);
        if ($access !== null) {
            return $access;
        }

        return $this->hasPermission($user, Permission::VIEW);
    }

    /**
     * Verifica se o usuário pode criar novos itens de mídia.
     * Usado para controlar a exibição de botões de criação e acesso a formulários.
     */
    public function create(User $user): bool
    {
        $access = $this->hasAccess($user);
        if ($access !== null) {
            return $access;
        }

        return $this->hasPermission($user, Permission::CREATE);
    }

    /**
     * Verifica se o usuário pode atualizar um item de mídia específico.
     * Usado para controlar acesso a formulários de edição.
     */
    public function update(User $user, MediaItem $record): bool
    {
        $access = $this->hasAccess($user);
        if ($access !== null) {
            return $access;
        }

        return $this->hasPermission($user, Permission::UPDATE);
    }

    /**
     * Verifica se o usuário pode deletar um item de mídia específico.
     * Usado para controlar a exibição de botões de exclusão individuais.
     */
    public function delete(User $user, MediaItem $record): bool
    {
        $access = $this->hasAccess($user);
        if ($access !== null) {
            return $access;
        }

        return $this->hasPermission($user, Permission::DELETE);
    }

    /**
     * Verifica se o usuário pode deletar qualquer item de mídia.
     * Usado para controlar ações em massa de exclusão.
     */
    public function deleteAny(User $user): bool
    {
        $access = $this->hasAccess($user);
        if ($access !== null) {
            return $access;
        }

        return $this->hasPermission($user, Permission::DELETE);
    }

    /**
     * Verifica se o usuário possui a permissão específica para o recurso 'media'.
     * Configura o contexto do tenant antes de verificar a permissão,
     * garantindo que as permissões sejam verificadas no contexto correto.
     */
    private function hasPermission(User $user, Permission $permission, ?Tenant $tenant = null): bool
    {
        if ($tenant instanceof Tenant) {
            $registrar = app(PermissionRegistrar::class);
            $registrar->setPermissionsTeamId($tenant->getKey());
            app(SpatieTeamResolver::class)->setPermissionsTeamId($tenant->getKey());
        }

        return $user->can($permission->for('media'));
    }

    private function hasPermissionInAnyTenant(User $user, Permission $permission): bool
    {
        $userTenants = $user->tenants()->where('is_active', true)->get();

        if ($userTenants->isEmpty()) {
            return false;
        }

        $tenantIds = $userTenants->pluck('id')->toArray();

        if ($tenantIds === []) {
            return false;
        }

        $permissionName = $permission->for('media');

        return
            $user->rolesWithTeams()
                ->wherePivotIn('team_id', $tenantIds)
                ->whereHas('permissions', function ($query) use ($permissionName) {
                    $query->where('name', $permissionName);
                })
                ->exists();
    }
}
