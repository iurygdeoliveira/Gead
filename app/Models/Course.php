<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\UuidTrait;

class Course extends Model
{
    use UuidTrait;

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'team_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollments');
    }

    public function disciplines(): HasMany
    {
        return $this->hasMany(Discipline::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }

    /**
     * @return array{completed: int, expected: int}
     */
    public function getEvaluationsCompletionStatus(): array
    {
        $teamId = $this->team_id;
        $courseClasses = CourseClass::where('course_id', $this->id)->where('team_id', $teamId)->get();
        $classIds = $courseClasses->pluck('id')->toArray();

        if (empty($classIds)) {
            return [
                'completed' => 0,
                'expected' => 0,
            ];
        }

        $enrollmentCountsByClass = ClassEnrollment::whereIn('course_class_id', $classIds)
            ->whereHas('enrollment.student', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('user_id')
                        ->orWhereHas('user', function ($q) {
                            $q->where('is_suspended', false);
                        });
                });
                $query->whereDoesntHave('enrollments', function ($q) {
                    $q->whereDoesntHave('classEnrollments');
                });
            })
            ->selectRaw('course_class_id, count(*) as total')
            ->groupBy('course_class_id')
            ->pluck('total', 'course_class_id');

        $disciplineCountsByClass = CourseClassDiscipline::whereIn('course_class_id', $classIds)
            ->selectRaw('course_class_id, count(*) as total')
            ->groupBy('course_class_id')
            ->pluck('total', 'course_class_id');

        $totalPotential = 0;
        foreach ($classIds as $classId) {
            $totalPotential += ($enrollmentCountsByClass[$classId] ?? 0) * ($disciplineCountsByClass[$classId] ?? 0);
        }

        $evaluations = Evaluation::where('team_id', $teamId)
            ->whereHas('courseClassDiscipline.courseClass', function ($query) {
                $query->where('course_id', $this->id);
            })
            ->whereHas('classEnrollment.enrollment.student', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('user_id')
                        ->orWhereHas('user', function ($q) {
                            $q->where('is_suspended', false);
                        });
                });
                $query->whereDoesntHave('enrollments', function ($q) {
                    $q->whereDoesntHave('classEnrollments');
                });
            })
            ->get();

        $realizadas = $evaluations->whereNotNull('planning_score')->count();
        $total = max($totalPotential, $realizadas, $evaluations->count());

        return [
            'completed' => $realizadas,
            'expected' => $total,
        ];
    }
}
