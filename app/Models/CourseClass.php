<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['uuid', 'course_id', 'entry_period', 'academic_term_id', 'code', 'name', 'team_id'])]
class CourseClass extends Model
{
    use UuidTrait;

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return BelongsTo<AcademicTerm, $this>
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * @return HasMany<ClassEnrollment, $this>
     */
    public function classEnrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class);
    }

    /**
     * @return BelongsToMany<Discipline, $this, Pivot>
     */
    public function disciplines(): BelongsToMany
    {
        return $this->belongsToMany(Discipline::class, 'course_class_disciplines')
            ->withPivot('teacher_id')
            ->withTimestamps();
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'course_id', 'course_id');
    }
}
