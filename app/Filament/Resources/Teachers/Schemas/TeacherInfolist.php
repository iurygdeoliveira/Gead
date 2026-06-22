<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

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
                            ->icon('heroicon-o-information-circle')
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
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                RepeatableEntry::make('taught_disciplines')
                                    ->hiddenLabel()
                                    ->getStateUsing(function ($record) {
                                        if (! isset($record->cached_taught_disciplines)) {
                                            $record->cached_taught_disciplines = $record->taughtDisciplines()
                                                ->with(['courseClass.course', 'courseClass.academicTerm', 'discipline'])
                                                ->get()
                                                ->map(function ($pivot) {
                                                    return [
                                                        'course_name' => $pivot->courseClass?->course?->name ?? '-',
                                                        'academic_term' => $pivot->courseClass?->academicTerm?->name ?? '-',
                                                        'course_class_name' => $pivot->courseClass?->name ?? $pivot->courseClass?->code ?? '-',
                                                        'discipline_name' => $pivot->discipline?->name ?? '-',
                                                    ];
                                                });
                                        }

                                        return $record->cached_taught_disciplines;
                                    })
                                    ->table([
                                        TableColumn::make('Curso'),
                                        TableColumn::make('Período Letivo'),
                                        TableColumn::make('Turma'),
                                        TableColumn::make('Disciplina'),
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
                    ])
                    ->persistTabInQueryString(),
            ]);
    }
}
