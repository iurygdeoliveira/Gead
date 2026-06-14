<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    protected $fillable = ['discipline_id', 'teacher_id', 'academic_term_id', 'code', 'name', 'team_id'];
    public function discipline() { return $this->belongsTo(Discipline::class); }
    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function academicTerm() { return $this->belongsTo(AcademicTerm::class); }
    public function classEnrollments() { return $this->hasMany(ClassEnrollment::class); }
    public function team() { return $this->belongsTo(Team::class); }

}
