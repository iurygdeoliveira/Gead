<?php

declare(strict_types=1);

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\Evaluation;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\HasFeedbackAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEvaluation extends ViewRecord
{
    use HasBackButtonAction;
    use HasFeedbackAction;

    protected static string $resource = EvaluationResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
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
