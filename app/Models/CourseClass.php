<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseClass extends Model
{
    protected $fillable = ['course_id', 'entry_period', 'academic_term_id', 'code', 'name', 'team_id'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function classEnrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'course_class_disciplines')
            ->withPivot('teacher_id')
            ->withTimestamps();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'course_id', 'course_id');
    }
}
