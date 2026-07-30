<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Students\Widgets\StudentsStats;
use App\Models\Evaluation;
use App\Models\Student;
use App\Traits\Filament\HasFeedbackAction;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\HtmlString;
use Filament\Support\Icons\Heroicon;

class ListStudents extends ListRecords
{
    use HasFeedbackAction;

    protected static string $resource = StudentResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('bulk_dispense_evaluations')
                ->label('Dispensar Inativos')
                ->icon(Heroicon::OutlinedArchiveBoxXMark)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Dispensar alunos que não iniciaram avaliações')
                ->modalDescription(function (): HtmlString {
                    $teamId = Filament::getTenant()?->getKey();

                    $evalQuery = Evaluation::whereNotNull('planning_score');
                    if ($teamId) {
                        $evalQuery->where('evaluations.team_id', $teamId);
                    }

                    $studentsWithEvaluationsIds = $evalQuery
                        ->join('class_enrollments', 'evaluations.class_enrollment_id', '=', 'class_enrollments.id')
                        ->join('enrollments', 'class_enrollments.enrollment_id', '=', 'enrollments.id')
                        ->select('enrollments.student_id')
                        ->pluck('student_id')
                        ->unique()
                        ->toArray();

                    $studentsQuery = Student::where('is_dispensed_from_evaluations', false)
                        ->whereNotIn('id', $studentsWithEvaluationsIds)
                        ->with('enrollments.course');

                    if ($teamId) {
                        $studentsQuery->where('team_id', $teamId);
                    }

                    $studentsToDispense = $studentsQuery->get();

                    $breakdown = [];
                    foreach ($studentsToDispense as $student) {
                        foreach ($student->enrollments as $enrollment) {
                            $courseName = $enrollment->course->name ?? 'Sem Curso';
                            $breakdown[$courseName] = ($breakdown[$courseName] ?? 0) + 1;
                        }
                    }

                    if ($breakdown === []) {
                        return new HtmlString('<p>Não há alunos elegíveis para dispensa no momento.</p>');
                    }

                    $html = '<p>Os seguintes alunos serão dispensados, divididos por curso:</p><ul>';
                    foreach ($breakdown as $course => $count) {
                        $html .= "<li><strong>{$course}</strong>: {$count} aluno(s)</li>";
                    }
                    $html .= '</ul><br><p><strong>Atenção:</strong> Estes alunos não iniciaram nenhuma avaliação. Deseja continuar?</p>';

                    return new HtmlString($html);
                })
                ->action(function (): void {
                    $teamId = Filament::getTenant()?->getKey();

                    $evalQuery = Evaluation::whereNotNull('planning_score');
                    if ($teamId) {
                        $evalQuery->where('evaluations.team_id', $teamId);
                    }

                    $studentsWithEvaluationsIds = $evalQuery
                        ->join('class_enrollments', 'evaluations.class_enrollment_id', '=', 'class_enrollments.id')
                        ->join('enrollments', 'class_enrollments.enrollment_id', '=', 'enrollments.id')
                        ->select('enrollments.student_id')
                        ->pluck('student_id')
                        ->unique()
                        ->toArray();

                    $studentsQuery = Student::where('is_dispensed_from_evaluations', false)
                        ->whereNotIn('id', $studentsWithEvaluationsIds);

                    if ($teamId) {
                        $studentsQuery->where('team_id', $teamId);
                    }

                    $count = $studentsQuery->update(['is_dispensed_from_evaluations' => true]);

                    Notification::make()
                        ->title("{$count} alunos dispensados com sucesso!")
                        ->success()
                        ->send();
                }),
        ];
    }

    #[\Override]
    protected function getHeaderWidgets(): array
    {
        return [
            StudentsStats::class,
        ];
    }

    #[\Override]
    public function getTabs(): array
    {
        $teamId = Filament::getTenant()?->getKey();

        $baseQuery = Student::query()
            ->when($teamId, fn ($q) => $q->where('team_id', $teamId));

        return [
            'sem_turma' => Tab::make('Sem Turma')
                ->modifyQueryUsing(fn ($query) => $query->whereDoesntHave('enrollments.classEnrollments'))
                ->badge((clone $baseQuery)->whereDoesntHave('enrollments.classEnrollments')->count())
                ->badgeColor('danger'),
            'dispensados' => Tab::make('Dispensados')
                ->modifyQueryUsing(fn ($query) => $query->where('is_dispensed_from_evaluations', true))
                ->badge((clone $baseQuery)->where('is_dispensed_from_evaluations', true)->count())
                ->badgeColor('warning'),
            'farmacia' => Tab::make('Farm.')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where('name', 'like', '%Farmácia%')))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn (Builder $q) => $q->where('name', 'like', '%Farmácia%'))->count()),
            'enfermagem' => Tab::make('Enfer.')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where('name', 'like', '%Enfermagem%')))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn (Builder $q) => $q->where('name', 'like', '%Enfermagem%'))->count()),
            'informatica' => Tab::make('Infor.')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%Informática%')->orWhere('name', 'like', '%Informatica%'))))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->where('name', 'like', '%Informática%')->orWhere('name', 'like', '%Informatica%')))->count()),
            'gpi' => Tab::make('GPI')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%GPI%')->orWhere('name', 'like', '%Gestão da Produção Industrial%'))))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->where('name', 'like', '%GPI%')->orWhere('name', 'like', '%Gestão da Produção Industrial%')))->count()),
            'tads' => Tab::make('TADS')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%TADS%')->orWhere('name', 'like', '%Análise e Desenvolvimento de Sistema%'))))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->where('name', 'like', '%TADS%')->orWhere('name', 'like', '%Análise e Desenvolvimento de Sistema%')))->count()),
            'pcp' => Tab::make('PCP')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%PCP%')->orWhere('name', 'like', '%Planejamento e controle da produção%')->orWhere('name', 'like', '%Planejamento e Controle da Produção%'))))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->where('name', 'like', '%PCP%')->orWhere('name', 'like', '%Planejamento e controle da produção%')->orWhere('name', 'like', '%Planejamento e Controle da Produção%')))->count()),
            'eja' => Tab::make('EJA')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where('name', 'like', '%Operador de Computador%')))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn (Builder $q) => $q->where('name', 'like', '%Operador de Computador%'))->count()),
            'analises_clinicas' => Tab::make('Análises')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%Análises Clínicas%')->orWhere('name', 'like', '%Analises Clinicas%'))))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->where('name', 'like', '%Análises Clínicas%')->orWhere('name', 'like', '%Analises Clinicas%')))->count()),

        ];
    }
}
