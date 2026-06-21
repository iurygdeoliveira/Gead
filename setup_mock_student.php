<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Team;

$team = Team::first() ?? Team::create(['name' => 'Default Team', 'slug' => 'default-team']);

// Seleciona um aluno real que já tem turmas e disciplinas vinculadas.
// No nosso caso, o estudante de ID 1 (Aira Pereira Sabóia) possui 15 disciplinas pendentes.
$student = Student::find(1);

if (!$student) {
    echo "Estudante com ID 1 não encontrado no banco de dados.\n";
    exit(1);
}

// Verifica se já existe o user, senão cria um pra ele usando o email do aluno.
$user = clone User::firstOrCreate(
    ['email' => $student->email],
    [
        'name' => $student->name,
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
        'is_approved' => true,
    ]
);

// Atualiza o user caso ele já existisse mas estivesse sem as permissões certas.
$user->update([
    'password' => Hash::make('password'),
    'email_verified_at' => now(),
    'is_approved' => true,
]);

// Garante que o estudante está vinculado ao User criado.
$student->update(['user_id' => $user->id]);

// Reseta (deleta) todas as avaliações deste aluno para permitir testes
App\Models\Evaluation::whereHas('classEnrollment.enrollment.student', function ($q) use ($student) {
    $q->where('id', $student->id);
})->delete();

use App\Models\Role;
use App\Enums\RoleType;

// Adiciona ele ao time com a role de student.
DB::table('team_members')->updateOrInsert(
    ['team_id' => $team->id, 'user_id' => $user->id],
    ['role' => 'student', 'created_at' => now(), 'updated_at' => now()]
);

$role = Role::where('name', RoleType::STUDENT->value)->first();
if ($role) {
    $user->assignRoleInTeam($role, $team);
}

echo "Usuário de estudante real configurado com sucesso!\n";
echo "Nome: " . $user->name . "\n";
echo "Email (Login): " . $user->email . "\n";
echo "Senha: password\n";
