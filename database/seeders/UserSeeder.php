<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AppTeamRole;
use App\Enums\Permission as PermissionEnum;
use App\Enums\RoleType;
use App\Models\Membership;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');
        $resources = ['media', 'users', 'authentication-log'];

        foreach ($resources as $resource) {
            foreach (PermissionEnum::cases() as $permission) {
                PermissionModel::firstOrCreate([
                    'name' => $permission->for($resource),
                    'guard_name' => $guard,
                ]);
            }
        }

        RoleType::ensureGlobalRoles($guard);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@labsis.dev.br'],
            [
                'name' => 'Administrador',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
                'is_approved' => true,
                'approved_by' => null,
            ],
        );
        $globalResolver = resolve(SpatieTeamResolver::class);
        $globalResolver->setPermissionsTeamId(0);
        $admin->syncRoles([RoleType::ADMIN->value]);
        $globalResolver->setPermissionsTeamId(null);

        $adminRole = Role::where('name', RoleType::ADMIN->value)->where('guard_name', $guard)->first();
        if ($adminRole) {
            $adminRole->syncPermissions(PermissionModel::all());
        }

        $staffMembers = [
            [
                'name' => 'Walmir Jacinto de Sousa',
                'email' => 'walmir.sousa@ifto.edu.br',
            ],
            [
                'name' => 'Cassilda Alves dos Santos',
                'email' => 'cassilda.santos@ifto.edu.br',
            ],
            [
                'name' => 'Erica Feitosa Oliveira',
                'email' => 'erica.oliveira@ifto.edu.br',
            ],
        ];

        $campus = Team::where('cnpj', '03.131.702/0001-33')->first();
        if (! $campus) {
            $campus = Team::create([
                'name' => 'Campus Araguaína',
                'slug' => 'campus-araguaina',
                'cnpj' => '03.131.702/0001-33',
                'is_active' => true,
                'is_personal' => false,
            ]);
        }

        foreach ($staffMembers as $staffData) {
            $staff = User::query()->firstOrCreate(
                ['email' => $staffData['email']],
                [
                    'name' => $staffData['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('mudar123'), // Senha padrão para primeiro acesso
                    'is_approved' => true,
                    'approved_by' => $admin->id,
                ]
            );

            if ($staff->email === 'walmir.sousa@ifto.edu.br') {
                $this->ensureMembership($staff->id, $campus->id, AppTeamRole::MANAGER);
                
                \App\Models\Teacher::updateOrCreate(
                    ['email' => $staff->email],
                    [
                        'name' => $staff->name,
                        'team_id' => $campus->id,
                        'user_id' => $staff->id,
                    ]
                );
            } else {
                $this->ensureMembership($staff->id, $campus->id, AppTeamRole::TAE);
            }
        }

        $this->ensurePermissionsForTeam($campus->id, $guard);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function ensureMembership(int $userId, int $teamId, AppTeamRole $role): void
    {
        Membership::firstOrCreate(
            ['team_id' => $teamId, 'user_id' => $userId],
            ['role' => $role->value],
        );
    }

    /**
     * Garante que a role Owner do team contenha todas as permissões dos recursos.
     */
    private function ensurePermissionsForTeam(int $teamId, string $guard): void
    {
        $resources = ['media', 'users', 'authentication-log'];

        $teamResolver = resolve(SpatieTeamResolver::class);
        $teamResolver->setPermissionsTeamId($teamId);

        $managerRole = RoleType::ensureRoleForTeam(RoleType::MANAGER, $teamId, $guard);

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

        $teamResolver->setPermissionsTeamId(null);
    }
}
