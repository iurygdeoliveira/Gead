<?php

namespace App\Filament\Resources\Students\Pages;

use App\Traits\Filament\HasBackButtonAction;

use App\Traits\Filament\HasFeedbackAction;

use App\Filament\Resources\Students\StudentResource;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditStudent extends EditRecord
{
    use HasBackButtonAction;

    use HasFeedbackAction;

    use NotificationsTrait;

    protected static string $resource = StudentResource::class;

    #[\Override]
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    #[\Override]
    protected function getSavedNotification(): ?Notification
    {
        return $this->buildNotification('primary', 'Registro atualizado com sucesso!');
    }

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('delete')
                ->label('Excluir')
                ->icon(Heroicon::Trash)
                ->color('danger')
                ->visible(fn (): bool => Filament::auth()->user()?->can('delete', $this->getRecord()) ?? false)
                ->url(fn (): string => static::getResource()::getUrl('delete', ['record' => $this->getRecord()])),
        ];
    }
}
