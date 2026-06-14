<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        $file = fopen($csvPath, 'r');
        $isHeader = true;

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            $name = $row[1] ?? null;
            $registrationNumber = $row[2] ?? null;
            $email = $row[4] ?? null;

            if (empty($name) || empty($email) || $email === '-') {
                continue;
            }

            if (trim($email) === 'walmir.sousa@ifto.edu.br') {
                continue;
            }

            $user = \App\Models\User::firstOrCreate(
                ['email' => trim($email)],
                [
                    'name' => trim($name),
                    'email_verified_at' => now(),
                    'password' => \Illuminate\Support\Facades\Hash::make('mudar123'),
                    'is_approved' => true,
                ]
            );

            \App\Models\Teacher::updateOrCreate(
                ['email' => trim($email)],
                [
                    'name' => trim($name),
                    'registration_number' => $registrationNumber ? trim($registrationNumber) : null,
                    'team_id' => $team->id,
                    'user_id' => $user->id,
                ]
            );
        }

        fclose($file);
    }
}
