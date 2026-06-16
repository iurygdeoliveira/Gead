<?php

namespace App\Filament\Resources\CourseClasses\Actions;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class DeleteCourseClassAction
{
    public static function make(): Action
    {
        return Action::make('delete')
            ->label('Excluir')
            ->icon(Heroicon::Trash)
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn ($record) => $record->delete());
    }
}
