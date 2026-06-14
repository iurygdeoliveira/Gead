<?php

namespace App\Filament\Resources\Teachers\Schemas;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Discipline;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do Professor')
                    ->description('Informações básicas do professor.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->components(self::getPersonalDataFields()),

                Section::make('Atribuição de Disciplinas Ministradas')
                    ->description('Vincule as disciplinas de cursos e períodos que este professor ministrou.')
                    ->columnSpanFull()
                    ->visible(fn (string $operation): bool => $operation === 'edit' && (
                        Filament::auth()->user()?->hasRole(\App\Enums\RoleType::MANAGER->value) ||
                        Filament::auth()->user()?->hasRole(\App\Enums\RoleType::TAE->value)
                    ))
                    ->components([
                        Repeater::make('taughtDisciplines')
                            ->relationship('taughtDisciplines')
                            ->label('Disciplinas Ministradas')
                            ->schema([
                                self::getCourseField(),
                                self::getCourseClassField(),
                                self::getDisciplineField(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getPersonalDataFields(): array
    {
        return [
            TextInput::make('name')
                ->label('Nome Completo')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            TextInput::make('registration_number')
                ->label('Matrícula')
                ->maxLength(255),
        ];
    }

    public static function getCourseField(): Select
    {
        return Select::make('course_id')
            ->label('Curso')
            ->options(Course::pluck('name', 'id'))
            ->live()
            ->dehydrated(false)
            ->afterStateHydrated(function ($state, $set, $record) {
                if ($record && $record->courseClass) {
                    $set('course_id', $record->courseClass->course_id);
                }
            });
    }

    public static function getCourseClassField(): Select
    {
        return Select::make('course_class_id')
            ->label('Período')
            ->options(function (callable $get) {
                $courseId = $get('course_id');
                if (!$courseId) {
                    return CourseClass::all()->mapWithKeys(fn ($cc) => [$cc->id => $cc->entry_period]);
                }
                return CourseClass::where('course_id', $courseId)
                    ->get()
                    ->mapWithKeys(fn ($cc) => [$cc->id => $cc->entry_period]);
            })
            ->required()
            ->live();
    }

    public static function getDisciplineField(): Select
    {
        return Select::make('discipline_id')
            ->label('Disciplina')
            ->options(function (callable $get) {
                $courseId = $get('course_id');
                if (!$courseId) {
                    return Discipline::pluck('name', 'id');
                }
                return Discipline::where('course_id', $courseId)->pluck('name', 'id');
            })
            ->required();
    }
}
