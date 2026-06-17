<?php

namespace App\Filament\Resources\CourseClasses\Schemas;

use App\Models\ClassEnrollment;
use App\Models\Enrollment;
use App\Models\Teacher;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class CourseClassInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Class Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Detalhes da Turma')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                TextEntry::make('course.name')
                                    ->label('Curso'),
                                TextEntry::make('code')
                                    ->label('Código da Turma'),
                                TextEntry::make('entry_period')
                                    ->label('Período de Ingresso'),
                            ])
                            ->columns(3),
                        Tab::make('Alunos Matriculados')
                            ->icon('heroicon-o-users')
                            ->schema([
                                RepeatableEntry::make('enrolled_students')
                                    ->hiddenLabel()
                                    ->getStateUsing(function ($record) {
                                        return ClassEnrollment::query()
                                            ->where('course_class_id', $record->id)
                                            ->with(['enrollment.student'])
                                            ->get()
                                            ->map(function ($classEnrollment) {
                                                return [
                                                    'name' => $classEnrollment->enrollment?->student?->name ?? '-',
                                                    'registration_number' => $classEnrollment->enrollment?->registration_number ?? '-',
                                                    'email' => $classEnrollment->enrollment?->student?->email ?? '-',
                                                ];
                                            })
                                            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
                                    })
                                    ->table([
                                        TableColumn::make('Nome do Aluno'),
                                        TableColumn::make('Matrícula'),
                                        TableColumn::make('E-mail'),
                                    ])
                                    ->schema([
                                        TextEntry::make('name')
                                            ->hiddenLabel(),
                                        TextEntry::make('registration_number')
                                            ->hiddenLabel(),
                                        TextEntry::make('email')
                                            ->hiddenLabel(),
                                    ]),
                            ]),
                        Tab::make('Disciplinas')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                RepeatableEntry::make('disciplines')
                                    ->hiddenLabel()
                                    ->getStateUsing(function ($record) {
                                        // Load relations to avoid lazy-loading exceptions
                                        $record->loadMissing(['course.disciplines', 'disciplines']);

                                        // Load disciplines of the course linked to this cohort
                                        $course = $record->course;
                                        if (! $course) {
                                            return collect();
                                        }

                                        // We fetch all disciplines of the course, and associate the pivot/teacher info if it exists
                                        return $course->disciplines->map(function ($discipline) use ($record) {
                                            $cohortDiscipline = $record->disciplines()->where('discipline_id', $discipline->id)->first();
                                            $discipline->pivot = $cohortDiscipline ? $cohortDiscipline->pivot : null;

                                            return $discipline;
                                        })->sortBy('period', SORT_NATURAL | SORT_FLAG_CASE);
                                    })
                                    ->table([
                                        RepeatableEntry\TableColumn::make('Código'),
                                        RepeatableEntry\TableColumn::make('Nome'),
                                        RepeatableEntry\TableColumn::make('Docente'),
                                    ])
                                    ->schema([
                                        TextEntry::make('code')
                                            ->hiddenLabel(),
                                        TextEntry::make('name')
                                            ->hiddenLabel(),
                                        TextEntry::make('teacher_name')
                                            ->hiddenLabel()
                                            ->state(function ($record) {
                                                $teacherId = $record->pivot?->teacher_id;
                                                if ($teacherId) {
                                                    $teacher = Teacher::find($teacherId);

                                                    return $teacher ? $teacher->name : '-';
                                                }

                                                return '-';
                                            }),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }
}
