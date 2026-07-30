<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Insights;

use App\Services\EvaluationAnalyticsService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class DispensedStudentsDonutWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'dispensedStudentsDonutWidget';

    protected static ?string $heading = 'Proporção de Alunos Ativos vs. Dispensados da Avaliação';

    public ?int $teamId = null;

    protected function getOptions(): array
    {
        $service = app(EvaluationAnalyticsService::class);
        $stats = $service->getDispensedStudentsStats($this->teamId);

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 320,
                'fontFamily' => 'Poppins, sans-serif',
            ],
            'labels' => ['Alunos Ativos (Avaliam)', 'Alunos Dispensados'],
            'series' => [$stats['active'], $stats['dispensed']],
            'colors' => ['#84cc16', '#f59e0b'],
            'legend' => [
                'position' => 'bottom',
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'labels' => [
                            'show' => true,
                            'total' => [
                                'show' => true,
                                'label' => '% Dispensados',
                                'formatter' => 'function (w) { return "'.$stats['percentage'].'%"; }',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
