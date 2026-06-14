<?php

namespace App\Filament\Resources\Students\Tables;

use App\Filament\Resources\Students\Actions\ChangeStudentAccessStatusBulkAction;
use App\Filament\Resources\Students\Actions\DeleteStudentAction;
use App\Filament\Resources\Students\Actions\ToggleStudentSuspensionAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (\Illuminate\Database\Eloquent\Builder $query) => $query->with('user'))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->wrap(),
                TextColumn::make('enrollments.registration_number')
                    ->label('Matrícula(s)')
                    ->listWithLineBreaks()
                    ->wrap(),
                TextColumn::make('enrollments.course.name')
                    ->label('Curso')
                    ->listWithLineBreaks()
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
                    DeleteStudentAction::make()
                        ->icon(Heroicon::Trash),
                    ToggleStudentSuspensionAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ChangeStudentAccessStatusBulkAction::make(),
                ]),
            ]);
    }
}
