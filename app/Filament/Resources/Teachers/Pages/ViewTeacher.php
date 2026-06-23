<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewTeacher extends ViewRecord
{
    protected static string $resource = TeacherResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Voltar')
                ->icon(Heroicon::ArrowLeft)
                ->color('gray')
                ->url(fn () => static::getResource()::getUrl('index')),
            EditAction::make(),
        ];
    }
}
