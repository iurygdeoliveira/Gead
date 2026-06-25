<?php

namespace App\Filament\Resources\Evaluations\Schemas;

use App\Models\CourseClassDiscipline;
use App\Models\Evaluation;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EvaluationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Detalhes da Avaliação')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Detalhes da Avaliação')
                            ->icon(Heroicon::OutlinedInformationCircle)
                            ->schema([
                                TextEntry::make('classEnrollment.courseClass.course.name')
                                    ->label('Turma (Curso)'),
                                TextEntry::make('courseClassDiscipline.teacher.name')
                                    ->label('Professor'),
                                TextEntry::make('courseClassDiscipline.discipline.name')
                                    ->label('Disciplina'),
                                TextEntry::make('courseClassDiscipline.courseClass.academicTerm.name')
                                    ->label('Período Letivo'),
                            ])
                            ->columns(2),
                        Tab::make('Avaliação Docente')
                            ->icon(Heroicon::OutlinedClipboardDocumentCheck)
                            ->schema([
                                RepeatableEntry::make('taught_disciplines_evaluations')
                                    ->hiddenLabel()
                                    ->getStateUsing(function ($record) {
                                        if (! isset($record->cached_taught_disciplines_evaluations)) {
                                            $teacherId = $record->courseClassDiscipline?->teacher_id;
                                            $academicTermId = $record->courseClassDiscipline?->courseClass?->academic_term_id;

                                            if (! $teacherId || ! $academicTermId) {
                                                $record->cached_taught_disciplines_evaluations = [];
                                            } else {
                                                $ccds = CourseClassDiscipline::where('teacher_id', $teacherId)
                                                    ->whereHas('courseClass', function ($query) use ($academicTermId): void {
                                                        $query->where('academic_term_id', $academicTermId);
                                                    })
                                                    ->with(['discipline', 'courseClass'])
                                                    ->get();

                                                $record->cached_taught_disciplines_evaluations = $ccds->map(function ($ccd): array {
                                                    $latestEvalIds = Evaluation::where('course_class_discipline_id', $ccd->id)
                                                        ->whereNotNull('planning_score')
                                                        ->groupBy('class_enrollment_id')
                                                        ->selectRaw('MAX(id) as id')
                                                        ->pluck('id');

                                                    $averages = Evaluation::whereIn('id', $latestEvalIds)
                                                        ->selectRaw('AVG(planning_score) as planning_score, AVG(posture_score) as posture_score, AVG(attendance_score) as attendance_score, AVG(punctuality_score) as punctuality_score, AVG(execution_score) as execution_score, AVG(assessment_score) as assessment_score')
                                                        ->first();

                                                    return [
                                                        'discipline_label' => ($ccd->discipline->name ?? '-').' ('.($ccd->courseClass->name ?? '-').')',
                                                        'dimensions' => [
                                                            [
                                                                'dimension' => 'O docente apresenta seu plano de ensino (PLANEJAMENTO) no início do semestre ou ano letivo, indicando a ementa, competências e habilidades, recursos didáticos que serão utilizados, formas de avaliações, referências bibliográficas?',
                                                                'media' => $averages->planning_score ? number_format((float) $averages->planning_score, 2) : '-',
                                                            ],
                                                            [
                                                                'dimension' => 'O docente apresenta uma POSTURA adequada ao cargo e responsabilidade que ocupa?',
                                                                'media' => $averages->posture_score ? number_format((float) $averages->posture_score, 2) : '-',
                                                            ],
                                                            [
                                                                'dimension' => 'O docente é ASSÍDUO, ou seja, não falta às aulas e quando falta, apresenta justificativa e promove suas devidas reposições ou anteposições?',
                                                                'media' => $averages->attendance_score ? number_format((float) $averages->attendance_score, 2) : '-',
                                                            ],
                                                            [
                                                                'dimension' => 'O docente é PONTUAL, ou seja, não chega atrasado ou libera a turma mais cedo?',
                                                                'media' => $averages->punctuality_score ? number_format((float) $averages->punctuality_score, 2) : '-',
                                                            ],
                                                            [
                                                                'dimension' => 'O docente na REALIZAÇÃO de suas aulas procura contextualizar os conteúdos trabalhados; domina o conteúdo; utiliza bem os recursos didáticos; possui fala(dicção) clara, coerente e fluente?',
                                                                'media' => $averages->execution_score ? number_format((float) $averages->execution_score, 2) : '-',
                                                            ],
                                                            [
                                                                'dimension' => 'O docente nas AVALIAÇÕES mostra coerência entre o que foi ensinado e o que é exigido do estudante, entrega as avaliações e comenta os resultados, auxilia no processo de recuperação daqueles conteúdos não apreendidos?',
                                                                'media' => $averages->assessment_score ? number_format((float) $averages->assessment_score, 2) : '-',
                                                            ],
                                                        ],
                                                    ];
                                                })->toArray();
                                            }
                                        }

                                        return $record->cached_taught_disciplines_evaluations;
                                    })
                                    ->schema([
                                        TextEntry::make('discipline_label')
                                            ->label('Disciplina / Turma'),
                                        RepeatableEntry::make('dimensions')
                                            ->hiddenLabel()
                                            ->table([
                                                RepeatableEntry\TableColumn::make('Dimensão'),
                                                RepeatableEntry\TableColumn::make('Média'),
                                            ])
                                            ->schema([
                                                TextEntry::make('dimension')
                                                    ->hiddenLabel(),
                                                TextEntry::make('media')
                                                    ->hiddenLabel(),
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }
}
