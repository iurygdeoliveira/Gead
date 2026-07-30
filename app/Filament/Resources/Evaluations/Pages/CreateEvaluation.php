<?php

declare(strict_types=1);

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\HasFeedbackAction;
use Filament\Resources\Pages\CreateRecord;

class CreateEvaluation extends CreateRecord
{
    use HasBackButtonAction;
    use HasFeedbackAction;

    protected static string $resource = EvaluationResource::class;
}
