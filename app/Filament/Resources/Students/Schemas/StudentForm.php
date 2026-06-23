<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do Aluno')
                    ->description('Informações básicas do aluno.')
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('name')
                            ->label('Nome Completo')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('E-mail Institucional')
                            ->email()
                            ->maxLength(255)
                            ->unique(modifyRuleUsing: fn (Unique $rule) => $rule->where('team_id', Filament::getTenant()?->getKey())),
                    ]),
                Section::make('Matrículas no SUAP')
                    ->description('Vínculos do aluno com cursos e turmas.')
                    ->columnSpanFull()
                    ->components([
                        Repeater::make('enrollments')
                            ->relationship()
                            ->label('Matrículas')
                            ->columns(3)
                            ->schema([
                                Select::make('course_id')
                                    ->relationship('course', 'name')
                                    ->label('Curso')
                                    ->required()
                                    ->searchable(),
                                TextInput::make('registration_number')
                                    ->label('Número de Matrícula')
                                    ->required(),
                                TextInput::make('entry_period')
                                    ->label('Período de Ingresso')
                                    ->placeholder('Ex: 2026.1')
                                    ->required(),
                                Repeater::make('classEnrollments')
                                    ->relationship()
                                    ->label('Vínculo em Turma')
                                    ->columnSpanFull()
                                    ->schema([
                                        Select::make('course_class_id')
                                            ->relationship('courseClass', 'name')
                                            ->label('Turma')
                                            ->required()
                                            ->searchable()
                                            ->getOptionLabelFromRecordUsing(fn ($record): string => "{$record->name} ({$record->code})"),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
