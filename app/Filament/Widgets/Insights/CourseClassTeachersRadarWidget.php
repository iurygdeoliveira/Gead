<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Insights;

use App\Services\EvaluationAnalyticsService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class CourseClassTeachersRadarWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'courseClassTeachersRadarWidget';

    protected static ?string $heading = 'Perfil Consolidado do Curso (Média Geral dos Professores)';

    public ?int $teamId = null;

    public ?int $courseId = null;

    #[\Override]
    protected function getOptions(): array
    {
        if (! $this->courseId) {
            return [
                'chart' => ['type' => 'radar', 'height' => 380, 'fontFamily' => 'Poppins, sans-serif'],
                'series' => [],
                'title' => ['text' => 'Selecione um curso no filtro acima', 'align' => 'center'],
            ];
        }

        $service = resolve(EvaluationAnalyticsService::class);
        $data = $service->getCourseProfileRadar($this->courseId, $this->teamId);

        $dimensions = $data['dimensions'];
        $series = [];

        foreach ($data['classes'] as $className => $scores) {
            $series[] = [
                'name' => $className,
                'data' => array_values($scores),
            ];
        }

        return [
            'chart' => [
                'type' => 'radar',
                'height' => 480,
                'fontFamily' => 'Poppins, sans-serif',
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $dimensions,
            ],
            'stroke' => [
                'width' => 7,
            ],
            'fill' => [
                'opacity' => 0,
            ],
            'colors' => ['#84cc16', '#e7010a', '#2563eb', '#d97706', '#9333ea', '#db2777', '#0284c7', '#ea580c'],
            'markers' => [
                'size' => 4,
            ],
            'legend' => [
                'position' => 'bottom',
            ],
            'yaxis' => [
                'min' => 0,
                'max' => 10,
            ],
        ];
    }
}
