<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Voltar')
                ->icon('heroicon-m-arrow-left')
                ->color('gray')
                ->url(fn () => static::getResource()::getUrl('index')),
            EditAction::make(),
        ];
    }
}
