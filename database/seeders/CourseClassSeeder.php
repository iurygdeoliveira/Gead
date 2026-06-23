<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Team;
use Illuminate\Database\Seeder;

class CourseClassSeeder extends Seeder
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

        $academicTerm = AcademicTerm::firstOrCreate([
            'name' => '2026.1',
        ]);

        $files = [
            [
                'path' => database_path('seeders/dados de seed/Base_de_alunos_2026.1.csv'),
                'course_name_idx' => 5,
                'class_code_idx' => 8,
            ],
            [
                'path' => database_path('seeders/dados de seed/diarios_2026.1.csv'),
                'course_name_idx' => 7,
                'class_code_idx' => 5,
            ],
        ];

        // Cache courses to avoid many queries
        $courses = Course::where('team_id', $team->id)->get()->keyBy('name');

        foreach ($files as $fileConfig) {
            if (! file_exists($fileConfig['path'])) {
                continue;
            }

            $file = fopen($fileConfig['path'], 'r');
            $isHeader = true;

            while (($row = fgetcsv($file, 1000, ',', escape: '\\')) !== false) {
                if ($isHeader) {
                    $isHeader = false;

                    continue;
                }

                $courseName = trim($row[$fileConfig['course_name_idx']] ?? '');
                $classCode = trim($row[$fileConfig['class_code_idx']] ?? '');
                if ($courseName === '') {
                    continue;
                }
                if ($courseName === '0') {
                    continue;
                }
                if ($courseName === '-') {
                    continue;
                }
                if ($classCode === '') {
                    continue;
                }
                if ($classCode === '0') {
                    continue;
                }
                if ($classCode === '-') {
                    continue;
                }

                $course = $courses->get($courseName);
                if (! $course) {
                    continue;
                }

                // Extrair período de ingresso do código da turma se possível. Ex: 20261... -> 2026.1
                $entryPeriod = null;
                if (preg_match('/^(\d{4})(\d)/', $classCode, $matches)) {
                    $entryPeriod = $matches[1].'.'.$matches[2];
                }

                CourseClass::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'code' => $classCode,
                        'team_id' => $team->id,
                    ],
                    [
                        'name' => $classCode,
                        'entry_period' => $entryPeriod ?? 'Desconhecido',
                        'academic_term_id' => $academicTerm->id,
                    ]
                );
            }

            fclose($file);
        }
    }
}
