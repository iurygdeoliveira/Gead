<?php

namespace App\Filament\Resources\Evaluations\Schemas;

use App\Models\Evaluation;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

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
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('classEnrollment.courseClass.course.name')
                                    ->label('Turma (Curso)'),
                                TextEntry::make('courseClassDiscipline.teacher.name')
                                    ->label('Professor'),
                                TextEntry::make('courseClassDiscipline.discipline.name')
                                    ->label('Disciplina'),
                                TextEntry::make('courseClassDiscipline.courseClass.entry_period')
                                    ->label('Período Letivo'),
                            ])
                            ->columns(2),
                        Tab::make('Avaliação Docente')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                RepeatableEntry::make('evaluation_dimensions')
                                    ->hiddenLabel()
                                    ->getStateUsing(function ($record) {
                                        $averages = Evaluation::where('course_class_discipline_id', $record->course_class_discipline_id)
                                            ->selectRaw('AVG(planning_score) as planning_score, AVG(posture_score) as posture_score, AVG(attendance_score) as attendance_score, AVG(punctuality_score) as punctuality_score, AVG(execution_score) as execution_score, AVG(assessment_score) as assessment_score')
                                            ->first();

                                        return [
                                            [
                                                'dimension' => 'O docente apresenta seu plano de ensino (PLANEJAMENTO) no início do semestre ou ano letivo, indicando a ementa, competências e habilidades, recursos didáticos que serão utilizados, formas de avaliações, referências bibliográficas?',
                                                'media' => $averages->planning_score ? number_format($averages->planning_score, 2) : '-',
                                            ],
                                            [
                                                'dimension' => 'O docente apresenta uma POSTURA adequada ao cargo e responsabilidade que ocupa?',
                                                'media' => $averages->posture_score ? number_format($averages->posture_score, 2) : '-',
                                            ],
                                            [
                                                'dimension' => 'O docente é ASSÍDUO, ou seja, não falta às aulas e quando falta, apresenta justificativa e promove suas devidas reposições ou anteposições?',
                                                'media' => $averages->attendance_score ? number_format($averages->attendance_score, 2) : '-',
                                            ],
                                            [
                                                'dimension' => 'O docente é PONTUAL, ou seja, não chega atrasado ou libera a turma mais cedo?',
                                                'media' => $averages->punctuality_score ? number_format($averages->punctuality_score, 2) : '-',
                                            ],
                                            [
                                                'dimension' => 'O docente na REALIZAÇÃO de suas aulas procura contextualizar os conteúdos trabalhados; domina o conteúdo; utiliza bem os recursos didáticos; possui fala(dicção) clara, coerente e fluente?',
                                                'media' => $averages->execution_score ? number_format($averages->execution_score, 2) : '-',
                                            ],
                                            [
                                                'dimension' => 'O docente nas AVALIAÇÕES mostra coerência entre o que foi ensinado e o que é exigido do estudante, entrega as avaliações e comenta os resultados, auxilia no processo de recuperação daqueles conteúdos não apreendidos?',
                                                'media' => $averages->assessment_score ? number_format($averages->assessment_score, 2) : '-',
                                            ],
                                        ];
                                    })
                                    ->table([
                                        TableColumn::make('Dimensão'),
                                        TableColumn::make('Média'),
                                    ])
                                    ->schema([
                                        TextEntry::make('dimension')
                                            ->hiddenLabel(),
                                        TextEntry::make('media')
                                            ->hiddenLabel(),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }
}
