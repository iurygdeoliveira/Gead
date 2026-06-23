<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Traits\Filament\NotificationsTrait;

class EditCourse extends EditRecord
{
    use NotificationsTrait;

    protected static string $resource = CourseResource::class;

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
            \Filament\Actions\Action::make('delete')
                ->label('Excluir')
                ->icon('heroicon-m-trash')
                ->color('danger')
                ->visible(fn (): bool => \Filament\Facades\Filament::auth()->user()?->can('delete', $this->getRecord()) ?? false)
                ->url(fn (): string => static::getResource()::getUrl('delete', ['record' => $this->getRecord()])),
        ];
    }
}
