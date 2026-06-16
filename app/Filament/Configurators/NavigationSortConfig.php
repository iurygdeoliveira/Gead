<?php

declare(strict_types=1);

namespace App\Filament\Configurators;

use App\Filament\Pages\LoginAuditPage;
use App\Filament\Resources\CourseClasses\CourseClassResource;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\Evaluations\EvaluationResource;
use App\Filament\Resources\Feedback\FeedbackResource;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Security\SecurityEventResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Teachers\TeacherResource;
use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\Users\UserResource;

class NavigationSortConfig
{
    public static function getSortOrder(string $class): ?int
    {
        $order = [
            // Cadastros / Recursos Principais
            TeacherResource::class => 1,
            EvaluationResource::class => 2,
            StudentResource::class => 3,
            CourseResource::class => 4,
            CourseClassResource::class => 5,
            MediaResource::class => 6,

            // Administração / Segurança
            UserResource::class => 10,
            SecurityEventResource::class => 11,
            LoginAuditPage::class => 12,

            // Sistema
            TeamResource::class => 20,
            FeedbackResource::class => 21,
        ];

        // Busca exata pela classe
        if (isset($order[$class])) {
            return $order[$class];
        }

        if (! class_exists($class)) {
            return null;
        }

        // Fallback: busca pela classe pai (herança)
        foreach ($order as $registeredClass => $sort) {
            if (is_subclass_of($class, $registeredClass)) {
                return $sort;
            }
        }

        return null;
    }
}
