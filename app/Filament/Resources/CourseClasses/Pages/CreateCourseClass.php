<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourseClasses\Pages;

use App\Filament\Resources\CourseClasses\CourseClassResource;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\HasFeedbackAction;
use Filament\Resources\Pages\CreateRecord;

class CreateCourseClass extends CreateRecord
{
    use HasBackButtonAction;
    use HasFeedbackAction;

    protected static string $resource = CourseClassResource::class;
}
