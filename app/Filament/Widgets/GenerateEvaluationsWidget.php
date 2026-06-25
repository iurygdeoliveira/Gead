<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\ClassEnrollment;
use App\Models\CourseClassDiscipline;
use App\Models\Evaluation;
use App\Models\Student;
use App\Models\Team;
use App\Filament\Resources\Students\StudentResource;
use App\Traits\Filament\NotificationsTrait;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class GenerateEvaluationsWidget extends BaseWidget
{
    use NotificationsTrait;

    protected ?string $pollingInterval = null;

    #[\Override]
    public static function canView(): bool
    {
        return in_array(filament()->getCurrentPanel()?->getId(), ['manager', 'tae', 'admin']);
    }

    public function generate(): void
    {
        $teamId = filament()->getTenant()?->getKey();
        if (! $teamId) {
            $team = Team::first();
            $teamId = $team ? $team->id : null;
        }

        if (! $teamId) {
            $this->notifyDanger('Erro', 'Nenhum campus (team) ativo encontrado.');

            return;
        }

        // Fetch all class enrollments for the campus
        $classEnrollments = ClassEnrollment::whereHas('courseClass', function (Builder $query) use ($teamId): void {
            $query->where('team_id', $teamId);
        })
            ->whereHas('enrollment.student', function ($query): void {
                $query->whereDoesntHave('enrollments', function ($q): void {
                    $q->whereDoesntHave('classEnrollments');
                });
            })
            ->get();

        // Fetch all course class disciplines
        $courseClassDisciplines = CourseClassDiscipline::all()->groupBy('course_class_id');

        // Fetch existing evaluations to prevent duplicates
        $existingEvaluations = Evaluation::where('team_id', $teamId)->get()->mapWithKeys(fn ($item): array => ["{$item->class_enrollment_id}-{$item->course_class_discipline_id}" => true])->toArray();

        $evaluationsToInsert = [];
        $generatedCount = 0;

        foreach ($classEnrollments as $classEnrollment) {
            $ccds = $courseClassDisciplines[$classEnrollment->course_class_id] ?? collect();

            foreach ($ccds as $ccd) {
                $key = "{$classEnrollment->id}-{$ccd->id}";

                if (! isset($existingEvaluations[$key])) {
                    $evaluationsToInsert[] = [
                        'uuid' => Str::uuid()->toString(),
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

        if ($evaluationsToInsert !== []) {
            foreach (array_chunk($evaluationsToInsert, 500) as $chunk) {
                Evaluation::insertOrIgnore($chunk);
            }
        }

        $this->notifySuccess('Sucesso!', "Foram geradas {$generatedCount} novas fichas de avaliação baseadas nos diários.");
    }

    #[\Override]
    protected function getStats(): array
    {
        $teamId = filament()->getTenant()?->getKey();
        if (! $teamId) {
            $team = Team::first();
            $teamId = $team ? $team->id : null;
        }

        $dispensadosCount = 0;
        $semTurmaCount = 0;

        if ($teamId) {
            $dispensadosCount = Student::where('team_id', $teamId)
                ->where('is_dispensed_from_evaluations', true)
                ->count();

            $semTurmaCount = Student::where('team_id', $teamId)
                ->whereDoesntHave('enrollments.classEnrollments')
                ->count();
        }

        return [
            Stat::make('Ação', 'GERAR AVALIAÇÕES')
                ->description(new HtmlString(Blade::render('
                    <span style="display: inline-flex; align-items: center;">
                        <span wire:loading.remove wire:target="generate">
                            <span style="display: inline-flex; align-items: center; gap: 0.375rem;">
                                <x-filament::icon icon="heroicon-m-play" class="h-4 w-4" style="display: inline-block;" />
                                <span>Clique em qualquer lugar deste card</span>
                            </span>
                        </span>
                        <span wire:loading wire:target="generate">
                            <span style="display: inline-flex; align-items: center; gap: 0.375rem;">
                                <x-filament::loading-indicator class="h-4 w-4" style="display: inline-block;" />
                                <span>Gerando avaliações, aguarde...</span>
                            </span>
                        </span>
                    </span>
                ')))
                ->color('primary')
                ->extraAttributes([
                    'wire:click' => 'generate',
                    'wire:loading.class' => 'opacity-70 cursor-wait pointer-events-none',
                    'wire:target' => 'generate',
                    'style' => 'cursor: pointer !important;',
                    'class' => 'cursor-pointer transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg ring-2 ring-primary-500 bg-primary-50 dark:bg-primary-500/10 hover:bg-primary-100 dark:hover:bg-primary-500/20',
                    'title' => 'Clique para iniciar o processamento de avaliações',
                ]),

            Stat::make('Alunos Dispensados da Avaliação', number_format($dispensadosCount, 0, ',', '.'))
                ->description('Ver lista de dispensados')
                ->descriptionIcon(Heroicon::ExclamationCircle)
                ->color('warning')
                ->url(StudentResource::getUrl('index', ['activeTab' => 'dispensados'])),

            Stat::make('Alunos sem Turma Vinculada', number_format($semTurmaCount, 0, ',', '.'))
                ->description('Ver lista sem enturmação')
                ->descriptionIcon(Heroicon::ExclamationTriangle)
                ->color('danger')
                ->url(StudentResource::getUrl('index', ['activeTab' => 'sem_turma'])),
        ];
    }
}
