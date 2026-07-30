<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourseClasses\Pages;

use App\Filament\Resources\CourseClasses\CourseClassResource;
use App\Models\CourseClass;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\HasFeedbackAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCourseClass extends ViewRecord
{
    use HasBackButtonAction;
    use HasFeedbackAction;

    protected static string $resource = CourseClassResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
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
