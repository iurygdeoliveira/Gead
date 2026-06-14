<?php

namespace App\Filament\Resources\Teachers\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Forms\Components\Repeater\TableColumn;

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
                                        return \DB::table('course_class_disciplines')
                                            ->where('teacher_id', $record->id)
                                            ->get()
                                            ->map(function ($row) {
                                                $courseClass = \App\Models\CourseClass::with('course')->find($row->course_class_id);
                                                $discipline = \App\Models\Discipline::find($row->discipline_id);
                                                
                                                return [
                                                    'course_name' => $courseClass?->course?->name ?? '-',
                                                    'entry_period' => $courseClass?->entry_period ?? '-',
                                                    'discipline_name' => $discipline?->name ?? '-',
                                                ];
                                            });
                                    })
                                    ->table([
                                        TableColumn::make('Curso'),
                                        TableColumn::make('Período de Ingresso'),
                                        TableColumn::make('Disciplina'),
                                    ])
                                    ->schema([
                                        TextEntry::make('course_name')
                                            ->hiddenLabel(),
                                        TextEntry::make('entry_period')
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
