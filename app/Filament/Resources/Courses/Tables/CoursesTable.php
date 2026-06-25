<?php

declare(strict_types=1);

namespace App\Filament\Resources\Courses\Tables;

use App\Models\Course;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                TextColumn::make('evaluations_status')
                    ->label('Avaliações')
                    ->badge()
                    ->color('primary')
                    ->getStateUsing(function (Course $record): string {
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
                ]),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
            ]);
    }
}
