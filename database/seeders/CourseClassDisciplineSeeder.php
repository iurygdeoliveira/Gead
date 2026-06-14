<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Discipline;
use App\Models\CourseClass;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class CourseClassDisciplineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $directory = database_path('seeders/dados de seed/disciplinas dos professores');

        if (!is_dir($directory)) {
            $this->command->error("Diretório não encontrado: {$directory}");
            return;
        }

        $csvFiles = glob($directory . '/*.csv');

        if (empty($csvFiles)) {
            $this->command->warn("Nenhum arquivo CSV encontrado em: {$directory}");
            return;
        }

        foreach ($csvFiles as $csvPath) {
            $this->command->info("Processando arquivo: " . basename($csvPath));
            $file = fopen($csvPath, 'r');
            $isHeader = true;

            while (($row = fgetcsv($file, 1000, ',')) !== false) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                // Columns: Professor,Matrícula,CPF,Curso,Disciplina,Período
                $professorName = trim($row[0] ?? '');
                $registrationNumber = trim($row[1] ?? '');
                $cpf = trim($row[2] ?? '');
                $courseRawName = trim($row[3] ?? '');
                $disciplineName = trim($row[4] ?? '');
                $teachingPeriod = trim($row[5] ?? '');

                if (empty($registrationNumber) && empty($professorName)) {
                    continue;
                }

                if (empty($disciplineName) || empty($teachingPeriod)) {
                    continue;
                }

                // 1. Find the Teacher
                $teacher = null;
                if (!empty($registrationNumber)) {
                    $teacher = Teacher::where('registration_number', $registrationNumber)->first();
                }
                if (!$teacher && !empty($professorName)) {
                    $teacher = Teacher::where('name', 'ilike', $professorName)->first();
                }

                if (!$teacher) {
                    $this->command->warn("Professor não encontrado: {$professorName} (Matrícula: {$registrationNumber})");
                    continue;
                }

                // 2. Find Course if course name is provided in CSV
                $course = null;
                if (!empty($courseRawName)) {
                    $cleanCourseName = trim(explode('(', $courseRawName)[0]);
                    $course = Course::where('name', 'ilike', $cleanCourseName)->first();
                }

                // 3. Find the Discipline(s) matching the name (and course if available)
                $cleanDisciplineName = preg_replace('/^Att\d+\s*-\s*/i', '', $disciplineName);
                $query = Discipline::where('name', 'ilike', $cleanDisciplineName);
                if ($course) {
                    $query->where('course_id', $course->id);
                }
                $disciplines = $query->get();

                if ($disciplines->isEmpty()) {
                    $this->command->warn("Disciplina não encontrada no banco: '{$disciplineName}'" . ($course ? " para o curso '{$course->name}'" : ''));
                    continue;
                }

                // 4. Match each discipline with the corresponding CourseClass cohort
                foreach ($disciplines as $discipline) {
                    $disciplinePeriod = $discipline->period;
                    
                    if (empty($disciplinePeriod) || $disciplinePeriod === '-') {
                        $this->command->warn("Disciplina '{$disciplineName}' não possui um período curricular válido definido.");
                        continue;
                    }

                    // Only handle numeric discipline periods for calculating cohort entry_period
                    if (is_numeric($disciplinePeriod)) {
                        $calculatedEntryPeriod = $this->calculateEntryPeriod($teachingPeriod, (int)$disciplinePeriod);

                        // Find the CourseClass for this course and calculated entry period
                        $courseClass = CourseClass::where('course_id', $discipline->course_id)
                            ->where('entry_period', $calculatedEntryPeriod)
                            ->first();

                        if ($courseClass) {
                            // Link/Update the teacher relationship in pivot table
                            DB::table('course_class_disciplines')->updateOrInsert(
                                [
                                    'course_class_id' => $courseClass->id,
                                    'discipline_id' => $discipline->id,
                                ],
                                [
                                    'teacher_id' => $teacher->id,
                                    'updated_at' => now(),
                                    'created_at' => now(), // only used if inserting
                                ]
                            );
                            $this->command->info("Vinculado: {$teacher->name} -> {$discipline->name} na turma {$courseClass->name}");
                        } else {
                            $this->command->warn("Turma não encontrada para o curso ID {$discipline->course_id} e período de entrada {$calculatedEntryPeriod}");
                        }
                    } else {
                        $this->command->warn("Período curricular '{$disciplinePeriod}' da disciplina '{$disciplineName}' não é numérico.");
                    }
                }
            }
            fclose($file);
        }
    }

    /**
     * Calculate entry period based on teaching period and curricular period.
     */
    private function calculateEntryPeriod(string $teachingPeriod, int $disciplinePeriod): string
    {
        $normalized = str_replace('/', '.', $teachingPeriod);
        $parts = explode('.', $normalized);
        $year = (int)$parts[0];
        $sem = (int)($parts[1] ?? 1);

        $semestersToSubtract = $disciplinePeriod - 1;
        for ($i = 0; $i < $semestersToSubtract; $i++) {
            if ($sem === 1) {
                $year--;
                $sem = 2;
            } else {
                $sem = 1;
            }
        }

        return "{$year}.{$sem}";
    }
}
