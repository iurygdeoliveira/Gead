<?php

declare(strict_types=1);

namespace App\Filament\Resources\Feedback\Pages;

use App\Filament\Resources\Feedback\FeedbackResource;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewFeedback extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = FeedbackResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            Action::make('delete')
                ->label(__('Excluir'))
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->url(fn (): string => FeedbackResource::getUrl('delete', ['record' => $this->getRecord()])),
        ];
    }
}
