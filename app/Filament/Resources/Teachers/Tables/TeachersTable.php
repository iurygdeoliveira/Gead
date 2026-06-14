<?php

namespace App\Filament\Resources\Teachers\Tables;

use App\Filament\Resources\Teachers\Actions\ChangeTeacherAccessStatusBulkAction;
use App\Filament\Resources\Teachers\Actions\DeleteTeacherAction;
use App\Filament\Resources\Teachers\Actions\ToggleTeacherSuspensionAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;

class TeachersTable
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
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->sortable(),
                TextColumn::make('registration_number')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable(),
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
                    DeleteTeacherAction::make()
                        ->icon(Heroicon::Trash),
                    ToggleTeacherSuspensionAction::make()
                    ->color('warning'),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ChangeTeacherAccessStatusBulkAction::make(),
                ]),
            ]);
    }
}
