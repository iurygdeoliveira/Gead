<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

#[Fillable([
    'uuid',
    'name',
    'email',
    'team_id',
    'user_id',
    'is_dispensed_from_evaluations',
])]
class Student extends Model
{
    use UuidTrait;

    #[\Override]
    protected function casts(): array
    {
        return [
            'is_dispensed_from_evaluations' => 'boolean',
        ];
    }

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
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * @return BelongsToMany<Course, $this, Pivot>
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments');
    }

    /**
     * @return array<int, array{discipline_name: string, teacher_name: string, teaching_period: string, status: string}>
     */
    protected function getEvaluationsStatusAttribute(): array
    {
        // Carrega todas as disciplinas do aluno com seus relacionamentos em uma única query
        $disciplines = CourseClassDiscipline::with(['courseClass.course', 'discipline', 'teacher'])
            ->whereHas('courseClass.classEnrollments.enrollment', function (Builder $q): void {
                $q->where('student_id', $this->id);
            })->get();

        if ($disciplines->isEmpty()) {
            return [];
        }

        // Carrega os IDs das disciplinas que já foram avaliadas em uma única query
        $evaluatedDisciplineIds = Evaluation::whereIn('course_class_discipline_id', $disciplines->pluck('id'))
            ->whereHas('classEnrollment.enrollment', function ($q): void {
                $q->where('student_id', $this->id);
            })
            ->whereNotNull('planning_score')
            ->pluck('course_class_discipline_id')
            ->toArray();

        return $disciplines->map(fn (CourseClassDiscipline $ccd): array => [
            'discipline_name' => $ccd->discipline->name ?? '-',
            'teacher_name' => $ccd->teacher->name ?? 'Sem Professor',
            'teaching_period' => '2026.1', // Simplificado — gerado semestralmente
            'status' => in_array($ccd->id, $evaluatedDisciplineIds) ? 'Realizada' : 'Pendente',
        ])->all();
    }
}
