<?php

namespace App\Filament\Resources\CourseClasses\Schemas;

use App\Models\ClassEnrollment;
use App\Models\Teacher;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

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
                            ->icon(Heroicon::OutlinedInformationCircle)
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
                            ->icon(Heroicon::OutlinedUsers)
                            ->schema([
                                RepeatableEntry::make('enrolled_students')
                                    ->hiddenLabel()
                                    ->getStateUsing(fn ($record) => ClassEnrollment::query()
                                        ->where('course_class_id', $record->id)
                                        ->with(['enrollment.student'])
                                        ->get()
                                        ->map(fn ($classEnrollment): array => [
                                            'name' => $classEnrollment->enrollment->student->name ?? '-',
                                            'registration_number' => $classEnrollment->enrollment->registration_number ?? '-',
                                            'email' => $classEnrollment->enrollment->student->email ?? '-',
                                        ])
                                        ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE))
                                    ->table([
                                        RepeatableEntry\TableColumn::make('Nome do Aluno'),
                                        RepeatableEntry\TableColumn::make('Matrícula'),
                                        RepeatableEntry\TableColumn::make('E-mail'),
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
                            ->icon(Heroicon::OutlinedAcademicCap)
                            ->schema([
                                RepeatableEntry::make('disciplines')
                                    ->hiddenLabel()
                                    ->getStateUsing(function ($record) {
                                        // Load relations to avoid lazy-loading exceptions
                                        $record->loadMissing(['disciplines']);

                                        // We fetch all disciplines of the cohort and group them by ID to handle multiple teachers
                                        return $record->disciplines->groupBy('id')->map(function ($group) {
                                            $discipline = $group->first();
                                            // collect all teacher_ids
                                            $teacherIds = $group->pluck('pivot.teacher_id')->filter()->unique();
                                            $teachers = Teacher::whereIn('id', $teacherIds)->get();
                                            $discipline->class_teachers = $teachers;

                                            return $discipline;
                                        })->values()->sortBy('period', SORT_NATURAL | SORT_FLAG_CASE);
                                    })
                                    ->table([
                                        RepeatableEntry\TableColumn::make('Código'),
                                        RepeatableEntry\TableColumn::make('Nome'),
                                        RepeatableEntry\TableColumn::make('Docente(s)'),
                                    ])
                                    ->schema([
                                        TextEntry::make('code')
                                            ->hiddenLabel(),
                                        TextEntry::make('name')
                                            ->hiddenLabel(),
                                        TextEntry::make('teacher_name')
                                            ->hiddenLabel()
                                            ->badge()
                                            ->color('primary')
                                            ->state(function ($record) {
                                                if (isset($record->class_teachers) && $record->class_teachers->count() > 0) {
                                                    return $record->class_teachers->pluck('name')->toArray();
                                                }

                                                return ['-'];
                                            }),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }
}
