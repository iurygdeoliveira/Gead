<?php

namespace App\Filament\Resources\CourseClasses\Widgets;

use App\Models\CourseClass;
use App\Models\CourseClassDiscipline;
use App\Models\ClassEnrollment;
use App\Models\Evaluation;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CourseClassesStats extends BaseWidget
{
    protected function getStats(): array
    {
        $currentTeam = Filament::getTenant();
        $teamId = $currentTeam ? $currentTeam->getKey() : null;

        $classesQuery = CourseClass::query();
        if ($teamId) {
            $classesQuery->where('team_id', $teamId);
        }
        
        $classes = $classesQuery->get();
        $totalTurmas = $classes->count();

        if ($totalTurmas === 0) {
            return [
                Stat::make('Total de Turmas', 0)
                    ->description('Turmas cadastradas neste campus')
                    ->descriptionIcon('heroicon-m-academic-cap')
                    ->color('primary'),

                Stat::make('Turmas com Avaliações Completas', 0)
                    ->description('Avaliações Completas')
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success'),

                Stat::make('Turmas com Avaliações Incompletas', 0)
                    ->description('Avaliações Pendentes')
                    ->descriptionIcon('heroicon-m-x-circle')
                    ->color('danger'),
            ];
        }

        $classIds = $classes->pluck('id')->toArray();

        // Count of active enrollments per CourseClass
        $enrollmentCountsByClass = ClassEnrollment::whereIn('course_class_id', $classIds)
            ->whereHas('enrollment.student', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('user_id')
                        ->orWhereHas('user', function ($q) {
                            $q->where('is_suspended', false);
                        });
                });
                $query->whereDoesntHave('enrollments', function ($q) {
                    $q->whereDoesntHave('classEnrollments');
                });
            })
            ->selectRaw('course_class_id, count(*) as total')
            ->groupBy('course_class_id')
            ->pluck('total', 'course_class_id');

        // Count of disciplines per CourseClass
        $disciplineCountsByClass = CourseClassDiscipline::whereIn('course_class_id', $classIds)
            ->selectRaw('course_class_id, count(*) as total')
            ->groupBy('course_class_id')
            ->pluck('total', 'course_class_id');

        // Completed evaluations mapping back to course_class_id
        $ccds = CourseClassDiscipline::whereIn('course_class_id', $classIds)->get(['id', 'course_class_id']);
        $ccdIds = $ccds->pluck('id')->toArray();
        $ccdToClassMap = $ccds->pluck('course_class_id', 'id')->toArray();

        $evaluationsCompletedByClass = [];
        
        if (!empty($ccdIds)) {
            $evaluations = Evaluation::whereIn('course_class_discipline_id', $ccdIds)
                ->whereNotNull('planning_score')
                ->when($teamId, fn($q) => $q->where('team_id', $teamId))
                ->whereHas('classEnrollment.enrollment.student', function ($query) {
                    $query->where(function ($subQuery) {
                        $subQuery->whereNull('user_id')
                            ->orWhereHas('user', function ($q) {
                                $q->where('is_suspended', false);
                            });
                    });
                    $query->whereDoesntHave('enrollments', function ($q) {
                        $q->whereDoesntHave('classEnrollments');
                    });
                })
                ->get(['id', 'course_class_discipline_id']);
                
            foreach ($evaluations as $eval) {
                $classId = $ccdToClassMap[$eval->course_class_discipline_id] ?? null;
                if ($classId) {
                    $evaluationsCompletedByClass[$classId] = ($evaluationsCompletedByClass[$classId] ?? 0) + 1;
                }
            }
        }

        $completas = 0;
        $incompletas = 0;

        foreach ($classes as $courseClass) {
            $classId = $courseClass->id;
            $studentsCount = $enrollmentCountsByClass[$classId] ?? 0;
            $disciplinesCount = $disciplineCountsByClass[$classId] ?? 0;
            
            $expected = $studentsCount * $disciplinesCount;

            if ($expected > 0) {
                $completed = $evaluationsCompletedByClass[$classId] ?? 0;
                if ($completed >= $expected) {
                    $completas++;
                } else {
                    $incompletas++;
                }
            }
        }

        return [
            Stat::make('Total de Turmas', $totalTurmas)
                ->description('Turmas cadastradas neste campus')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make('Turmas com Avaliações Completas', $completas)
                ->description('Avaliações Completas')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Turmas com Avaliações Incompletas', $incompletas)
                ->description('Avaliações Pendentes')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
