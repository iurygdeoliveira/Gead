<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseClassDiscipline extends Pivot
{
    protected $table = 'course_class_disciplines';

    public function courseClass()
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
