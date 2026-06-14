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
        $team = \App\Models\Team::where('cnpj', '03.131.702/0001-33')->first();
        if (! $team) {
            $team = \App\Models\Team::create([
                'name' => 'Campus Araguaína',
                'slug' => 'campus-araguaina',
                'cnpj' => '03.131.702/0001-33',
                'is_active' => true,
                'is_personal' => false,
            ]);
        }

        $csvFiles = glob(database_path('seeders/dados de seed/*/Alunos*.csv'));

        if (empty($csvFiles)) {
            $this->command->error("Nenhum arquivo de alunos encontrado nos subdiretórios de seeders/dados de seed");
            return;
        }

        foreach ($csvFiles as $csvPath) {
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
                $entryPeriod = $row[9] ?? null;

                if (empty($email) || $email === 'None' || $email === '-') {
                    $email = null;
                }

                $cursoStr = $row[3] ?? null;
                $courseId = null;
                if (!empty($cursoStr) && $cursoStr !== '-') {
                    $parts = explode(' - ', $cursoStr, 2);
                    $code = trim($parts[0]);

                    // Force Analises Clinicas code to be 195 as commented by the user
                    if ($code === '210') {
                        $code = '195';
                    }

                    $course = \App\Models\Course::firstOrCreate(
                        ['code' => $code, 'team_id' => $team->id],
                        ['name' => isset($parts[1]) ? trim(str_replace('(Campus Araguaína)', '', $parts[1])) : 'Curso Desconhecido']
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

                // Create or update Student (Pessoa)
                $student = \App\Models\Student::updateOrCreate(
                    [
                        // Se tiver email, usamos email. Senão, usamos nome + team.
                        'name' => trim($name),
                        'team_id' => $team->id,
                    ],
                    [
                        'email' => $email ? trim($email) : null,
                        'user_id' => $user ? $user->id : null,
                    ]
                );

                // Create or update Enrollment (Vínculo do Aluno no Curso)
                if ($registrationNumber && $courseId) {
                    \App\Models\Enrollment::updateOrCreate(
                        [
                            'registration_number' => trim($registrationNumber),
                        ],
                        [
                            'student_id' => $student->id,
                            'course_id' => $courseId,
                            'entry_period' => $entryPeriod ? trim($entryPeriod) : null,
                        ]
                    );
                }
            }

            fclose($file);
        }
    }
}
