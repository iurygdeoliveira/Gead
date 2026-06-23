<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'uuid',
    'name',
    'email',
    'registration_number',
    'team_id',
    'user_id',
])]
class Teacher extends Model
{
    use UuidTrait;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * @return HasMany<CourseClassDiscipline, $this>
     */
    public function taughtDisciplines(): HasMany
    {
        return $this->hasMany(CourseClassDiscipline::class);
    }

    protected static array $activeClassEnrollmentsCache = [];

    protected static array $evaluationsCompletedCache = [];

    /**
     * @return array{completed: int, expected: int}
     */
    public function getEvaluationsCompletionStatus(): array
    {
        $ccds = $this->relationLoaded('taughtDisciplines') ? $this->taughtDisciplines : $this->taughtDisciplines()->get();
        $expected = $ccds->count();
        $completed = 0;

        if ($expected === 0) {
            return [
                'completed' => 0,
                'expected' => 0,
            ];
        }

        $classIdsToFetch = [];
        $ccdIdsToFetch = [];
        foreach ($ccds as $ccd) {
            /** @var CourseClassDiscipline $ccd */
            $courseClassId = $ccd->getAttribute('course_class_id');
            if (! isset(self::$activeClassEnrollmentsCache[$courseClassId])) {
                $classIdsToFetch[] = $courseClassId;
            }
            if (! isset(self::$evaluationsCompletedCache[$ccd->getAttribute('id')])) {
                $ccdIdsToFetch[] = $ccd->getAttribute('id');
            }
        }

        if ($classIdsToFetch !== []) {
            $activeClassEnrollments = ClassEnrollment::whereIn('course_class_id', array_unique($classIdsToFetch))
                ->whereHas('enrollment.student', function ($query): void {
                    $query->where(function ($subQuery): void {
                        $subQuery->whereNull('user_id')
                            ->orWhereHas('user', function ($q): void {
                                $q->where('is_suspended', false);
                            });
                    });
                    $query->whereDoesntHave('enrollments', function ($q): void {
                        $q->whereDoesntHave('classEnrollments');
                    });
                })
                ->get(['id', 'course_class_id']);

            foreach (array_unique($classIdsToFetch) as $classId) {
                self::$activeClassEnrollmentsCache[$classId] = [];
            }

            foreach ($activeClassEnrollments as $enrollment) {
                self::$activeClassEnrollmentsCache[$enrollment->course_class_id][] = $enrollment->id;
            }
        }

        if ($ccdIdsToFetch !== []) {
            $evaluations = Evaluation::whereIn('course_class_discipline_id', array_unique($ccdIdsToFetch))
                ->whereNotNull('planning_score')
                ->get(['id', 'course_class_discipline_id', 'class_enrollment_id']);

            foreach (array_unique($ccdIdsToFetch) as $ccdId) {
                self::$evaluationsCompletedCache[$ccdId] = [];
            }

            foreach ($evaluations as $eval) {
                self::$evaluationsCompletedCache[$eval->course_class_discipline_id][] = $eval->class_enrollment_id;
            }
        }

        foreach ($ccds as $ccd) {
            /** @var CourseClassDiscipline $ccd */
            $activeClassEnrollmentIds = self::$activeClassEnrollmentsCache[$ccd->getAttribute('course_class_id')] ?? [];
            $activeCount = count($activeClassEnrollmentIds);

            if ($activeCount > 0) {
                $completedEvalIds = self::$evaluationsCompletedCache[$ccd->getAttribute('id')] ?? [];
                $completedCount = count(array_intersect($completedEvalIds, $activeClassEnrollmentIds));

                if ($completedCount === $activeCount) {
                    $completed++;
                }
            }
        }

        return [
            'completed' => $completed,
            'expected' => $expected,
        ];
    }
}
