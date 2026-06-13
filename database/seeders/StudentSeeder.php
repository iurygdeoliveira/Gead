<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = base_path('docs/dados de seed/alunos.csv');

        if (! file_exists($csvPath)) {
            $this->command->error("Arquivo CSV não encontrado: {$csvPath}");
            return;
        }

        $team = \App\Models\Team::where('cnpj', '03.131.702/0001-33')->first();
        if (! $team) {
            $this->command->error("Campus Araguaína não encontrado (CNPJ: 03.131.702/0001-33)");
            return;
        }

        $file = fopen($csvPath, 'r');
        $isHeader = true;

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            $registrationNumber = $row[2] ?? null;
            $name = $row[1] ?? null;
            $email = $row[7] ?? null;

            if (empty($email) || $email === 'None' || $email === '-') {
                $email = null;
            }

            $cursoStr = $row[3] ?? null;
            $courseId = null;
            if (!empty($cursoStr) && $cursoStr !== '-') {
                $parts = explode(' - ', $cursoStr, 2);
                $code = trim($parts[0]);
                
                // Cursos do SUAP às vezes podem não estar ainda no banco de dados
                // Se precisarmos criá-lo automaticamente, seria aqui. Por ora, vamos buscar.
                $course = \App\Models\Course::firstOrCreate(
                    ['code' => $code, 'team_id' => $team->id],
                    ['name' => isset($parts[1]) ? trim($parts[1]) : 'Curso Desconhecido']
                );
                $courseId = $course->id;
            }

            if (empty($name)) {
                continue;
            }

            $user = null;
            if ($email) {
                $user = \App\Models\User::firstOrCreate(
                    ['email' => trim($email)],
                    [
                        'name' => trim($name),
                        'email_verified_at' => now(),
                        'password' => \Illuminate\Support\Facades\Hash::make('mudar123'),
                        'is_approved' => true,
                    ]
                );
            }

            \App\Models\Student::updateOrCreate(
                [
                    'registration_number' => trim($registrationNumber),
                ],
                [
                    'name' => trim($name),
                    'team_id' => $team->id,
                    'email' => $email ? trim($email) : null,
                    'course_id' => $courseId,
                    'user_id' => $user ? $user->id : null,
                ]
            );
        }

        fclose($file);
    }
}
