<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditCourse extends EditRecord
{
    use NotificationsTrait;

    protected static string $resource = CourseResource::class;

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
            Action::make('back')
                ->label('Voltar')
                ->icon(Heroicon::ArrowLeft)
                ->color('gray')
                ->url(fn () => static::getResource()::getUrl('index')),
            Action::make('delete')
                ->label('Excluir')
                ->icon(Heroicon::Trash)
                ->color('danger')
                ->visible(fn (): bool => Filament::auth()->user()?->can('delete', $this->getRecord()) ?? false)
                ->url(fn (): string => static::getResource()::getUrl('delete', ['record' => $this->getRecord()])),
        ];
    }
}
