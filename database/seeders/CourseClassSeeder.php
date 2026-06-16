<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Team;
use Illuminate\Database\Seeder;

class CourseClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $team = Team::where('cnpj', '03.131.702/0001-33')->first();
        if (! $team) {
            $team = Team::create([
                'name' => 'Campus Araguaína',
                'slug' => 'campus-araguaina',
                'cnpj' => '03.131.702/0001-33',
                'is_active' => true,
                'is_personal' => false,
            ]);
        }

        // Create CourseClasses (Turmas) based on unique combinations of course and entry period in enrollments
        $combinations = Enrollment::select('course_id', 'entry_period')
            ->whereNotNull('entry_period')
            ->distinct()
            ->get();

        foreach ($combinations as $combo) {
            $course = Course::find($combo->course_id);
            if ($course) {
                $code = "{$course->code}-{$combo->entry_period}";
                $name = "{$course->name} - {$combo->entry_period}";

                CourseClass::updateOrCreate(
                    [
                        'course_id' => $combo->course_id,
                        'entry_period' => $combo->entry_period,
                        'team_id' => $team->id,
                    ],
                    [
                        'code' => $code,
                        'name' => $name,
                    ]
                );
            }
        }
    }
}
