<?php

namespace App\Filament\Resources\Evaluations\Schemas;

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
                            ->label('Matrícula na Turma')
                            ->relationship('classEnrollment', 'id')
                            ->searchable()
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
