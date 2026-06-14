<?php

namespace App\Filament\Resources\Evaluations\Tables;

use App\Filament\Resources\Evaluations\Actions\DeleteEvaluationAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;

class EvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('classEnrollment.student.name')
                    ->label('Aluno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('classEnrollment.courseClass.name')
                    ->label('Turma')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('planning_score')
                    ->label('Plan.')
                    ->sortable(),
                TextColumn::make('execution_score')
                    ->label('Exec.')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('secondary'),
                    EditAction::make(),
                    DeleteEvaluationAction::make(),
                ])
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
