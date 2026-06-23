<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable(['course_id', 'name', 'code', 'period'])]
class Discipline extends Model
{
    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return HasMany<CourseClass, $this>
     */
    public function courseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }

    /**
     * @return BelongsToMany<Teacher, $this, Pivot>
     */
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'course_class_disciplines', 'discipline_id', 'teacher_id')
            ->distinct();
    }
}
