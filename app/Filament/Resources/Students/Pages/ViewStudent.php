<?php

namespace App\Filament\Resources\Students\Pages;

use App\Traits\Filament\HasBackButtonAction;

use App\Traits\Filament\HasFeedbackAction;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewStudent extends ViewRecord
{
    use HasBackButtonAction;

    use HasFeedbackAction;

    protected static string $resource = StudentResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
