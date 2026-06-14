<?php

namespace App\Filament\Resources\Evaluations\Actions;

use Filament\Tables\Actions\Action;
use Filament\Support\Icons\Heroicon;

class DeleteEvaluationAction
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
