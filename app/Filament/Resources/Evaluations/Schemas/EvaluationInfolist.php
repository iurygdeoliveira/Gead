<?php

namespace App\Filament\Resources\Evaluations\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class EvaluationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Avaliação')
                    ->components([
                        TextEntry::make('classEnrollment.student.name')
                            ->label('Aluno'),
                        TextEntry::make('classEnrollment.courseClass.name')
                            ->label('Turma'),
                        TextEntry::make('planning_score')
                            ->label('Planejamento'),
                        TextEntry::make('posture_score')
                            ->label('Postura'),
                        TextEntry::make('attendance_score')
                            ->label('Assiduidade'),
                        TextEntry::make('punctuality_score')
                            ->label('Pontualidade'),
                        TextEntry::make('execution_score')
                            ->label('Realização das Aulas'),
                        TextEntry::make('assessment_score')
                            ->label('Avaliações'),
                        TextEntry::make('comments')
                            ->label('Comentários')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
