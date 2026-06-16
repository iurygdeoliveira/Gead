<?php

namespace App\Filament\Resources\Evaluations\Pages;

use App\Filament\Resources\Evaluations\EvaluationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => \Filament\Schemas\Components\Tabs\Tab::make('Todos'),
            'farmacia' => \Filament\Schemas\Components\Tabs\Tab::make('Farmácia')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where('name', 'ilike', '%Farmácia%'))),
            'enfermagem' => \Filament\Schemas\Components\Tabs\Tab::make('Enfermagem')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where('name', 'ilike', '%Enfermagem%'))),
            'informatica' => \Filament\Schemas\Components\Tabs\Tab::make('Informática')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where(fn($sub) => $sub->where('name', 'ilike', '%Informática%')->orWhere('name', 'ilike', '%Informatica%')))),
            'gpi' => \Filament\Schemas\Components\Tabs\Tab::make('GPI')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where(fn($sub) => $sub->where('name', 'ilike', '%GPI%')->orWhere('name', 'ilike', '%Gestão da Produção Industrial%')))),
            'tads' => \Filament\Schemas\Components\Tabs\Tab::make('TADS')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where(fn($sub) => $sub->where('name', 'ilike', '%TADS%')->orWhere('name', 'ilike', '%Análise e Desenvolvimento de Sistema%')))),
            'pcp' => \Filament\Schemas\Components\Tabs\Tab::make('PCP')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where(fn($sub) => $sub->where('name', 'ilike', '%PCP%')->orWhere('name', 'ilike', '%Planejamento e controle da produção%')))),
            'eja' => \Filament\Schemas\Components\Tabs\Tab::make('EJA')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where('name', 'ilike', '%Operador de Computador%'))),
            'analises_clinicas' => \Filament\Schemas\Components\Tabs\Tab::make('Análises Clínicas')
                ->query(fn ($query) => $query->whereHas('courseClassDiscipline.courseClass.course', fn ($q) => $q->where(fn($sub) => $sub->where('name', 'ilike', '%Análises Clínicas%')->orWhere('name', 'ilike', '%Analises Clinicas%')))),
        ];
    }
}
