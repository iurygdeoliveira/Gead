<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\RoleType;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');

        $this->createPermissions($guard);
        $admin = $this->createAdminUser($guard);

        $tenant = $this->createTenant('CAMPUS ARAGUAINA');

        $this->setupTenant($tenant, $admin, $guard);

        // Limpa o cache de permissões para garantir que as alterações sejam aplicadas
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function createPermissions(string $guard): void
    {
        $resources = ['media', 'users'];

        foreach ($resources as $resource) {
            foreach (PermissionEnum::cases() as $permission) {
                PermissionModel::firstOrCreate([
                    'name' => $permission->for($resource),
                    'guard_name' => $guard,
                ]);
            }
        }
    }

    private function createAdminUser(string $guard): User
    {
        RoleType::ensureGlobalRoles($guard);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@labsis.dev.br'],
            [
                'name' => 'Administrador',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
                'is_approved' => true,
                'approved_by' => null, // Admin se auto-aprova
            ],
        );

        // Global (sem tenant): fixar team_id = 0 para atribuições globais
        $globalResolver = app(SpatieTeamResolver::class);
        $globalResolver->setPermissionsTeamId(0);
        $admin->syncRoles([RoleType::ADMIN->value]);
        // Reset do resolver para evitar vazamento de contexto
        $globalResolver->setPermissionsTeamId(null);

        // Garante que a role Admin possua todas as permissões
        $adminRole = Role::where('name', RoleType::ADMIN->value)->where('guard_name', $guard)->first();
        if ($adminRole) {
            $adminRole->syncPermissions(PermissionModel::all());
        }

        return $admin;
    }

    private function createTenant(string $name): Tenant
    {
        return Tenant::firstOrCreate(
            ['name' => $name],
            [
                'uuid' => (string) Str::uuid(),
                'is_active' => true,
            ],
        );
    }

    private function setupTenant(Tenant $tenant, User $admin, string $guard): void
    {
        // Cria usuários para cada role no tenant
        $users = $this->createTenantUsers($admin);

        // Vincula os usuários ao tenant
        foreach ($users as $user) {
            $user->tenants()->sync([$tenant->id]);
        }

        // Garante que as roles existam para o tenant
        $roleManager = RoleType::ensureManagerRoleForTeam($tenant->id, $guard);
        $roleTeacher = RoleType::ensureTeacherRoleForTeam($tenant->id, $guard);
        $roleStudent = RoleType::ensureStudentRoleForTeam($tenant->id, $guard);
        $roleEmployee = RoleType::ensureEmployeeRoleForTeam($tenant->id, $guard);

        // Atribui permissões para a role de Manager
        $this->assignPermissionsToManagerRole($roleManager, $tenant->id, $guard);

        // Atribui as roles aos usuários dentro do tenant
        $users['manager']->assignRoleInTenant($roleManager, $tenant);
        $users['teacher']->assignRoleInTenant($roleTeacher, $tenant);
        $users['student']->assignRoleInTenant($roleStudent, $tenant);
        $users['employee']->assignRoleInTenant($roleEmployee, $tenant);
    }

    private function createTenantUsers(User $admin): array
    {
        $manager = User::query()->firstOrCreate(
            ['email' => 'gerente.araguaina@labsis.dev.br'],
            [
                'name' => 'Carlos Silva',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
                'is_approved' => true,
                'approved_by' => $admin->id,
            ],
        );

        $teacher = User::query()->firstOrCreate(
            ['email' => 'professor.araguaina@labsis.dev.br'],
            [
                'name' => 'Mariana Costa',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
                'is_approved' => true,
                'approved_by' => $admin->id,
            ],
        );

        $student = User::query()->firstOrCreate(
            ['email' => 'aluno.araguaina@labsis.dev.br'],
            [
                'name' => 'João Pereira',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
                'is_approved' => true,
                'approved_by' => $admin->id,
            ],
        );

        $employee = User::query()->firstOrCreate(
            ['email' => 'funcionario.araguaina@labsis.dev.br'],
            [
                'name' => 'Ana Souza',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
                'is_approved' => true,
                'approved_by' => $admin->id,
            ],
        );

        return compact('manager', 'teacher', 'student', 'employee');
    }

    /**
     * Atribui permissões à role Manager para um tenant específico
     */
    private function assignPermissionsToManagerRole(Role $managerRole, int $tenantId, string $guard): void
    {
        $resources = ['media', 'users'];

        // Configurar o contexto de team para o tenant
        $teamResolver = app(SpatieTeamResolver::class);
        $teamResolver->setPermissionsTeamId($tenantId);

        // Manager recebe todas as permissões
        foreach ($resources as $resource) {
            foreach (PermissionEnum::cases() as $permission) {
                $permissionName = $permission->for($resource);
                PermissionModel::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => $guard,
                ]);

                if (! $managerRole->hasPermissionTo($permissionName, $guard)) {
                    $managerRole->givePermissionTo($permissionName);
                }
            }
        }

        // Reset do resolver para evitar vazamento de contexto
        $teamResolver->setPermissionsTeamId(null);
    }
}
