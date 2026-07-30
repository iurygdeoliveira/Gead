<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Traits\Filament\HasBackButtonAction;

use App\Traits\Filament\HasFeedbackAction;

use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\On;

class ViewTeacher extends ViewRecord
{
    use HasBackButtonAction;

    use HasFeedbackAction;

    protected static string $resource = TeacherResource::class;

    #[On('refreshTeacherInfolist')]
    public function refreshInfolist(): void
    {
        unset($this->getRecord()->cached_taught_disciplines);
    }

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
