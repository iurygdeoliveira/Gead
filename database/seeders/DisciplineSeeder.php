<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Discipline;

class DisciplineSeeder extends Seeder
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

        $filesToCoursesMap = [
            'Tecnico subsequente em Analises Clinicas/disciplinas_analises_clinicas_2021.csv' => ['195'],
            'Tecnico em Biotecnologia/disciplinas_biotecnologia_2021.csv' => ['211'],
            'Tecnico subsequente em Enfermagem/disciplinas_enfermagem_2023.csv' => ['194'],
            'Bacharelado em Farmacia/disciplinas_farmacia_2024.csv' => ['280'],
            'Tecnologo em Gestao da Produção Industrial/disciplinas_gpi_2023.csv' => ['097', '97'],
            'Tecnico em Informatica/disciplinas_info_2021.csv' => ['213'],
            'Tecnico em Planejamento e Controle da Produção/disciplinas_pcp_2026.csv' => ['349'],
            'Tecnologo em Analise e Desenvolvimento de Sistemas/disciplinas_tads_2022.csv' => ['216'],
        ];

        foreach ($filesToCoursesMap as $filename => $courseCodes) {
            $csvPath = database_path("seeders/dados de seed/{$filename}");
            if (!file_exists($csvPath)) {
                $this->command->error("Arquivo não encontrado: {$csvPath}");
                continue;
            }

            $courses = Course::whereIn('code', $courseCodes)->where('team_id', $team->id)->get();
            if ($courses->isEmpty()) {
                $this->command->warn("Nenhum curso encontrado com os códigos: " . implode(', ', $courseCodes) . " para o arquivo {$filename}");
                continue;
            }

            $file = fopen($csvPath, 'r');
            $isHeader = true;
            while (($row = fgetcsv($file, 1000, ',')) !== false) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                $period = empty($row[1]) ? null : trim($row[1]);
                $code = empty($row[2]) ? null : trim($row[2]);
                $component = $row[3] ?? '';

                if (empty($component) || trim($component) === '-') {
                    continue;
                }

                $parts = explode(' - ', $component, 2);
                $name = isset($parts[1]) ? trim($parts[1]) : trim($component);
                $name = preg_replace('/^Att\d+\s*-\s*/i', '', $name);

                foreach ($courses as $course) {
                    Discipline::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'code' => $code,
                        ],
                        [
                            'name' => $name,
                            'period' => $period,
                        ]
                    );
                }
            }
            fclose($file);
        }
    }
}
