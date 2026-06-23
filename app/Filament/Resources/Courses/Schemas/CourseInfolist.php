<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

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
                            ->icon(Heroicon::OutlinedInformationCircle)
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
                            ->icon(Heroicon::OutlinedUsers)
                            ->schema([
                                RepeatableEntry::make('enrollments')
                                    ->hiddenLabel()
                                    ->getStateUsing(fn ($record) => $record->enrollments->sortBy('student.name', SORT_NATURAL | SORT_FLAG_CASE))
                                    ->table([
                                        RepeatableEntry\TableColumn::make('Nome do Aluno'),
                                        RepeatableEntry\TableColumn::make('E-mail'),
                                        RepeatableEntry\TableColumn::make('Matrícula'),
                                        RepeatableEntry\TableColumn::make('Turma'),
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
                            ->icon(Heroicon::OutlinedAcademicCap)
                            ->schema([
                                RepeatableEntry::make('disciplines')
                                    ->hiddenLabel()
                                    ->getStateUsing(fn ($record) => $record->disciplines->sortBy('period', SORT_NATURAL | SORT_FLAG_CASE))
                                    ->table([
                                        RepeatableEntry\TableColumn::make('Código'),
                                        RepeatableEntry\TableColumn::make('Nome'),
                                        RepeatableEntry\TableColumn::make('Professores'),
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
