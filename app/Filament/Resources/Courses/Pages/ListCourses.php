<?php

declare(strict_types=1);

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Courses\Widgets\CoursesStats;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    #[\Override]
    protected function getHeaderWidgets(): array
    {
        return [
            CoursesStats::class,
        ];
    }
}
