<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'name',
        'email',
        'team_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'enrollments');
    }

    /**
     * @return array<int, array{discipline_name: string, teacher_name: string, teaching_period: string, status: string}>
     */
    public function getEvaluationsStatusAttribute(): array
    {
        // Carrega todas as disciplinas do aluno com seus relacionamentos em uma única query
        $disciplines = CourseClassDiscipline::with(['courseClass.course', 'discipline', 'teacher'])
            ->whereHas('courseClass.classEnrollments.enrollment', function ($q) {
                $q->where('student_id', $this->id);
            })->get();

        // Carrega os IDs das disciplinas que já foram avaliadas em uma única query
        $evaluatedDisciplineIds = Evaluation::where('course_class_discipline_id', $disciplines->pluck('id'))
            ->whereHas('classEnrollment.enrollment', function ($q) {
                $q->where('student_id', $this->id);
            })
            ->whereNotNull('planning_score')
            ->pluck('course_class_discipline_id')
            ->toArray();

        return $disciplines->map(function (CourseClassDiscipline $ccd) use ($evaluatedDisciplineIds) {
            return [
                'discipline_name' => $ccd->discipline?->name ?? '-',
                'teacher_name' => $ccd->teacher?->name ?? 'Sem Professor',
                'teaching_period' => '2026.1', // Simplificado — gerado semestralmente
                'status' => in_array($ccd->id, $evaluatedDisciplineIds) ? 'Realizada' : 'Pendente',
            ];
        })->all();
    }
}
