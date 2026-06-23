<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\UuidTrait;

class Evaluation extends Model
{
    use UuidTrait;

    protected $fillable = ['uuid', 'class_enrollment_id', 'course_class_discipline_id', 'planning_score', 'posture_score', 'attendance_score', 'punctuality_score', 'execution_score', 'assessment_score', 'comments', 'team_id'];

    public function classEnrollment(): BelongsTo
    {
        return $this->belongsTo(ClassEnrollment::class);
    }

    public function courseClassDiscipline(): BelongsTo
    {
        return $this->belongsTo(CourseClassDiscipline::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
