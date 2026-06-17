<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\ClassEnrollment;
use App\Models\CourseClassDiscipline;
use App\Models\Evaluation;
use App\Models\Team;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class GenerateEvaluationsWidget extends Widget
{
    protected string $view = 'filament.widgets.generate-evaluations-widget';

    public static function canView(): bool
    {
        return filament()->getCurrentPanel()?->getId() === 'manager';
    }

    public function generate(): void
    {
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

        // Fetch all class enrollments for the campus
        $classEnrollments = ClassEnrollment::whereHas('courseClass', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })
        ->whereHas('enrollment.student', function ($query) {
            $query->whereDoesntHave('enrollments', function ($q) {
                $q->whereDoesntHave('classEnrollments');
            });
        })
        ->get();

        // Fetch all course class disciplines
        $courseClassDisciplines = CourseClassDiscipline::all()->groupBy('course_class_id');

        // Fetch existing evaluations to prevent duplicates
        $existingEvaluations = Evaluation::where('team_id', $teamId)->get()->mapWithKeys(function ($item) {
            return ["{$item->class_enrollment_id}-{$item->course_class_discipline_id}" => true];
        })->toArray();

        $evaluationsToInsert = [];
        $generatedCount = 0;

        foreach ($classEnrollments as $classEnrollment) {
            $ccds = $courseClassDisciplines[$classEnrollment->course_class_id] ?? collect();
            
            foreach ($ccds as $ccd) {
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
            ->body("Foram geradas {$generatedCount} novas fichas de avaliação baseadas nos diários.")
            ->success()
            ->send();
    }
}
