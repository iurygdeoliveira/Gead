<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassEnrollment extends Model
{
    protected $fillable = ['enrollment_id', 'course_class_id'];
    public function enrollment() { return $this->belongsTo(Enrollment::class); }
    public function courseClass() { return $this->belongsTo(CourseClass::class); }
    public function evaluation() { return $this->hasOne(Evaluation::class); }

}
