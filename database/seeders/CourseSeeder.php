<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Team;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        $files = [
            [
                'path' => database_path('seeders/dados de seed/Base_de_alunos_2026.1.csv'),
                'code_idx' => 4,
                'name_idx' => 5,
            ],
            [
                'path' => database_path('seeders/dados de seed/diarios_2026.1.csv'),
                'code_idx' => 6,
                'name_idx' => 7,
            ],
        ];

        foreach ($files as $fileConfig) {
            if (! file_exists($fileConfig['path'])) {
                $this->command->error("Arquivo não encontrado: {$fileConfig['path']}");

                continue;
            }

            $file = fopen($fileConfig['path'], 'r');
            $isHeader = true;

            while (($row = fgetcsv($file, 1000, ',', escape: '\\')) !== false) {
                if ($isHeader) {
                    $isHeader = false;

                    continue;
                }

                $code = trim($row[$fileConfig['code_idx']] ?? '');
                $name = trim($row[$fileConfig['name_idx']] ?? '');
                if ($code === '') {
                    continue;
                }
                if ($code === '0') {
                    continue;
                }
                if ($code === '-') {
                    continue;
                }

                if ($name === '' || $name === '0') {
                    $name = "Curso $code";
                }
                if (str_contains($name, 'Qualificação Profissional')) {
                    continue;
                }
                if (str_contains($name, 'Assistente de Contabilidade')) {
                    continue;
                }

                Course::updateOrCreate(
                    ['name' => $name, 'team_id' => $team->id],
                    ['code' => $code]
                );
            }

            fclose($file);
        }
    }
}
