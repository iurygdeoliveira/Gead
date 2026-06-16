<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\CourseClass;
use App\Models\CourseClassDiscipline;
use App\Models\Enrollment;
use App\Models\Evaluation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class EvaluationsOverviewWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

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

    #[\Override]
    protected function getStats(): array
    {
        $teamId = filament()->getTenant()?->id;

        if (! $teamId) {
            return [
                Stat::make('Avaliações Realizadas', '0%')->description('0 / 0 esperadas')->color('primary'),
                Stat::make('Avaliações Não Realizadas', '0%')->description('0 pendentes')->color('danger'),
            ];
        }

        $courseClasses = CourseClass::where('team_id', $teamId)->with('course')->get();
        $classIds = $courseClasses->pluck('id')->toArray();

        $enrollmentCounts = Enrollment::whereHas('course', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })
            ->selectRaw('course_id, entry_period, count(*) as total')
            ->groupBy('course_id', 'entry_period')
            ->get()
            ->mapWithKeys(function ($item) {
                return ["{$item->course_id}-{$item->entry_period}" => $item->total];
            })->toArray();

        $courseClassDisciplines = CourseClassDiscipline::with('discipline')
            ->whereIn('course_class_id', $classIds)
            ->get()
            ->groupBy('course_class_id');

        $evaluations = Evaluation::where('team_id', $teamId)->get();

        $realizadas = $evaluations->whereNotNull('planning_score')->count();
        $totalPotential = 0;

        foreach ($courseClasses as $courseClass) {
            $course = $courseClass->course;
            if (! $course) {
                continue;
            }

            $isAnnual = str_contains(mb_strtolower($course->name, 'UTF-8'), 'integrado');
            $entryPeriod = $courseClass->entry_period;

            $studentsCount = $enrollmentCounts["{$course->id}-{$entryPeriod}"] ?? 0;

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

                $totalPotential += $studentsCount;
            }
        }

        $total = max($totalPotential, $realizadas, $evaluations->count());
        $naoRealizadas = max(0, $total - $realizadas);

        $divider = $total > 0 ? $total : 1;
        $realizadasPct = ($realizadas / $divider) * 100;
        $naoRealizadasPct = ($naoRealizadas / $divider) * 100;

        return [
            Stat::make('Total Esperado', number_format($total, 0, ',', '.'))
                ->description('Fichas a serem preenchidas')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),

            Stat::make('Avaliações Realizadas', number_format($realizadasPct, 1).'%')
                ->description($realizadas.' / '.$total.' esperadas')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),

            Stat::make('Avaliações Não Realizadas', number_format($naoRealizadasPct, 1).'%')
                ->description($naoRealizadas.' pendentes')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
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
