<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Models\Evaluation;
use App\Traits\Filament\HasFeedbackAction;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEvaluations extends ListRecords
{
    use HasFeedbackAction;

    protected static string $resource = EvaluationResource::class;

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    #[\Override]
    public function getTabs(): array
    {
        $teamId = Filament::getTenant()?->getKey();

        $baseQuery = Evaluation::query()
            ->where('team_id', $teamId)
            ->whereIn('id', function ($q): void {
                $q->selectRaw('MAX(id)')
                    ->from('evaluations')
                    ->groupBy('course_class_discipline_id');
            });

        return [
            'farmacia' => Tab::make('Farm.')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where('name', 'ilike', '%Farmácia%')))
                ->badge((clone $baseQuery)->whereHas('courseClassDiscipline.courseClass.course', fn (Builder $q) => $q->where('name', 'ilike', '%Farmácia%'))->count()),
            'enfermagem' => Tab::make('Enfer.')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where('name', 'ilike', '%Enfermagem%')))
                ->badge((clone $baseQuery)->whereHas('courseClassDiscipline.courseClass.course', fn (Builder $q) => $q->where('name', 'ilike', '%Enfermagem%'))->count()),
            'informatica' => Tab::make('Infor.')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'ilike', '%Informática%')->orWhere('name', 'ilike', '%Informatica%'))))
                ->badge((clone $baseQuery)->whereHas('courseClassDiscipline.courseClass.course', fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->where('name', 'ilike', '%Informática%')->orWhere('name', 'ilike', '%Informatica%')))->count()),
            'gpi' => Tab::make('GPI')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'ilike', '%GPI%')->orWhere('name', 'ilike', '%Gestão da Produção Industrial%'))))
                ->badge((clone $baseQuery)->whereHas('courseClassDiscipline.courseClass.course', fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->where('name', 'ilike', '%GPI%')->orWhere('name', 'ilike', '%Gestão da Produção Industrial%')))->count()),
            'tads' => Tab::make('TADS')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'ilike', '%TADS%')->orWhere('name', 'ilike', '%Análise e Desenvolvimento de Sistema%'))))
                ->badge((clone $baseQuery)->whereHas('courseClassDiscipline.courseClass.course', fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->where('name', 'ilike', '%TADS%')->orWhere('name', 'ilike', '%Análise e Desenvolvimento de Sistema%')))->count()),
            'pcp' => Tab::make('PCP')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'ilike', '%PCP%')->orWhere('name', 'ilike', '%Planejamento e controle da produção%')->orWhere('name', 'ilike', '%Planejamento e Controle da Produção%'))))
                ->badge((clone $baseQuery)->whereHas('courseClassDiscipline.courseClass.course', fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->where('name', 'ilike', '%PCP%')->orWhere('name', 'ilike', '%Planejamento e controle da produção%')->orWhere('name', 'ilike', '%Planejamento e Controle da Produção%')))->count()),
            'eja' => Tab::make('EJA')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where('name', 'ilike', '%Operador de Computador%')))
                ->badge((clone $baseQuery)->whereHas('courseClassDiscipline.courseClass.course', fn (Builder $q) => $q->where('name', 'ilike', '%Operador de Computador%'))->count()),
            'analises_clinicas' => Tab::make('Análises')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'ilike', '%Análises Clínicas%')->orWhere('name', 'ilike', '%Analises Clinicas%'))))
                ->badge((clone $baseQuery)->whereHas('courseClassDiscipline.courseClass.course', fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->where('name', 'ilike', '%Análises Clínicas%')->orWhere('name', 'ilike', '%Analises Clinicas%')))->count()),
        ];
    }
}
