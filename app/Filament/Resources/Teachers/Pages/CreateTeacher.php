<?php

declare(strict_types=1);

namespace App\Filament\Resources\Teachers\Pages;

use App\Traits\Filament\HasBackButtonAction;

use App\Traits\Filament\HasFeedbackAction;

use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeacher extends CreateRecord
{
    use HasBackButtonAction;

    use HasFeedbackAction;

    protected static string $resource = TeacherResource::class;
}
