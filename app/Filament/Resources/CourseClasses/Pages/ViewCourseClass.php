<?php

namespace App\Filament\Resources\CourseClasses\Pages;

use App\Filament\Resources\CourseClasses\CourseClassResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCourseClass extends ViewRecord
{
    protected static string $resource = CourseClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
