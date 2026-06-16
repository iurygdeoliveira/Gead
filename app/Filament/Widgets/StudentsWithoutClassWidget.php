<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\Widget;

class StudentsWithoutClassWidget extends Widget
{
    protected string $view = 'filament.widgets.students-without-class-widget';

    public static function canView(): bool
    {
        return filament()->getCurrentPanel()?->getId() === 'manager';
    }

    public function getCount(): int
    {
        $teamId = filament()->getTenant()?->id;

        if (! $teamId) {
            return 0;
        }

        return Student::where('team_id', $teamId)
            ->whereDoesntHave('enrollments.classEnrollments')
            ->count();
    }
}
