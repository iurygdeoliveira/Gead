<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    protected $fillable = ['name', 'start_date', 'end_date'];
    public function courseClasses() { return $this->hasMany(CourseClass::class); }

}
