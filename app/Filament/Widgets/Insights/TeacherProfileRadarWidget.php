<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Insights;

use App\Models\Teacher;
use App\Services\EvaluationAnalyticsService;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TeacherProfileRadarWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'teacherProfileRadarWidget';

    protected static ?string $heading = 'Perfil de Desempenho Individual do Docente';

    public ?int $teamId = null;

    public ?int $teacherId = null;

    #[\Override]
    protected function getOptions(): array
    {
        $teacherId = $this->teacherId ?: Teacher::first()?->id;

        if (! $teacherId) {
            return [
                'chart' => ['type' => 'radar', 'height' => 380, 'fontFamily' => 'Poppins, sans-serif'],
                'series' => [],
                'title' => ['text' => 'Selecione um professor no filtro acima', 'align' => 'center'],
            ];
        }

        $service = resolve(EvaluationAnalyticsService::class);
        $profile = $service->getTeacherProfile($teacherId, $this->teamId);

        $dimensions = array_keys($profile['teacher_scores']);
        $teacherData = array_values($profile['teacher_scores']);

        return [
            'chart' => [
                'type' => 'radar',
                'height' => 480,
                'fontFamily' => 'Poppins, sans-serif',
            ],
            'series' => [
                [
                    'name' => 'Professor Selecionado',
                    'data' => $teacherData,
                ],
            ],
            'xaxis' => [
                'categories' => $dimensions,
            ],
            'stroke' => [
                'width' => 7,
            ],
            'fill' => [
                'opacity' => 0,
            ],
            'colors' => ['#84cc16'],
            'markers' => [
                'size' => 4,
            ],
            'yaxis' => [
                'min' => 0,
                'max' => 10,
            ],
        ];
    }
}
