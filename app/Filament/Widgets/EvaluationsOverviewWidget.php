<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\CourseClass;
use App\Models\CourseClassDiscipline;
use App\Models\ClassEnrollment;
use App\Models\Evaluation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EvaluationsOverviewWidget extends BaseWidget
{
    protected ?string $pollingInterval = null;

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

        $courseClasses = CourseClass::where('team_id', $teamId)->get();
        $classIds = $courseClasses->pluck('id')->toArray();

        $enrollmentCountsByClass = ClassEnrollment::whereIn('course_class_id', $classIds)
            ->selectRaw('course_class_id, count(*) as total')
            ->groupBy('course_class_id')
            ->pluck('total', 'course_class_id');

        $disciplineCountsByClass = CourseClassDiscipline::whereIn('course_class_id', $classIds)
            ->selectRaw('course_class_id, count(*) as total')
            ->groupBy('course_class_id')
            ->pluck('total', 'course_class_id');

        $totalPotential = 0;
        foreach ($classIds as $classId) {
            $totalPotential += ($enrollmentCountsByClass[$classId] ?? 0) * ($disciplineCountsByClass[$classId] ?? 0);
        }

        $evaluations = Evaluation::where('team_id', $teamId)->get();

        $realizadas = $evaluations->whereNotNull('planning_score')->count();

        $total = max($totalPotential, $realizadas, $evaluations->count());
        $naoRealizadas = max(0, $total - $realizadas);

        $divider = $total > 0 ? $total : 1;
        $realizadasPct = ($realizadas / $divider) * 100;
        $naoRealizadasPct = ($naoRealizadas / $divider) * 100;

        return [
            Stat::make('Total Esperado', number_format($total, 0, ',', '.'))
                ->description('Avaliações a serem realizadas')
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
}
