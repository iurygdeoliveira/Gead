<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseClassDiscipline;
use App\Models\Enrollment;
use App\Models\Evaluation;
use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class CourseEvaluationsWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.course-evaluations-widget';

    public ?string $period = null;

    public ?int $courseId = null;

    #[On('period-updated')]
    public function updatePeriod($period): void
    {
        $this->period = $period;
    }

    public static function canView(): bool
    {
        return filament()->getCurrentPanel()?->getId() === 'manager';
    }

    public function getData(): array
    {
        $teamId = filament()->getTenant()?->id;

        if (! $teamId || ! $this->courseId) {
            return [];
        }

        $course = Course::where('id', $this->courseId)->where('team_id', $teamId)->first();
        if (! $course) {
            return [];
        }

        $courseClasses = CourseClass::where('course_id', $this->courseId)->where('team_id', $teamId)->get();
        $classIds = $courseClasses->pluck('id')->toArray();

        // Count all students enrolled in this course, grouped by entry_period
        // This gives us the potential demand even before ClassEnrollments are generated
        $enrollmentCountsByPeriod = Enrollment::where('course_id', $this->courseId)
            ->selectRaw('entry_period, count(*) as total')
            ->groupBy('entry_period')
            ->pluck('total', 'entry_period');

        $courseClassDisciplines = CourseClassDiscipline::with('discipline')
            ->whereIn('course_class_id', $classIds)
            ->get()
            ->groupBy('course_class_id');

        $evaluations = Evaluation::where('team_id', $teamId)
            ->whereHas('courseClassDiscipline.courseClass', function ($query) {
                $query->where('course_id', $this->courseId);
            })
            ->get();

        $realizadas = $evaluations->whereNotNull('planning_score')->count();
        $totalPotential = 0;

        foreach ($courseClasses as $courseClass) {
            $isAnnual = str_contains(mb_strtolower($course->name, 'UTF-8'), 'integrado');
            $entryPeriod = $courseClass->entry_period;

            $studentsCount = $enrollmentCountsByPeriod[$entryPeriod] ?? 0;

            $ccds = $courseClassDisciplines[$courseClass->id] ?? collect();
            foreach ($ccds as $ccd) {
                $discipline = $ccd->discipline;
                if (! $discipline || empty($discipline->period) || ! is_numeric($discipline->period)) {
                    continue;
                }

                $teachingPeriod = $this->calculateTeachingPeriod($entryPeriod, (int) $discipline->period, $isAnnual);

                if ($this->period && $teachingPeriod !== $this->period) {
                    continue;
                }

                // Add to potential demand for this period
                $totalPotential += $studentsCount;
            }
        }

        // Se totalPotential for menor que realizadas (caso de dados sujos), fixamos no max()
        $total = max($totalPotential, $realizadas, $evaluations->count());
        $naoRealizadas = max(0, $total - $realizadas);

        $divider = $total > 0 ? $total : 1;

        return [
            'name' => $course->name,
            'realizadas' => $realizadas,
            'realizadas_pct' => ($realizadas / $divider) * 100,
            'nao_realizadas' => $naoRealizadas,
            'nao_realizadas_pct' => ($naoRealizadas / $divider) * 100,
            'total' => $total,
        ];
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
