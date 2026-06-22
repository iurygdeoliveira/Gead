<?php

namespace App\Filament\Resources\Teachers\Schemas;

use App\Enums\RoleType;
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
                        Filament::auth()->user()?->hasRole(RoleType::MANAGER->value) ||
                        Filament::auth()->user()?->hasRole(RoleType::TAE->value)
                    ))
                    ->components([
                        Repeater::make('taughtDisciplines')
                            ->relationship('taughtDisciplines')
                            ->label('Disciplinas Ministradas')
                            ->schema([
                                self::getCourseField(),
                                self::getPeriodField(),
                                self::getCourseClassField(),
                                self::getDisciplineField(),
                            ])
                            ->columns(4)
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
                if ($record) {
                    $record->loadMissing('courseClass');
                    if ($record->courseClass) {
                        $set('course_id', $record->courseClass->course_id);
                    }
                }
            });
    }

    public static function getPeriodField(): Select
    {
        return Select::make('period')
            ->label('Período')
            ->options(function (callable $get) {
                $courseId = $get('course_id');

                return once(function () use ($courseId) {
                    if (! $courseId) {
                        return CourseClass::all()->pluck('entry_period', 'entry_period')->unique();
                    }

                    return CourseClass::where('course_id', $courseId)
                        ->pluck('entry_period', 'entry_period')
                        ->unique();
                });
            })
            ->required()
            ->live()
            ->dehydrated(false)
            ->afterStateHydrated(function ($state, $set, $record) {
                if ($record) {
                    $record->loadMissing('courseClass');
                    if ($record->courseClass) {
                        $set('period', $record->courseClass->entry_period);
                    }
                }
            });
    }

    public static function getCourseClassField(): Select
    {
        return Select::make('course_class_id')
            ->label('Turma')
            ->options(function (callable $get) {
                $courseId = $get('course_id');
                $period = $get('period');

                return once(function () use ($courseId, $period) {
                    $query = CourseClass::query();
                    if ($courseId) {
                        $query->where('course_id', $courseId);
                    }
                    if ($period) {
                        $query->where('entry_period', $period);
                    }

                    return $query->get()->mapWithKeys(fn ($cc) => [$cc->id => $cc->name ?? $cc->code]);
                });
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

                return once(function () use ($courseId) {
                    $query = Discipline::query();
                    if ($courseId) {
                        $query->where('course_id', $courseId);
                    }

                    return $query->pluck('name', 'id');
                });
            })
            ->required();
    }
}
