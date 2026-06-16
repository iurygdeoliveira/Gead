<?php

namespace App\Filament\Resources\Teachers\Tables;

use App\Filament\Resources\Teachers\Actions\ChangeTeacherAccessStatusBulkAction;
use App\Filament\Resources\Teachers\Actions\DeleteTeacherAction;
use App\Filament\Resources\Teachers\Actions\ToggleTeacherSuspensionAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('registration_number')
                    ->label('SIAPE')
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
                    DeleteTeacherAction::make()
                        ->icon(Heroicon::Trash),
                    ToggleTeacherSuspensionAction::make()
                        ->color('warning'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ChangeTeacherAccessStatusBulkAction::make(),
                ]),
            ]);
    }
}
