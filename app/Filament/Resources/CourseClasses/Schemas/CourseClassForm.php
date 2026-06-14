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
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nome da Turma')
                            ->placeholder('Ex: Turma A')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('code')
                            ->label('Código')
                            ->maxLength(255),
                        \Filament\Forms\Components\Select::make('discipline_id')
                            ->label('Disciplina')
                            ->relationship('discipline', 'name')
                            ->searchable()
                            ->required()
                            ->preload(),
                        \Filament\Forms\Components\Select::make('teacher_id')
                            ->label('Docente')
                            ->relationship('teacher', 'name')
                            ->searchable()
                            ->required()
                            ->preload(),
                        \Filament\Forms\Components\Select::make('academic_term_id')
                            ->label('Período Letivo')
                            ->relationship('academicTerm', 'name')
                            ->searchable()
                            ->required()
                            ->preload(),
                    ]),
            ]);
    }
}
