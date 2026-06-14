<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Detalhes do Curso')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nome'),
                        TextEntry::make('code')
                            ->label('Código'),
                        TextEntry::make('team.name')
                            ->label('Campus Vinculado'),
                    ]),
                Section::make('Alunos Matriculados')
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('enrollments')
                            ->hiddenLabel()
                            ->getStateUsing(function ($record) {
                                return $record->enrollments->sortBy('student.name', SORT_NATURAL | SORT_FLAG_CASE);
                            })
                            ->table([
                                \Filament\Infolists\Components\RepeatableEntry\TableColumn::make('Nome do Aluno'),
                                \Filament\Infolists\Components\RepeatableEntry\TableColumn::make('E-mail'),
                                \Filament\Infolists\Components\RepeatableEntry\TableColumn::make('Matrícula'),
                                \Filament\Infolists\Components\RepeatableEntry\TableColumn::make('Período de Ingresso'),
                            ])
                            ->schema([
                                TextEntry::make('student.name')
                                    ->hiddenLabel(),
                                TextEntry::make('student.email')
                                    ->hiddenLabel(),
                                TextEntry::make('registration_number')
                                    ->hiddenLabel(),
                                TextEntry::make('entry_period')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ]);
    }
}
