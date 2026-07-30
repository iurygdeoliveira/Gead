<?php

declare(strict_types=1);

namespace App\Filament\Resources\Teams\Pages;

use App\Traits\Filament\HasFeedbackAction;

use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\Teams\Widgets\TeamStats;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeams extends ListRecords
{
    use HasFeedbackAction;

    protected static string $resource = TeamResource::class;

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
            TeamStats::class,
        ];
    }
}
