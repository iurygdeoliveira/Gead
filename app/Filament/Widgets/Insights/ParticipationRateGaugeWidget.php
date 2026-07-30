<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Insights;

use App\Services\EvaluationAnalyticsService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ParticipationRateGaugeWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'participationRateGaugeWidget';

    protected static ?string $heading = 'Taxa de Participação na Avaliação Docente';

    public ?int $teamId = null;

    protected function getOptions(): array
    {
        $service = app(EvaluationAnalyticsService::class);
        $stats = $service->getParticipationRates($this->teamId);

        $courseNames = array_column($stats['courses'], 'course_name');
        $completedSeries = array_column($stats['courses'], 'completed');
        $expectedSeries = array_column($stats['courses'], 'total');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 380,
                'fontFamily' => 'Poppins, sans-serif',
                'stacked' => true,
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => true,
                    'borderRadius' => 4,
                ],
            ],
            'series' => [
                [
                    'name' => 'Avaliações Realizadas',
                    'data' => $completedSeries,
                ],
                [
                    'name' => 'Pendentes',
                    'data' => array_map(fn ($total, $done) => max(0, $total - $done), $expectedSeries, $completedSeries),
                ],
            ],
            'xaxis' => [
                'categories' => $courseNames,
            ],
            'colors' => ['#84cc16', '#e7010a'],
            'legend' => [
                'position' => 'top',
            ],
            'title' => [
                'text' => "Taxa Global do Campus: {$stats['global_rate']}%",
                'align' => 'center',
                'style' => [
                    'fontSize' => '16px',
                    'fontWeight' => 'bold',
                    'color' => '#84cc16',
                ],
            ],
        ];
    }
}
