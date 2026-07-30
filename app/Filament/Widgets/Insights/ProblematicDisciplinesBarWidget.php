<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Insights;

use App\Services\EvaluationAnalyticsService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ProblematicDisciplinesBarWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'problematicDisciplinesBarWidget';

    protected static ?string $heading = 'Disciplinas com Menores Índices de Avaliação Docente';

    public ?int $teamId = null;

    public ?int $courseId = null;

    public ?int $courseClassId = null;

    protected function getOptions(): array
    {
        $service = app(EvaluationAnalyticsService::class);
        $disciplines = $service->getProblematicDisciplines($this->teamId, $this->courseId, $this->courseClassId, 10);

        $names = array_column($disciplines, 'discipline_name');
        $scores = array_column($disciplines, 'avg_score');

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 450,
                'fontFamily' => 'Poppins, sans-serif',
            ],
            'plotOptions' => [
                'bar' => [
                    'horizontal' => true,
                    'borderRadius' => 4,
                    'distributed' => true,
                    'barHeight' => '65%',
                ],
            ],
            'series' => [
                [
                    'name' => 'Score Médio Geral',
                    'data' => $scores,
                ],
            ],
            'xaxis' => [
                'categories' => $names,
                'min' => 0,
                'max' => 10,
            ],
            'yaxis' => [
                'labels' => [
                    'maxWidth' => 350,
                    'style' => [
                        'fontSize' => '12px',
                        'fontWeight' => 600,
                    ],
                ],
            ],
            'grid' => [
                'padding' => [
                    'left' => 20,
                ],
            ],
            'colors' => array_map(function ($score) {
                if ($score < 5.0) {
                    return '#e7010a';
                }
                if ($score < 7.0) {
                    return '#f59e0b';
                }

                return '#84cc16';
            }, $scores),
            'legend' => [
                'show' => false,
            ],
        ];
    }
}
