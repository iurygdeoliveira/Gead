<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Insights;

use App\Services\EvaluationAnalyticsService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class DimensionCorrelationHeatmapWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'dimensionCorrelationHeatmapWidget';

    protected static ?string $heading = 'Matriz de Correlação Estatística entre as 6 Dimensões (Pearson)';

    public ?int $teamId = null;

    public ?int $courseId = null;

    #[\Override]
    protected function getOptions(): array
    {
        $service = resolve(EvaluationAnalyticsService::class);
        $result = $service->getDimensionCorrelationMatrix($this->teamId, $this->courseId);

        $dimensions = $result['dimensions'];
        $matrix = $result['matrix'];

        $series = [];
        foreach ($dimensions as $dimY) {
            $dataRow = [];
            foreach ($dimensions as $dimX) {
                $val = $matrix[$dimY][$dimX] ?? 0.0;
                $dataRow[] = [
                    'x' => $dimX,
                    'y' => $val,
                ];
            }
            $series[] = [
                'name' => $dimY,
                'data' => $dataRow,
            ];
        }

        return [
            'chart' => [
                'type' => 'heatmap',
                'height' => 380,
                'fontFamily' => 'Poppins, sans-serif',
                'toolbar' => ['show' => false],
            ],
            'series' => $series,
            'plotOptions' => [
                'heatmap' => [
                    'shadeIntensity' => 0.5,
                    'radius' => 4,
                    'useFillColorAsStroke' => true,
                    'colorScale' => [
                        'ranges' => [
                            ['from' => -1.0, 'to' => 0.39, 'name' => 'Fraca / Desconexão (< 0.40)', 'color' => '#ef4444'],
                            ['from' => 0.40, 'to' => 0.74, 'name' => 'Moderada (0.40 - 0.74)', 'color' => '#f59e0b'],
                            ['from' => 0.75, 'to' => 1.0, 'name' => 'Forte / Coerente (≥ 0.75)', 'color' => '#10b981'],
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
                    'formatter' => 'function (val) { return "r = " + val; }',
                ],
            ],
        ];
    }
}
