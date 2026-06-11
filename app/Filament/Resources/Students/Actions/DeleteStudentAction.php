<?php

namespace App\Filament\Resources\Students\Actions;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;

class DeleteStudentAction
{
    public static function make(): Action
    {
        return Action::make('delete')
            ->label('Excluir')
            ->icon(Heroicon::Trash)
            ->color('danger')
            ->visible(
                fn ($record): bool => Filament::auth()->user()?->can('delete', $record) ?? false
            )
            ->url(fn ($record): string => StudentResource::getUrl('delete', ['record' => $record]))
            ->openUrlInNewTab(false);
    }
}
