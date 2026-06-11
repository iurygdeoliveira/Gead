<?php

namespace App\Filament\Resources\Teachers\Actions;

use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;

class DeleteTeacherAction
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
            ->url(fn ($record): string => TeacherResource::getUrl('delete', ['record' => $record]))
            ->openUrlInNewTab(false);
    }
}
