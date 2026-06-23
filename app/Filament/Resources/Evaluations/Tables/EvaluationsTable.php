<?php

namespace App\Filament\Resources\Evaluations\Tables;

use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->whereIn('id', function ($q): void {
                    $q->selectRaw('MAX(id)')
                        ->from('evaluations')
                        ->groupBy('course_class_discipline_id');
                })
                ->with([
                    'courseClassDiscipline.courseClass.course',
                    'courseClassDiscipline.discipline',
                    'courseClassDiscipline.teacher',
                ])
            )
            ->defaultSort('course_class_discipline_id')
            ->columns([

                TextColumn::make('courseClassDiscipline.teacher.name')
                    ->label('Professor')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->weight(FontWeight::Medium)
                    ->alignLeft(),

                TextColumn::make('courseClassDiscipline.discipline.name')
                    ->label('Disciplina')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->color('gray')
                    ->alignLeft(),

                TextColumn::make('courseClassDiscipline.courseClass.name')
                    ->label('Turma')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->color('gray')
                    ->alignLeft(),

                TextColumn::make('teaching_period')
                    ->label('Período Letivo')
                    ->getStateUsing(fn ($record): string => '2026.1')
                    ->color('gray')
                    ->alignLeft(),

            ])
            ->filters([

            ])
            ->recordActions([
                ViewAction::make()
                    ->color('secondary'),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
