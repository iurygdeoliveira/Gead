<?php

namespace App\Filament\Resources\CourseClasses\Schemas;

use Filament\Schemas\Schema;

class CourseClassForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Dados da Turma')
                    ->columns(2)
                    ->components([
                        \Filament\Forms\Components\Select::make('course_id')
                            ->label('Curso')
                            ->relationship('course', 'name')
                            ->searchable()
                            ->required()
                            ->preload(),
                        \Filament\Forms\Components\TextInput::make('entry_period')
                            ->label('Período de Ingresso')
                            ->placeholder('Ex: 2026.1')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nome da Turma')
                            ->placeholder('Ex: Técnico em Biotecnologia - 2026.1')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('code')
                            ->label('Código')
                            ->placeholder('Ex: 211-2026.1')
                            ->maxLength(255),
                        \Filament\Forms\Components\Select::make('academic_term_id')
                            ->label('Período Letivo')
                            ->relationship('academicTerm', 'name')
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
}
