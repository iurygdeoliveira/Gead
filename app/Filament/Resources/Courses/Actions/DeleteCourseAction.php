<?php

namespace App\Filament\Resources\Courses\Actions;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;

class DeleteCourseAction
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
            ->url(fn ($record): string => CourseResource::getUrl('delete', ['record' => $record]))
            ->openUrlInNewTab(false);
    }
}
