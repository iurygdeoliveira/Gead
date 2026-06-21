<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discipline extends Model
{
    protected $fillable = ['course_id', 'name', 'code', 'period'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function courseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }
}
