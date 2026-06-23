<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourseClasses\Pages;

use App\Filament\Resources\CourseClasses\CourseClassResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseClass extends CreateRecord
{
    protected static string $resource = CourseClassResource::class;
}
