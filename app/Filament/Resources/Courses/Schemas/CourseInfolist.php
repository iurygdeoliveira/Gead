<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\ViewEntry;

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
                            ])
                            ->columns(3),
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
                                        TableColumn::make('Período de Ingresso'),
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
                                        TableColumn::make('Período'),
                                    ])
                                    ->schema([
                                        TextEntry::make('code')
                                            ->hiddenLabel(),
                                        TextEntry::make('name')
                                            ->hiddenLabel(),
                                        TextEntry::make('period')
                                            ->hiddenLabel(),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }
}
