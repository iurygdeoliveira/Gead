<?php

declare(strict_types=1);

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\HasFeedbackAction;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    use HasBackButtonAction;
    use HasFeedbackAction;

    protected static string $resource = StudentResource::class;
}
