<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\RoleType;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Filament\Facades\Filament;
use Spatie\Permission\PermissionRegistrar;

class UserPolicy
{
    /**
     * Método executado antes de qualquer verificação de autorização.
     * Permite acesso total para Admin e Manager do tenant atual.
     * Retorna null para continuar com as verificações específicas.
     */
    public function before(User $user): ?bool
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
     * Verifica se o usuário pode visualizar qualquer registro de usuário.
     * Usado para controlar a exibição de listagens/tabelas.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, Permission::VIEW);
    }

    /**
     * Verifica se o usuário pode visualizar um registro específico de usuário.
     * Usado para controlar acesso a páginas de detalhes.
     */
    public function view(User $user, User $record): bool
    {
        return $this->hasPermission($user, Permission::VIEW);
    }

    /**
     * Verifica se o usuário pode criar novos registros de usuário.
     * Usado para controlar a exibição de botões de criação e acesso a formulários.
     */
    public function create(User $user): bool
    {
        return $this->hasPermission($user, Permission::CREATE);
    }

    /**
     * Verifica se o usuário pode atualizar um registro específico de usuário.
     * Usado para controlar acesso a formulários de edição.
     */
    public function update(User $user, User $record): bool
    {
        return $this->hasPermission($user, Permission::UPDATE);
    }

    /**
     * Verifica se o usuário pode deletar um registro específico de usuário.
     * Usado para controlar a exibição de botões de exclusão individuais.
     */
    public function delete(User $user, User $record): bool
    {
        return $this->hasPermission($user, Permission::DELETE);
    }

    /**
     * Verifica se o usuário pode deletar qualquer registro de usuário.
     * Usado para controlar ações em massa de exclusão.
     */
    public function deleteAny(User $user): bool
    {
        return $this->hasPermission($user, Permission::DELETE);
    }

    /**
     * Verifica se o usuário possui a permissão específica para o recurso 'users'.
     * Configura o contexto do tenant antes de verificar a permissão,
     * garantindo que as permissões sejam verificadas no contexto correto.
     */
    private function hasPermission(User $user, Permission $permission): bool
    {
        $currentTenant = Filament::getTenant();

        if ($currentTenant instanceof Tenant) {
            $registrar = app(PermissionRegistrar::class);
            $registrar->setPermissionsTeamId($currentTenant->getKey());
            app(SpatieTeamResolver::class)->setPermissionsTeamId($currentTenant->getKey());
        }

        return $user->can($permission->for('users'));
    }
}
