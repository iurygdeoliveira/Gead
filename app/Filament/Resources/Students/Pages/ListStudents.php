<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Students\Widgets\StudentsStats;
use App\Models\Student;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

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
            StudentsStats::class,
        ];
    }

    public function getTabs(): array
    {
        $teamId = Filament::getTenant()?->id;

        $baseQuery = Student::query()
            ->when($teamId, fn ($q) => $q->where('team_id', $teamId));

        return [
            'sem_turma' => Tab::make('Sem Turma')
                ->modifyQueryUsing(fn($query) => $query->whereDoesntHave('enrollments.classEnrollments'))
                ->badge((clone $baseQuery)->whereDoesntHave('enrollments.classEnrollments')->count())
                ->badgeColor('danger'),
            'farmacia' => Tab::make('Farm.')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where('name', 'like', '%Farmácia%')))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn ($q) => $q->where('name', 'like', '%Farmácia%'))->count()),
            'enfermagem' => Tab::make('Enfer.')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where('name', 'like', '%Enfermagem%')))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn ($q) => $q->where('name', 'like', '%Enfermagem%'))->count()),
            'informatica' => Tab::make('Infor.')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%Informática%')->orWhere('name', 'like', '%Informatica%'))))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%Informática%')->orWhere('name', 'like', '%Informatica%')))->count()),
            'gpi' => Tab::make('GPI')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%GPI%')->orWhere('name', 'like', '%Gestão da Produção Industrial%'))))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%GPI%')->orWhere('name', 'like', '%Gestão da Produção Industrial%')))->count()),
            'tads' => Tab::make('TADS')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%TADS%')->orWhere('name', 'like', '%Análise e Desenvolvimento de Sistema%'))))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%TADS%')->orWhere('name', 'like', '%Análise e Desenvolvimento de Sistema%')))->count()),
            'pcp' => Tab::make('PCP')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%PCP%')->orWhere('name', 'like', '%Planejamento e controle da produção%')->orWhere('name', 'like', '%Planejamento e Controle da Produção%'))))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%PCP%')->orWhere('name', 'like', '%Planejamento e controle da produção%')->orWhere('name', 'like', '%Planejamento e Controle da Produção%')))->count()),
            'eja' => Tab::make('EJA')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where('name', 'like', '%Operador de Computador%')))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn ($q) => $q->where('name', 'like', '%Operador de Computador%'))->count()),
            'analises_clinicas' => Tab::make('Análises')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%Análises Clínicas%')->orWhere('name', 'like', '%Analises Clinicas%'))))
                ->badge((clone $baseQuery)->whereHas('enrollments.course', fn ($q) => $q->where(fn ($sub) => $sub->where('name', 'like', '%Análises Clínicas%')->orWhere('name', 'like', '%Analises Clinicas%')))->count()),
           
        ];
    }
}
