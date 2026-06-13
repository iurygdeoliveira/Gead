<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Filament\Resources\Courses\Actions\DeleteCourseAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->label('Nome')
                    ->wrap(),
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
                    DeleteCourseAction::make()
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
