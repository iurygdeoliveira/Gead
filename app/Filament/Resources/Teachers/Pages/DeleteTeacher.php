<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Traits\Filament\HasFeedbackAction;

use App\Filament\Resources\Teachers\TeacherResource;
use App\Models\Teacher;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class DeleteTeacher extends ViewRecord
{
    use HasFeedbackAction;

    use HasBackButtonAction;

    protected static string $resource = TeacherResource::class;

    #[\Override]
    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->authorize('delete', $this->getRecord());
    }

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            Action::make('delete')
                ->label('Confirmar Exclusão')
                ->color('danger')
                ->icon(Heroicon::OutlinedTrash)
                ->requiresConfirmation()
                ->modalHeading('Confirmar Exclusão Permanente')
                ->modalDescription('Tem certeza de que deseja excluir permanentemente este professor? Esta ação não pode ser desfeita.')
                ->modalSubmitActionLabel('Sim, Excluir')
                ->modalCancelActionLabel('Cancelar')
                ->visible(fn (): bool => Filament::auth()->user()?->can('delete', $this->getRecord()) ?? false)
                ->action(function (): void {
                    $this->authorize('delete', $this->getRecord());
                    $this->getRecord()->delete();

                    $this->redirect($this->getResource()::getUrl('index'));
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

        if (! $record instanceof Teacher) {
            return 'Excluir Professor';
        }

        return 'Excluir: '.($record->name);
    }
}
