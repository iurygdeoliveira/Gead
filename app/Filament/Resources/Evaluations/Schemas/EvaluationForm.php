<?php

namespace App\Filament\Resources\Evaluations\Schemas;

use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Avaliação Docente')
                    ->description('Dimensões baseadas no Relatório de Avaliação Discente.')
                    ->columns(2)
                    ->components([
                        \Filament\Forms\Components\Select::make('class_enrollment_id')
                            ->label('Aluno / Turma')
                            ->relationship(
                                name: 'classEnrollment',
                                titleAttribute: 'id',
                                modifyQueryUsing: fn ($query) => $query->with(['enrollment.student', 'courseClass'])
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => ($record->enrollment && $record->enrollment->student ? $record->enrollment->student->name : 'Matrícula #' . $record->id) . ' - ' . ($record->courseClass ? $record->courseClass->name : ''))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        \Filament\Forms\Components\Select::make('course_class_discipline_id')
                            ->label('Professor / Disciplina')
                            ->options(function (Get $get) {
                                $classEnrollmentId = $get('class_enrollment_id');
                                if (!$classEnrollmentId) {
                                    return [];
                                }
                                $classEnrollment = \App\Models\ClassEnrollment::find($classEnrollmentId);
                                if (!$classEnrollment) {
                                    return [];
                                }
                                return \App\Models\CourseClassDiscipline::where('course_class_id', $classEnrollment->course_class_id)
                                    ->with(['teacher', 'discipline'])
                                    ->get()
                                    ->mapWithKeys(function ($ccd) {
                                        $teacherName = $ccd->teacher ? $ccd->teacher->name : 'Sem Professor';
                                        return [$ccd->id => "{$ccd->discipline->name} (Prof. {$teacherName})"];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('planning_score')
                            ->label('1. Planejamento (0 a 10)')
                            ->numeric()
                            ->maxValue(10),
                        \Filament\Forms\Components\TextInput::make('posture_score')
                            ->label('2. Postura (0 a 10)')
                            ->numeric()
                            ->maxValue(10),
                        \Filament\Forms\Components\TextInput::make('attendance_score')
                            ->label('3. Assiduidade (0 a 10)')
                            ->numeric()
                            ->maxValue(10),
                        \Filament\Forms\Components\TextInput::make('punctuality_score')
                            ->label('4. Pontualidade (0 a 10)')
                            ->numeric()
                            ->maxValue(10),
                        \Filament\Forms\Components\TextInput::make('execution_score')
                            ->label('5. Realização das Aulas (0 a 10)')
                            ->numeric()
                            ->maxValue(10),
                        \Filament\Forms\Components\TextInput::make('assessment_score')
                            ->label('6. Avaliações (0 a 10)')
                            ->numeric()
                            ->maxValue(10),
                        \Filament\Forms\Components\Textarea::make('comments')
                            ->label('Comentários')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
