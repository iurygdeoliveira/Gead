<?php

namespace App\Filament\Resources\CourseClasses\Pages;

use App\Filament\Resources\CourseClasses\CourseClassResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Traits\Filament\NotificationsTrait;

class EditCourseClass extends EditRecord
{
    use NotificationsTrait;

    protected static string $resource = CourseClassResource::class;

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

    public function getTitle(): string
    {
        return 'Editar Turma: '.$this->getRecord()->name;
    }
}
