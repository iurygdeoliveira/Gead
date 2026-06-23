<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Team;
use Filament\Widgets\Widget;

class StudentsWithoutClassWidget extends Widget
{
    protected string $view = 'filament.widgets.students-without-class-widget';

    #[\Override]
    public static function canView(): bool
    {
        return in_array(filament()->getCurrentPanel()?->getId(), ['manager', 'tae']);
    }

    public function getCount(): int
    {
        /** @var Team|null $tenant */
        $tenant = filament()->getTenant();
        $teamId = $tenant?->id;

        if (! $teamId) {
            return 0;
        }

        return Student::where('team_id', $teamId)
            ->whereDoesntHave('enrollments.classEnrollments')
            ->count();
    }
}
