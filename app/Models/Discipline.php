<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    protected $fillable = ['course_id', 'name', 'code', 'period'];
    public function course() { return $this->belongsTo(Course::class); }
    public function courseClasses() { return $this->hasMany(CourseClass::class); }

}
