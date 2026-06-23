<?php

namespace App\Filament\Resources\CourseClasses\Pages;

use App\Filament\Resources\CourseClasses\CourseClassResource;
use App\Models\CourseClass;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewCourseClass extends ViewRecord
{
    protected static string $resource = CourseClassResource::class;

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

    #[\Override]
    public function getTitle(): string
    {
        /** @var CourseClass $record */
        $record = $this->getRecord();

        return 'Visualizar Turma: '.$record->name;
    }
}
