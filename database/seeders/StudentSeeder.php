<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Team;
use App\Models\ClassEnrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
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

        $csvPath = database_path('seeders/dados de seed/Base_de_alunos_2026.1.csv');

        if (! file_exists($csvPath)) {
            $this->command->error("Arquivo CSV não encontrado: {$csvPath}");
            return;
        }

        $coursesCache = Course::where('team_id', $team->id)->get()->keyBy('name');
        $courseClassesCache = CourseClass::where('team_id', $team->id)->get()->keyBy('code');

        DB::transaction(function () use ($csvPath, $team, $coursesCache, $courseClassesCache) {
            $file = fopen($csvPath, 'r');
            $isHeader = true;

            while (($row = fgetcsv($file, 1000, ',')) !== false) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                $registrationNumber = trim($row[1] ?? '');
                $name = trim($row[2] ?? '');
                $courseName = trim($row[5] ?? '');
                $classCode = trim($row[8] ?? '');
                $academicEmail = trim($row[9] ?? '');

                if (empty($name) || empty($registrationNumber) || empty($courseName)) {
                    continue;
                }

                $course = $coursesCache->get($courseName);
                if (!$course) {
                    continue;
                }

                $emailToSave = !empty($academicEmail) ? $academicEmail : null;

                $student = Student::updateOrCreate(
                    [
                        'name' => $name,
                        'team_id' => $team->id,
                    ],
                    [
                        'email' => $emailToSave,
                        'user_id' => null,
                    ]
                );

                $enrollment = Enrollment::updateOrCreate(
                    [
                        'registration_number' => $registrationNumber,
                    ],
                    [
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'entry_period' => null,
                    ]
                );

                if (!empty($classCode) && $classCode !== '-') {
                    $courseClass = $courseClassesCache->get($classCode);
                    if ($courseClass) {
                        ClassEnrollment::updateOrCreate(
                            [
                                'enrollment_id' => $enrollment->id,
                                'course_class_id' => $courseClass->id,
                            ]
                        );
                    }
                }
            }

            fclose($file);
        });
    }
}
