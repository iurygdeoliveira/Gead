<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Insights;

use App\Services\EvaluationAnalyticsService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TemporalEvolutionLineWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'temporalEvolutionLineWidget';

    protected static ?string $heading = 'Evolução Temporal dos Scores por Semestre Letivo';

    public ?int $teamId = null;

    public ?int $courseId = null;

    #[\Override]
    protected function getOptions(): array
    {
        $service = resolve(EvaluationAnalyticsService::class);
        $data = $service->getTemporalEvolution($this->teamId, $this->courseId);

        $series = [];
        $colors = [
            'Planejamento' => '#3b82f6',
            'Postura' => '#8b5cf6',
            'Assiduidade' => '#10b981',
            'Pontualidade' => '#f59e0b',
            'Execução' => '#ef4444',
            'Avaliação' => '#06b6d4',
        ];

        foreach ($data['series'] as $name => $values) {
            $series[] = [
                'name' => $name,
                'data' => $values,
            ];
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 380,
                'fontFamily' => 'Poppins, sans-serif',
                'zoom' => ['enabled' => false],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 3,
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $data['terms'],
            ],
            'yaxis' => [
                'min' => 0,
                'max' => 10,
                'tickAmount' => 5,
            ],
            'colors' => array_values($colors),
            'markers' => [
                'size' => 5,
            ],
            'legend' => [
                'position' => 'bottom',
            ],
        ];
    }
}
