<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do Aluno')
                    ->description('Informações básicas do aluno.')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->label('Nome Completo')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('E-mail Institucional')
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                    ]),
                Section::make('Matrículas no SUAP')
                    ->description('Vínculos do aluno com cursos e turmas.')
                    ->components([
                        \Filament\Forms\Components\Repeater::make('enrollments')
                            ->relationship()
                            ->label('Matrículas')
                            ->columns(3)
                            ->schema([
                                \Filament\Forms\Components\Select::make('course_id')
                                    ->relationship('course', 'name')
                                    ->label('Curso')
                                    ->required()
                                    ->searchable(),
                                \Filament\Forms\Components\TextInput::make('registration_number')
                                    ->label('Número de Matrícula')
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('entry_period')
                                    ->label('Período de Ingresso')
                                    ->placeholder('Ex: 2026.1')
                                    ->required(),
                                \Filament\Forms\Components\Repeater::make('classEnrollments')
                                    ->relationship()
                                    ->label('Vínculo em Turma')
                                    ->columnSpanFull()
                                    ->schema([
                                        \Filament\Forms\Components\Select::make('course_class_id')
                                            ->relationship('courseClass', 'name')
                                            ->label('Turma')
                                            ->required()
                                            ->searchable()
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->code})"),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
