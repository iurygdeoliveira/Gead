<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Team;
use App\Models\User;
use App\Models\Teacher;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/dados de seed/professores.csv');

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
            $passwordHash = Hash::make('mudar123');
            $now = now();

            while (($row = fgetcsv($file, 1000, ',')) !== false) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                $name = $row[1] ?? null;
                $registrationNumber = $row[2] ?? null;
                $email = $row[4] ?? null;

                if (empty($name)) {
                    continue;
                }

                if (empty($email) || $email === 'None' || trim($email) === '-') {
                    $email = null;
                }

                if ($email && trim($email) === 'walmir.sousa@ifto.edu.br') {
                    continue;
                }

                $user = null;
                if ($email) {
                    $user = User::firstOrCreate(
                        ['email' => trim($email)],
                        [
                            'name' => trim($name),
                            'email_verified_at' => $now,
                            'password' => $passwordHash,
                            'is_approved' => true,
                        ]
                    );
                }

                if ($email) {
                    Teacher::updateOrCreate(
                        ['email' => trim($email)],
                        [
                            'name' => trim($name),
                            'registration_number' => $registrationNumber ? trim($registrationNumber) : null,
                            'team_id' => $team->id,
                            'user_id' => $user ? $user->id : null,
                        ]
                    );
                } else {
                    Teacher::updateOrCreate(
                        [
                            'name' => trim($name),
                            'team_id' => $team->id,
                        ],
                        [
                            'email' => null,
                            'registration_number' => $registrationNumber ? trim($registrationNumber) : null,
                            'user_id' => null,
                        ]
                    );
                }
            }

            fclose($file);
        });
    }
}
