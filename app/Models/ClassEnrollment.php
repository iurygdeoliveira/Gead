<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ClassEnrollment extends Model
{
    protected $fillable = ['enrollment_id', 'course_class_id'];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function courseClass(): BelongsTo
    {
        return $this->belongsTo(CourseClass::class);
    }

    public function evaluation(): HasOne
    {
        return $this->hasOne(Evaluation::class);
    }
}
