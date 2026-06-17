<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/dados de seed/diarios.csv');

        if (! file_exists($csvPath)) {
            $this->command->error("Arquivo CSV não encontrado: {$csvPath}");
            return;
        }

        $team = Team::where('cnpj', '03.131.702/0001-33')->first();
        if (! $team) {
            $team = Team::create([
                'name' => 'Campus Araguaína',
                'slug' => 'campus-araguaina',
                'cnpj' => '03.131.702/0001-33',
                'is_active' => true,
                'is_personal' => false,
            ]);
        }

        DB::transaction(function () use ($csvPath, $team) {
            $file = fopen($csvPath, 'r');
            $isHeader = true;

            $teachersToInsert = [];

            while (($row = fgetcsv($file, 1000, ',')) !== false) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                $professoresRaw = trim($row[4] ?? '');

                if (empty($professoresRaw) || $professoresRaw === '-') {
                    continue;
                }

                // O formato pode ser "Nome (Matrícula), Nome2 (Matrícula2)"
                // Usaremos regex para extrair os nomes e matrículas
                preg_match_all('/([^(),]+)\s*\((\d+)\)/', $professoresRaw, $matches, PREG_SET_ORDER);

                if (empty($matches)) {
                    // Tenta ver se é só um nome sem matrícula
                    $parts = explode(',', $professoresRaw);
                    foreach ($parts as $part) {
                        $name = trim($part);
                        if (!empty($name)) {
                            $teachersToInsert[$name] = null; // null registration_number
                        }
                    }
                } else {
                    foreach ($matches as $match) {
                        $name = trim($match[1]);
                        // Remover caracteres extras do início, se houver (ex: aspas)
                        $name = trim($name, " \t\n\r\0\x0B\"");
                        if (str_starts_with(strtolower($name), 'e ')) {
                            $name = substr($name, 2);
                        }
                        $registrationNumber = trim($match[2]);
                        
                        if (!empty($name)) {
                            $teachersToInsert[$name] = $registrationNumber;
                        }
                    }
                }
            }

            fclose($file);

            // Agora cadastra todos os professores únicos encontrados no diários
            foreach ($teachersToInsert as $name => $registrationNumber) {
                Teacher::updateOrCreate(
                    [
                        'name' => $name,
                        'team_id' => $team->id,
                    ],
                    [
                        'registration_number' => $registrationNumber,
                        'user_id' => null,
                    ]
                );
            }

            // Agora processa o arquivo de e-mails para atualizar os e-mails e incluir docentes faltantes (como o gerente)
            $emailsCsvPath = database_path('seeders/dados de seed/email dos professores.csv');
            if (file_exists($emailsCsvPath)) {
                $emailFile = fopen($emailsCsvPath, 'r');
                $isEmailHeader = true;
                
                while (($row = fgetcsv($emailFile, 1000, ',')) !== false) {
                    if ($isEmailHeader) {
                        $isEmailHeader = false;
                        continue;
                    }

                    $name = trim($row[1] ?? '');
                    $registrationNumber = trim($row[2] ?? '');
                    $emailRaw = trim($row[4] ?? '');
                    
                    $email = (!empty($emailRaw) && $emailRaw !== '-') ? $emailRaw : null;

                    if (empty($name)) {
                        continue;
                    }

                    // Se a matrícula não estiver vazia, pode ser útil tentar buscar por matrícula também, 
                    // mas como a base estava criando por nome, mantemos a consistência
                    $teacher = Teacher::where('name', $name)->where('team_id', $team->id)->first();
                    
                    if ($teacher) {
                        $teacher->update([
                            'email' => $email,
                        ]);
                    } else {
                        // O professor (ou gerente) não estava no diarios.csv, vamos criá-lo
                        Teacher::create([
                            'name' => $name,
                            'team_id' => $team->id,
                            'registration_number' => $registrationNumber,
                            'email' => $email,
                            'user_id' => null,
                        ]);
                    }
                }
                
                fclose($emailFile);
            }
        });
    }
}
