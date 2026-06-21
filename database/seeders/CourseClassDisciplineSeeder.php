<?php

namespace Database\Seeders;

use App\Models\CourseClass;
use App\Models\Discipline;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseClassDisciplineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/dados de seed/diarios_2026.1.csv');

        if (! file_exists($csvPath)) {
            $this->command->error("Arquivo CSV não encontrado: {$csvPath}");
            return;
        }

        $allClasses = CourseClass::all()->keyBy('code');
        $allDisciplines = Discipline::all()->keyBy('code');

        DB::transaction(function () use ($csvPath, $allClasses, $allDisciplines) {
            $file = fopen($csvPath, 'r');
            $isHeader = true;

            $failures = [];

            while (($row = fgetcsv($file, 1000, ',')) !== false) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                $sigla = trim($row[2] ?? '');
                $professoresRaw = trim($row[4] ?? '');
                $classCode = trim($row[5] ?? '');

                if (empty($sigla) || empty($classCode) || empty($professoresRaw) || $professoresRaw === '-') {
                    continue;
                }

                $courseClass = $allClasses->get($classCode);
                $discipline = $allDisciplines->get($sigla);

                if (!$courseClass || !$discipline) {
                    $failures[] = "Não encontrou Turma ($classCode) ou Disciplina ($sigla)";
                    continue;
                }

                // Extract teachers
                $teachersInRow = [];
                preg_match_all('/([^(),]+)\s*\((\d+)\)/', $professoresRaw, $matches, PREG_SET_ORDER);

                if (empty($matches)) {
                    $parts = explode(',', $professoresRaw);
                    foreach ($parts as $part) {
                        $name = trim($part);
                        if (!empty($name)) {
                            $teachersInRow[] = ['name' => $name, 'registration' => null];
                        }
                    }
                } else {
                    foreach ($matches as $match) {
                        $name = trim($match[1]);
                        $name = trim($name, " \t\n\r\0\x0B\"");
                        if (str_starts_with(strtolower($name), 'e ')) {
                            $name = substr($name, 2);
                        }
                        $teachersInRow[] = ['name' => $name, 'registration' => trim($match[2])];
                    }
                }

                foreach ($teachersInRow as $tData) {
                    $query = Teacher::query();
                    if ($tData['registration']) {
                        $query->where('registration_number', $tData['registration']);
                    } else {
                        $query->where('name', $tData['name']);
                    }
                    $teacher = $query->first();

                    if ($teacher) {
                        DB::table('course_class_disciplines')->updateOrInsert(
                            [
                                'course_class_id' => $courseClass->id,
                                'discipline_id' => $discipline->id,
                                'teacher_id' => $teacher->id,
                            ],
                            [
                                'updated_at' => now(),
                                'created_at' => now(),
                            ]
                        );
                    } else {
                        $failures[] = "Professor não encontrado no banco: " . $tData['name'];
                    }
                }
            }

            fclose($file);

            if (!empty($failures)) {
                $this->command->error("\n--- RELATÓRIO DE FALHAS ---");
                foreach (array_unique($failures) as $fail) {
                    $this->command->warn("- {$fail}");
                }
            }
        });
    }
}
