<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$student = App\Models\Student::first();
if (!$student) die("No student\n");

$disciplines = App\Models\CourseClassDiscipline::with(['courseClass.course', 'discipline'])
    ->whereHas('courseClass.classEnrollments.enrollment', function($q) use ($student) {
        $q->where('student_id', $student->id);
    })->get();

$data = [];
foreach ($disciplines as $ccd) {
    // Find if evaluation exists
    $evaluation = App\Models\Evaluation::where('course_class_discipline_id', $ccd->id)
        ->whereHas('classEnrollment.enrollment', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->exists();

    // period
    $courseClass = $ccd->courseClass;
    $discipline = $ccd->discipline;
    
    $teachingPeriod = '-';
    if ($courseClass && $discipline && !empty($discipline->period) && is_numeric($discipline->period)) {
        $entryPeriod = $courseClass->entry_period;
        $isAnnual = $courseClass->course ? str_contains(mb_strtolower($courseClass->course->name, 'UTF-8'), 'integrado') : false;
        $disciplinePeriod = (int) $discipline->period;

        $normalized = str_replace('/', '.', $entryPeriod);
        $parts = explode('.', $normalized);
        $year = (int) $parts[0];
        $sem = (int) ($parts[1] ?? 1);

        if ($isAnnual) {
            $teachingYear = $year + $disciplinePeriod - 1;
            $teachingPeriod = "{$teachingYear}.1";
        } else {
            $semestersToAdd = $disciplinePeriod - 1;
            for ($i = 0; $i < $semestersToAdd; $i++) {
                if ($sem === 2) {
                    $year++;
                    $sem = 1;
                } else {
                    $sem = 2;
                }
            }
            $teachingPeriod = "{$year}.{$sem}";
        }
    } else {
        $teachingPeriod = $courseClass ? $courseClass->entry_period : '-';
    }

    $data[] = [
        'discipline_name' => $discipline ? $discipline->name : '-',
        'teaching_period' => $teachingPeriod,
        'status' => $evaluation ? 'Realizada' : 'Pendente',
    ];
}

echo json_encode($data, JSON_PRETTY_PRINT);
