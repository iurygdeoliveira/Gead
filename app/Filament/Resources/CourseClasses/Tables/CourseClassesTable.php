<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourseClasses\Tables;

use App\Filament\Resources\CourseClasses\Actions\DeleteCourseClassAction;
use App\Models\CourseClass;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CourseClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Turma')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome da Turma')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('course.name')
                    ->label('Curso')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('evaluations_status')
                    ->label('Avaliações')
                    ->badge()
                    ->color('primary')
                    ->getStateUsing(function (CourseClass $record): string {
                        $status = $record->getEvaluationsCompletionStatus();

                        return "{$status['completed']} / {$status['expected']}";
                    })
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->icon(Heroicon::Eye)
                        ->color('secondary'),
                    EditAction::make()
                        ->icon(Heroicon::Pencil),
                    DeleteCourseClassAction::make()
                        ->icon(Heroicon::Trash),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
