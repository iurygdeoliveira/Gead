<?php

namespace App\Filament\Resources\CourseClasses\Pages;

use App\Filament\Resources\CourseClasses\CourseClassResource;
use App\Models\CourseClass;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditCourseClass extends EditRecord
{
    use NotificationsTrait;

    protected static string $resource = CourseClassResource::class;

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
            DeleteAction::make(),
        ];
    }

    #[\Override]
    public function getTitle(): string
    {
        /** @var CourseClass $record */
        $record = $this->getRecord();

        return 'Editar Turma: '.$record->name;
    }
}
