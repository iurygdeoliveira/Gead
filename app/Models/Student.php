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
            
            $data[] = [
                'discipline_name' => $discipline ? $discipline->name : '-',
                'teacher_name' => $ccd->teacher ? $ccd->teacher->name : 'Sem Professor',
                'teaching_period' => '2026.1', // Simplificado
                'status' => $evaluation ? 'Realizada' : 'Pendente',
            ];
        }

        return $data;
    }
}
