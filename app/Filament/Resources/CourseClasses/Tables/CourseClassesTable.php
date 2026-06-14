<?php

namespace App\Filament\Resources\CourseClasses\Tables;

use App\Filament\Resources\CourseClasses\Actions\DeleteCourseClassAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;

class CourseClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.name')
                    ->label('Curso')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('entry_period')
                    ->label('Período de Ingresso')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
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
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
