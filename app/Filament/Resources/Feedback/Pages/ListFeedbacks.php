<?php

declare(strict_types=1);

namespace App\Filament\Resources\Feedback\Pages;

use App\Filament\Resources\Feedback\FeedbackResource;
use Filament\Resources\Pages\ListRecords;

class ListFeedbacks extends ListRecords
{
    protected static string $resource = FeedbackResource::class;
}
