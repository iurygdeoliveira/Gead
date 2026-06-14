<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Dados do Professor')
                    ->description('Informações básicas do professor.')
                    ->columns(2)
                    ->components([
                        \Filament\Forms\Components\TextInput::make('name')
                            ->label('Nome Completo')
                            ->required()
                            ->maxLength(255),
                        \Filament\Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        \Filament\Forms\Components\TextInput::make('registration_number')
                            ->label('Matrícula')
                            ->maxLength(255),
                    ]),

                \Filament\Schemas\Components\Section::make('Atribuição de Disciplinas Ministradas')
                    ->description('Vincule as disciplinas de cursos e períodos que este professor ministrou.')
                    ->visible(fn (string $operation): bool => $operation === 'edit' && (
                        auth()->user()?->hasRole(\App\Enums\RoleType::MANAGER->value) ||
                        auth()->user()?->hasRole(\App\Enums\RoleType::TAE->value)
                    ))
                    ->components([
                        \Filament\Forms\Components\Repeater::make('taughtDisciplines')
                            ->relationship('taughtDisciplines')
                            ->label('Disciplinas Ministradas')
                            ->schema([
                                \Filament\Forms\Components\Select::make('course_id')
                                    ->label('Curso')
                                    ->options(\App\Models\Course::pluck('name', 'id'))
                                    ->live()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function ($state, $set, $record) {
                                        if ($record && $record->courseClass) {
                                            $set('course_id', $record->courseClass->course_id);
                                        }
                                    }),
                                \Filament\Forms\Components\Select::make('course_class_id')
                                    ->label('Turma (Período)')
                                    ->options(function (callable $get) {
                                        $courseId = $get('course_id');
                                        if (!$courseId) {
                                            return \App\Models\CourseClass::all()->mapWithKeys(fn ($cc) => [$cc->id => "{$cc->code} ({$cc->entry_period})"]);
                                        }
                                        return \App\Models\CourseClass::where('course_id', $courseId)
                                            ->get()
                                            ->mapWithKeys(fn ($cc) => [$cc->id => "{$cc->code} ({$cc->entry_period})"]);
                                    })
                                    ->required()
                                    ->live(),
                                \Filament\Forms\Components\Select::make('discipline_id')
                                    ->label('Disciplina')
                                    ->options(function (callable $get) {
                                        $courseId = $get('course_id');
                                        if (!$courseId) {
                                            return \App\Models\Discipline::pluck('name', 'id');
                                        }
                                        return \App\Models\Discipline::where('course_id', $courseId)->pluck('name', 'id');
                                    })
                                    ->required(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
