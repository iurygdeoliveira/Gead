<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Insights;

use App\Services\EvaluationAnalyticsService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class GradeDistributionHistogramWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'gradeDistributionHistogramWidget';

    protected static ?string $heading = 'Distribuição das Notas (0 a 10) - Detecção de Viés';

    public ?int $teamId = null;

    public ?int $courseId = null;

    protected function getOptions(): array
    {
        $service = app(EvaluationAnalyticsService::class);
        $distribution = $service->getGradeDistribution($this->teamId, $this->courseId);

        $categories = array_map(fn ($i) => "Nota {$i}", range(0, 10));
        $data = array_values($distribution);

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 380,
                'fontFamily' => 'Poppins, sans-serif',
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 4,
                    'columnWidth' => '60%',
                    'distributed' => true,
                ],
            ],
            'series' => [
                [
                    'name' => 'Frequência de Avaliações',
                    'data' => $data,
                ],
            ],
            'xaxis' => [
                'categories' => $categories,
            ],
            'yaxis' => [
                'title' => ['text' => 'Quantidade de Respostas'],
            ],
            'colors' => [
                '#ef4444', '#ef4444', '#ef4444', '#f97316', '#f59e0b',
                '#eab308', '#84cc16', '#22c55e', '#10b981', '#059669', '#047857',
            ],
            'legend' => [
                'show' => false,
            ],
        ];
    }
}
