<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Traits\Filament\NotificationsTrait;

class EditEvaluation extends EditRecord
{
    use NotificationsTrait;

    protected static string $resource = EvaluationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return $this->buildNotification('primary', 'Registro atualizado com sucesso!');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Voltar')
                ->icon('heroicon-m-arrow-left')
                ->color('gray')
                ->url(fn () => static::getResource()::getUrl('index')),
            DeleteAction::make(),
        ];
    }
}
