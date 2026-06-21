<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Course Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Detalhes do Curso')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nome'),
                                TextEntry::make('code')
                                    ->label('Código'),
                                TextEntry::make('team.name')
                                    ->label('Campus Vinculado'),
                                TextEntry::make('students_count')
                                    ->label('Total de Alunos Matriculados')
                                    ->getStateUsing(fn ($record) => $record->students()->count()),
                            ])
                            ->columns(4),
                        Tab::make('Alunos Matriculados')
                            ->icon('heroicon-o-users')
                            ->schema([
                                RepeatableEntry::make('enrollments')
                                    ->hiddenLabel()
                                    ->getStateUsing(function ($record) {
                                        return $record->enrollments->sortBy('student.name', SORT_NATURAL | SORT_FLAG_CASE);
                                    })
                                    ->table([
                                        TableColumn::make('Nome do Aluno'),
                                        TableColumn::make('E-mail'),
                                        TableColumn::make('Matrícula'),
                                        TableColumn::make('Turma'),
                                    ])
                                    ->schema([
                                        TextEntry::make('student.name')
                                            ->hiddenLabel(),
                                        TextEntry::make('student.email')
                                            ->hiddenLabel(),
                                        TextEntry::make('registration_number')
                                            ->hiddenLabel(),
                                        TextEntry::make('classEnrollments.courseClass.name')
                                            ->badge()
                                            ->hiddenLabel(),
                                    ]),
                            ]),
                        Tab::make('Disciplinas')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                RepeatableEntry::make('disciplines')
                                    ->hiddenLabel()
                                    ->getStateUsing(function ($record) {
                                        return $record->disciplines->sortBy('period', SORT_NATURAL | SORT_FLAG_CASE);
                                    })
                                    ->table([
                                        TableColumn::make('Código'),
                                        TableColumn::make('Nome'),
                                        TableColumn::make('Professores'),
                                    ])
                                    ->schema([
                                        TextEntry::make('code')
                                            ->hiddenLabel(),
                                        TextEntry::make('name')
                                            ->hiddenLabel(),
                                        TextEntry::make('teachers.name')
                                            ->badge()
                                            ->hiddenLabel(),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }
}
