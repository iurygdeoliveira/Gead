<?php

namespace Database\Seeders;

use App\Models\Course;
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
        $directory = database_path('seeders/dados de seed/disciplinas dos professores');

        if (! is_dir($directory)) {
            $this->command->error("Diretório não encontrado: {$directory}");

            return;
        }

        $csvFiles = glob($directory.'/*.csv');

        if (empty($csvFiles)) {
            $this->command->warn("Nenhum arquivo CSV encontrado em: {$directory}");

            return;
        }

        $allCourses = Course::all();
        $allSystemDisciplines = Discipline::all();

        $failures = [];

        foreach ($csvFiles as $csvPath) {
            $fileBasename = basename($csvPath);
            $this->command->info('Processando arquivo: '.$fileBasename);
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

                if (empty($disciplineName) || empty($teachingPeriod) || str_starts_with(strtolower(trim($disciplineName)), 'ud:')) {
                    continue;
                }

                // Filtrar apenas disciplinas ministradas em 2025/2 e 2026/1
                if ($teachingPeriod !== '2025/2' && $teachingPeriod !== '2026/1') {
                    continue;
                }

                if (empty($registrationNumber) && empty($professorName)) {
                    continue;
                }

                // 1. Find the Teacher
                $teacher = null;
                if (! empty($registrationNumber)) {
                    $teacher = Teacher::where('registration_number', $registrationNumber)->first();
                }
                if (! $teacher && ! empty($professorName)) {
                    $teacher = Teacher::whereRaw('LOWER(name) = ?', [strtolower($professorName)])->first();
                }

                if (! $teacher) {
                    $failures[] = [
                        'file' => $fileBasename,
                        'reason' => "Professor não encontrado: '{$professorName}' (Matrícula: {$registrationNumber})",
                    ];

                    continue;
                }

                // 2. Find Course using normalized name comparison
                $course = null;
                if (! empty($courseRawName)) {
                    $cleanCourseName = trim(explode('(', $courseRawName)[0]);
                    $normalizedCleanCourseName = $this->normalizeString($cleanCourseName);

                    $course = $allCourses->first(function ($c) use ($normalizedCleanCourseName) {
                        $dbNormalized = $this->normalizeString($c->name);

                        return $dbNormalized === $normalizedCleanCourseName ||
                               str_contains($dbNormalized, $normalizedCleanCourseName) ||
                               str_contains($normalizedCleanCourseName, $dbNormalized);
                    });
                }

                if (! $course) {
                    $failures[] = [
                        'file' => $fileBasename,
                        'reason' => "Curso não encontrado no banco: '{$courseRawName}'",
                    ];

                    continue;
                }

                // 3. Find the Discipline(s) matching the normalized name
                $cleanDisciplineName = preg_replace('/^Att\d+\s*-\s*/i', '', $disciplineName);
                $normalizedCleanDisciplineName = $this->normalizeString($cleanDisciplineName);

                $disciplines = $allSystemDisciplines->filter(function ($d) use ($course, $normalizedCleanDisciplineName) {
                    return $d->course_id === $course->id && $this->normalizeString($d->name) === $normalizedCleanDisciplineName;
                });

                if ($disciplines->isEmpty()) {
                    $failures[] = [
                        'file' => $fileBasename,
                        'reason' => "Disciplina não encontrada no banco: '{$disciplineName}'".($course ? " para o curso '{$course->name}'" : ''),
                    ];

                    continue;
                }

                // 4. Match each discipline with the corresponding CourseClass cohort
                foreach ($disciplines as $discipline) {
                    $disciplinePeriod = $discipline->period;

                    $isAnnual = $course ? str_contains(mb_strtolower($course->name, 'UTF-8'), 'integrado') : false;

                    // Excluir Unidades Diversificadas / Disciplinas com período não numérico ou que começam com "UD:"
                    if (empty($disciplinePeriod) || $disciplinePeriod === '-' || ! is_numeric($disciplinePeriod) || str_starts_with(strtolower(trim($discipline->name)), 'ud:')) {
                        // Unidades Diversificadas serão ignoradas do seed conforme solicitado
                        continue;
                    }

                    // Se for numérico, calcula o semestre específico de ingresso
                    $calculatedEntryPeriod = $this->calculateEntryPeriod($teachingPeriod, (int) $disciplinePeriod, $isAnnual);

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
                                'teacher_id' => $teacher->id,
                            ],
                            [
                                'updated_at' => now(),
                                'created_at' => now(),
                            ]
                        );
                        $this->command->info("Vinculado: {$teacher->name} -> {$discipline->name} na turma {$courseClass->name}");
                    } else {
                        $failures[] = [
                            'file' => $fileBasename,
                            'reason' => "Turma não encontrada para o curso ID {$discipline->course_id} e período de entrada {$calculatedEntryPeriod} (Disciplina: {$disciplineName})",
                        ];
                    }
                }
            }
            fclose($file);
        }

        if (! empty($failures)) {
            $this->command->error("\n--- RELATÓRIO DE DISCIPLINAS NÃO VINCULADAS ---");
            foreach ($failures as $fail) {
                $this->command->warn("- [{$fail['file']}] {$fail['reason']}");
            }
        }
    }

    /**
     * Normalize string for comparison by removing accents, spaces, and punctuation.
     */
    private function normalizeString(string $str): string
    {
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/^att\d+\s*-\s*/i', '', $str);

        $str = str_replace(
            ['á', 'à', 'â', 'ã', 'ä', 'é', 'è', 'ê', 'ë', 'í', 'ì', 'î', 'ï', 'ó', 'ò', 'ô', 'õ', 'ö', 'ú', 'ù', 'û', 'ü', 'ç', 'ñ'],
            ['a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'c', 'n'],
            $str
        );

        return preg_replace('/[^a-z0-9]/', '', $str);
    }

    /**
     * Calculate entry period based on teaching period and curricular period.
     */
    private function calculateEntryPeriod(string $teachingPeriod, int $disciplinePeriod, bool $isAnnual): string
    {
        $normalized = str_replace('/', '.', $teachingPeriod);
        $parts = explode('.', $normalized);
        $year = (int) $parts[0];
        $sem = (int) ($parts[1] ?? 1);

        if ($isAnnual) {
            // For annual courses, entry is always in the 1st semester of the entry year
            $entryYear = $year - $disciplinePeriod + 1;

            return "{$entryYear}.1";
        }

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

    /**
     * Helper to get active entry periods for variable/optional disciplines.
     */
    private function getActiveEntryPeriods(string $teachingPeriod, bool $isAnnual): array
    {
        $normalized = str_replace('/', '.', $teachingPeriod);
        $parts = explode('.', $normalized);
        $year = (int) $parts[0];
        $sem = (int) ($parts[1] ?? 1);

        $active = [];

        if ($isAnnual) {
            for ($grade = 1; $grade <= 4; $grade++) {
                $entryYear = $year - $grade + 1;
                $active[] = "{$entryYear}.1";
            }
        } else {
            for ($grade = 1; $grade <= 10; $grade++) {
                $y = $year;
                $s = $sem;
                $semestersToSubtract = $grade - 1;
                for ($i = 0; $i < $semestersToSubtract; $i++) {
                    if ($s === 1) {
                        $y--;
                        $s = 2;
                    } else {
                        $s = 1;
                    }
                }
                $active[] = "{$y}.{$s}";
            }
        }

        return $active;
    }
}
