<?php

use App\Enums\RoleType;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Encontrar ou criar o usuário admin
        $user = User::query()->firstOrCreate(
            ['email' => 'iurygdeoliveira@gmail.com'],
            [
                'name' => 'Iury de Oliveira',
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(32)),
                'is_approved' => true,
                'approved_by' => null,
            ]
        );

        // 2. Garantir que está aprovado e com e-mail verificado se já existia
        if (!$user->is_approved || !$user->email_verified_at) {
            $user->update([
                'is_approved' => true,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        }

        // 3. Garantir a Role de Admin no escopo global (team_id = 0)
        $globalResolver = resolve(SpatieTeamResolver::class);
        $globalResolver->setPermissionsTeamId(0);
        
        RoleType::ensureGlobalRoles('web');

        $user->assignRole(RoleType::ADMIN->value);
        $globalResolver->setPermissionsTeamId(null);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $user = User::query()->where('email', 'iurygdeoliveira@gmail.com')->first();
        if ($user) {
            $globalResolver = resolve(SpatieTeamResolver::class);
            $globalResolver->setPermissionsTeamId(0);
            $user->removeRole(RoleType::ADMIN->value);
            $globalResolver->setPermissionsTeamId(null);
        }
    }
};
