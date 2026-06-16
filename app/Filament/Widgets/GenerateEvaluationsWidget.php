<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\ClassEnrollment;
use App\Models\CourseClass;
use App\Models\CourseClassDiscipline;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\Team;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class GenerateEvaluationsWidget extends Widget
{
    protected string $view = 'filament.widgets.generate-evaluations-widget';

    public ?string $period = null;

    #[On('period-updated')]
    public function updatePeriod($period): void
    {
        $this->period = $period;
    }

    public static function canView(): bool
    {
        return filament()->getCurrentPanel()?->getId() === 'manager';
    }

    public function generate(): void
    {
        if (empty($this->period)) {
            Notification::make()
                ->title('Atenção')
                ->body('Selecione um período letivo para gerar as avaliações.')
                ->warning()
                ->send();

            return;
        }

        $tenant = filament()->getTenant();
        $teamId = $tenant ? $tenant->id : null;

        if (! $teamId) {
            $team = Team::first();
            $teamId = $team ? $team->id : null;
        }

        if (! $teamId) {
            Notification::make()
                ->title('Erro')
                ->body('Nenhum campus (team) ativo encontrado.')
                ->danger()
                ->send();

            return;
        }

        // 1. Garantir que as matrículas em turmas (ClassEnrollments) existam para este campus (Otimizado com Bulk Operations)
        $courseClasses = CourseClass::where('team_id', $teamId)->get();
        $classesGrouped = [];
        foreach ($courseClasses as $cc) {
            $classesGrouped[$cc->course_id][$cc->entry_period] = $cc->id;
        }

        $enrollments = Enrollment::whereHas('course', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->get();

        $existingClassEnrollments = ClassEnrollment::all()->mapWithKeys(function ($item) {
            return ["{$item->enrollment_id}-{$item->course_class_id}" => true];
        })->toArray();

        $classEnrollmentsToInsert = [];
        foreach ($enrollments as $enrollment) {
            $classId = $classesGrouped[$enrollment->course_id][$enrollment->entry_period] ?? null;
            if ($classId) {
                $key = "{$enrollment->id}-{$classId}";
                if (! isset($existingClassEnrollments[$key])) {
                    $classEnrollmentsToInsert[] = [
                        'enrollment_id' => $enrollment->id,
                        'course_class_id' => $classId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $existingClassEnrollments[$key] = true;
                }
            }
        }

        if (! empty($classEnrollmentsToInsert)) {
            ClassEnrollment::insertOrIgnore($classEnrollmentsToInsert);
        }

        // 2. Gerar as fichas de avaliação em branco (sem notas, para serem preenchidas)
        $classEnrollments = ClassEnrollment::with(['courseClass.course'])->whereHas('courseClass', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->get();

        $courseClassDisciplines = CourseClassDiscipline::with('discipline')->get()->groupBy('course_class_id');

        $existingEvaluations = Evaluation::where('team_id', $teamId)->get()->mapWithKeys(function ($item) {
            return ["{$item->class_enrollment_id}-{$item->course_class_discipline_id}" => true];
        })->toArray();

        $evaluationsToInsert = [];
        $generatedCount = 0;

        foreach ($classEnrollments as $classEnrollment) {
            $courseClass = $classEnrollment->courseClass;
            $isAnnual = $courseClass && $courseClass->course ? str_contains(mb_strtolower($courseClass->course->name, 'UTF-8'), 'integrado') : false;
            $entryPeriod = $courseClass->entry_period;

            $ccds = $courseClassDisciplines[$classEnrollment->course_class_id] ?? collect();
            foreach ($ccds as $ccd) {
                $discipline = $ccd->discipline;
                if (! $discipline || empty($discipline->period) || ! is_numeric($discipline->period)) {
                    continue;
                }

                $teachingPeriod = $this->calculateTeachingPeriod($entryPeriod, (int) $discipline->period, $isAnnual);

                if ($teachingPeriod !== $this->period) {
                    continue;
                }

                $key = "{$classEnrollment->id}-{$ccd->id}";
                if (! isset($existingEvaluations[$key])) {
                    $evaluationsToInsert[] = [
                        'class_enrollment_id' => $classEnrollment->id,
                        'course_class_discipline_id' => $ccd->id,
                        'team_id' => $teamId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $existingEvaluations[$key] = true;
                    $generatedCount++;
                }
            }
        }

        if (! empty($evaluationsToInsert)) {
            foreach (array_chunk($evaluationsToInsert, 500) as $chunk) {
                Evaluation::insertOrIgnore($chunk);
            }
        }

        Notification::make()
            ->title('Sucesso!')
            ->body("Processadas as matrículas e geradas {$generatedCount} novas fichas de avaliação para o período {$this->period}.")
            ->success()
            ->send();
    }

    private function calculateTeachingPeriod(string $entryPeriod, int $disciplinePeriod, bool $isAnnual): string
    {
        $normalized = str_replace('/', '.', $entryPeriod);
        $parts = explode('.', $normalized);
        $year = (int) $parts[0];
        $sem = (int) ($parts[1] ?? 1);

        if ($isAnnual) {
            $teachingYear = $year + $disciplinePeriod - 1;

            return "{$teachingYear}.1";
        }

        $semestersToAdd = $disciplinePeriod - 1;
        for ($i = 0; $i < $semestersToAdd; $i++) {
            if ($sem === 2) {
                $year++;
                $sem = 1;
            } else {
                $sem = 2;
            }
        }

        return "{$year}.{$sem}";
    }
}
