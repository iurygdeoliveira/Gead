<?php

namespace App\Filament\Resources\Teachers\Schemas;

use App\Models\Teacher;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class TeacherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Teacher Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Detalhes do Professor')
                            ->icon(Heroicon::OutlinedInformationCircle)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Nome Completo'),
                                TextEntry::make('email')
                                    ->label('E-mail'),
                                TextEntry::make('registration_number')
                                    ->label('Matrícula'),
                                TextEntry::make('team.name')
                                    ->label('Campus Vinculado'),
                            ])
                            ->columns(2),
                        Tab::make('Disciplinas Ministradas')
                            ->icon(Heroicon::OutlinedAcademicCap)
                            ->schema([
                                RepeatableEntry::make('taught_disciplines')
                                    ->hiddenLabel()
                                    ->getStateUsing(function ($record) {
                                        if (! isset($record->cached_taught_disciplines)) {
                                            $record->cached_taught_disciplines = $record->taughtDisciplines()
                                                ->with(['courseClass.course', 'courseClass.academicTerm', 'discipline'])
                                                ->get()
                                                ->map(fn ($pivot): array => [
                                                    'course_name' => $pivot->courseClass->course->name ?? '-',
                                                    'academic_term' => $pivot->courseClass->academicTerm->name ?? '-',
                                                    'course_class_name' => $pivot->courseClass->name ?? $pivot->courseClass->code ?? '-',
                                                    'discipline_name' => $pivot->discipline->name ?? '-',
                                                ]);
                                        }

                                        return $record->cached_taught_disciplines;
                                    })
                                    ->table([
                                        RepeatableEntry\TableColumn::make('Curso'),
                                        RepeatableEntry\TableColumn::make('Período Letivo'),
                                        RepeatableEntry\TableColumn::make('Turma'),
                                        RepeatableEntry\TableColumn::make('Disciplina'),
                                    ])
                                    ->schema([
                                        TextEntry::make('course_name')
                                            ->hiddenLabel(),
                                        TextEntry::make('academic_term')
                                            ->hiddenLabel(),
                                        TextEntry::make('course_class_name')
                                            ->hiddenLabel(),
                                        TextEntry::make('discipline_name')
                                            ->hiddenLabel(),
                                    ]),
                            ]),
                        Tab::make('Pendências')
                            ->icon(Heroicon::OutlinedExclamationCircle)
                            ->schema([
                                RepeatableEntry::make('pending_evaluations')
                                    ->hiddenLabel()
                                    ->getStateUsing(fn (Teacher $record): array => $record->getPendingEvaluationsData())
                                    ->table([
                                        RepeatableEntry\TableColumn::make('Aluno'),
                                        RepeatableEntry\TableColumn::make('Turma'),
                                        RepeatableEntry\TableColumn::make('Disciplina'),
                                        RepeatableEntry\TableColumn::make('Status'),
                                    ])
                                    ->schema([
                                        TextEntry::make('student_name')
                                            ->hiddenLabel(),
                                        TextEntry::make('course_class_name')
                                            ->hiddenLabel(),
                                        TextEntry::make('discipline_name')
                                            ->hiddenLabel(),
                                        TextEntry::make('status')
                                            ->hiddenLabel()
                                            ->badge()
                                            ->color('danger'),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }
}
