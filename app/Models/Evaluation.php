<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = ['class_enrollment_id', 'course_class_discipline_id', 'planning_score', 'posture_score', 'attendance_score', 'punctuality_score', 'execution_score', 'assessment_score', 'comments', 'team_id'];
    public function classEnrollment() { return $this->belongsTo(ClassEnrollment::class); }
    public function courseClassDiscipline() { return $this->belongsTo(CourseClassDiscipline::class); }
    public function team() { return $this->belongsTo(Team::class); }

}
