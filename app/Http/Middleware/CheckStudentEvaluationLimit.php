<?php

namespace App\Http\Middleware;

use App\Models\CourseClassDiscipline;
use App\Models\Evaluation;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentEvaluationLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Filament::auth()->user();
        if (! $user || ! $user->student) {
            return $next($request);
        }

        $student = $user->student;

        // Get all discipline IDs for the student
        $disciplineIds = CourseClassDiscipline::whereHas('courseClass.classEnrollments.enrollment', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->pluck('id');

        if ($disciplineIds->isNotEmpty()) {
            $evaluationsCount = Evaluation::selectRaw('course_class_discipline_id, count(*) as count')
                ->whereIn('course_class_discipline_id', $disciplineIds)
                ->whereHas('classEnrollment.enrollment', function ($q) use ($student) {
                    $q->where('student_id', $student->id);
                })
                ->whereNotNull('planning_score')
                ->groupBy('course_class_discipline_id')
                ->pluck('count', 'course_class_discipline_id');

            $hasPending = false;
            foreach ($disciplineIds as $id) {
                if (! isset($evaluationsCount[$id]) || $evaluationsCount[$id] < 2) {
                    $hasPending = true;
                    break;
                }
            }

            if (! $hasPending) {
                return response()->view('filament.pages.evaluations-completed');
            }
        }

        return $next($request);
    }
}
