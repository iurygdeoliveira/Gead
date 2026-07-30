<?php

declare(strict_types=1);

namespace App\Filament\Resources\Courses\Pages;

use App\Traits\Filament\HasBackButtonAction;

use App\Traits\Filament\HasFeedbackAction;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    use HasBackButtonAction;

    use HasFeedbackAction;

    protected static string $resource = CourseResource::class;
}
