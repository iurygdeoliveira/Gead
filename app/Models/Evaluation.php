<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['uuid', 'class_enrollment_id', 'course_class_discipline_id', 'planning_score', 'posture_score', 'attendance_score', 'punctuality_score', 'execution_score', 'assessment_score', 'comments', 'team_id'])]
class Evaluation extends Model
{
    use UuidTrait;

    /**
     * @return BelongsTo<ClassEnrollment, $this>
     */
    public function classEnrollment(): BelongsTo
    {
        return $this->belongsTo(ClassEnrollment::class);
    }

    /**
     * @return BelongsTo<CourseClassDiscipline, $this>
     */
    public function courseClassDiscipline(): BelongsTo
    {
        return $this->belongsTo(CourseClassDiscipline::class);
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
