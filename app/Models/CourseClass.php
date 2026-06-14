<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    protected $fillable = ['course_id', 'entry_period', 'academic_term_id', 'code', 'name', 'team_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function classEnrollments()
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class, 'course_class_disciplines')
            ->withPivot('teacher_id')
            ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id', 'course_id');
    }

}
