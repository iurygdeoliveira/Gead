<?php

declare(strict_types=1);

namespace App\Filament\Resources\Feedback\Pages;

use App\Traits\Filament\HasFeedbackAction;

use App\Filament\Resources\Feedback\FeedbackResource;
use App\Models\Feedback;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class DeleteFeedback extends ViewRecord
{
    use HasFeedbackAction;

    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = FeedbackResource::class;

    protected string $view = 'filament.resources.feedback.pages.delete-feedback';

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            Action::make('delete')
                ->label(__('Confirmar exclusão'))
                ->color('danger')
                ->icon(Heroicon::OutlinedTrash)
                ->requiresConfirmation()
                ->modalHeading(__('Confirmar exclusão permanente'))
                ->modalDescription(__('Tem certeza de que deseja excluir este feedback? Esta ação não pode ser desfeita.'))
                ->modalSubmitActionLabel(__('Sim, excluir'))
                ->modalCancelActionLabel(__('Cancelar'))
                ->action(function (): void {
                    $record = $this->getRecord();

                    if (! $record instanceof Feedback) {
                        return;
                    }

                    $record->delete();
                    $this->notifySuccess(__('Feedback excluído com sucesso'));
                    $this->redirect(FeedbackResource::getUrl('index'));
                }),
        ];
    }

    #[\Override]
    public function getTitle(): string|Htmlable
    {
        return $this->resolveDynamicTitle();
    }

    protected function resolveDynamicTitle(): string
    {
        $record = $this->getRecord();

        if (! $record instanceof Feedback) {
            return __('Excluir feedback');
        }

        $title = $record->page_title ?? $record->page_url ?? (string) $record->getKey();

        return __('Excluir: :title', ['title' => $title]);
    }
}
