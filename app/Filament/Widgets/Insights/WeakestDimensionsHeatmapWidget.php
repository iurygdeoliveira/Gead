<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Insights;

use App\Services\EvaluationAnalyticsService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class WeakestDimensionsHeatmapWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'weakestDimensionsHeatmapWidget';

    protected static ?string $heading = 'Dimensões Mais Fracas do Campus Araguaína';

    public ?int $teamId = null;

    #[\Override]
    protected function getOptions(): array
    {
        $service = resolve(EvaluationAnalyticsService::class);
        $data = $service->getWeakestDimensions($this->teamId);

        $seriesData = [];
        foreach ($data as $dimension => $score) {
            $seriesData[] = [
                'x' => $dimension,
                'y' => $score,
            ];
        }

        return [
            'chart' => [
                'type' => 'heatmap',
                'height' => 320,
                'fontFamily' => 'Poppins, sans-serif',
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'Score Médio',
                    'data' => $seriesData,
                ],
            ],
            'legend' => [
                'show' => true,
                'position' => 'top',
                'horizontalAlign' => 'center',
                'offsetY' => -5,
                'itemMargin' => [
                    'horizontal' => 10,
                    'vertical' => 10,
                ],
            ],
            'grid' => [
                'padding' => [
                    'top' => 15,
                    'bottom' => 15,
                ],
            ],
            'plotOptions' => [
                'heatmap' => [
                    'shadeIntensity' => 0.5,
                    'radius' => 6,
                    'useFillColorAsStroke' => true,
                    'colorScale' => [
                        'ranges' => [
                            ['from' => 0, 'to' => 4.99, 'name' => 'Crítico (< 5.0)', 'color' => '#e7010a'],
                            ['from' => 5.0, 'to' => 6.99, 'name' => 'Atenção (5.0 - 6.9)', 'color' => '#f59e0b'],
                            ['from' => 7.0, 'to' => 10.0, 'name' => 'Satisfatório (≥ 7.0)', 'color' => '#84cc16'],
                        ],
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => ['colors' => ['#ffffff']],
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function (val) { return val + " / 10"; }',
                ],
            ],
        ];
    }
}
