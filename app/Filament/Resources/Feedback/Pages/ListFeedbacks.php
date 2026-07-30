<?php

declare(strict_types=1);

namespace App\Filament\Resources\Feedback\Pages;

use App\Filament\Resources\Feedback\FeedbackResource;
use App\Traits\Filament\HasFeedbackAction;
use Filament\Resources\Pages\ListRecords;

class ListFeedbacks extends ListRecords
{
    use HasFeedbackAction;

    protected static string $resource = FeedbackResource::class;
}
