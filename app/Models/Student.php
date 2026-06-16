<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'email',
        'team_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments');
    }

    public function getEvaluationsStatusAttribute()
    {
        $disciplines = \App\Models\CourseClassDiscipline::with(['courseClass.course', 'discipline', 'teacher'])
            ->whereHas('courseClass.classEnrollments.enrollment', function($q) {
                $q->where('student_id', $this->id);
            })->get();

        $data = [];
        foreach ($disciplines as $ccd) {
            $evaluation = \App\Models\Evaluation::where('course_class_discipline_id', $ccd->id)
                ->whereHas('classEnrollment.enrollment', function($q) {
                    $q->where('student_id', $this->id);
                })
                ->whereNotNull('planning_score')
                ->exists();

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
                'teacher_name' => $ccd->teacher ? $ccd->teacher->name : 'Sem Professor',
                'teaching_period' => $teachingPeriod,
                'status' => $evaluation ? 'Realizada' : 'Pendente',
            ];
        }

        return $data;
    }
}
