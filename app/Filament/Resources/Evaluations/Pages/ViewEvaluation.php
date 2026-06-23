<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\Evaluation;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewEvaluation extends ViewRecord
{
    protected static string $resource = EvaluationResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Voltar')
                ->icon(Heroicon::ArrowLeft)
                ->color('gray')
                ->url(fn () => static::getResource()::getUrl('index')),
            EditAction::make(),
        ];
    }

    #[\Override]
    public function getTitle(): string
    {
        /** @var Evaluation $record */
        $record = $this->getRecord();

        $teacherName = $record->courseClassDiscipline?->teacher->name ?? 'Professor Desconhecido';

        return 'Visualizar avaliação de '.$teacherName;
    }
}
