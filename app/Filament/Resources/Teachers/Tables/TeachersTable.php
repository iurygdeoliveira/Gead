<?php

namespace App\Filament\Resources\Teachers\Tables;

use App\Enums\RoleType;
use App\Filament\Resources\Teachers\Actions\ChangeTeacherAccessStatusBulkAction;
use App\Filament\Resources\Teachers\Actions\DeleteTeacherAction;
use App\Filament\Resources\Teachers\Actions\ToggleTeacherSuspensionAction;
use App\Models\Evaluation;
use App\Models\Teacher;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['user', 'taughtDisciplines']);

                $currentTeam = Filament::getTenant();
                $teamId = $currentTeam?->id;

                if (! $teamId) {
                    return;
                }

                if (empty($query->getQuery()->orders)) {
                    $query->select('teachers.*')
                        ->selectSub(function ($query) {
                            $query->selectRaw('count(*)')
                                ->from('course_class_disciplines as ccd')
                                ->whereColumn('ccd.teacher_id', 'teachers.id')
                                ->whereRaw('
                                    (
                                        SELECT COUNT(*) 
                                        FROM class_enrollments ce 
                                        JOIN enrollments e ON ce.enrollment_id = e.id 
                                        JOIN students s ON e.student_id = s.id 
                                        LEFT JOIN users u ON s.user_id = u.id 
                                        WHERE ce.course_class_id = ccd.course_class_id 
                                          AND (s.user_id IS NULL OR u.is_suspended = false)
                                          AND NOT EXISTS (
                                              SELECT 1 
                                              FROM enrollments e2 
                                              LEFT JOIN class_enrollments ce2 ON e2.id = ce2.enrollment_id 
                                              WHERE e2.student_id = s.id AND ce2.id IS NULL
                                          )
                                    ) > (
                                        SELECT COUNT(*) 
                                        FROM evaluations ev 
                                        WHERE ev.course_class_discipline_id = ccd.id 
                                          AND ev.planning_score IS NOT NULL
                                    )
                                ');
                        }, 'incomplete_ccds_count')
                        ->orderByDesc('incomplete_ccds_count');
                }
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('registration_number')
                    ->label('SIAPE')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('evaluations_status')
                    ->label('Avaliações')
                    ->getStateUsing(function (Teacher $record) {
                        $status = $record->getEvaluationsCompletionStatus();

                        return "{$status['completed']} / {$status['expected']}";
                    })
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->icon(Heroicon::Eye)
                        ->color('secondary'),
                    EditAction::make()
                        ->icon(Heroicon::Pencil),
                    DeleteTeacherAction::make()
                        ->icon(Heroicon::Trash),
                    ToggleTeacherSuspensionAction::make()
                        ->color('warning'),
                    Action::make('pdf')
                        ->label('PDF')
                        ->color('success')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(fn (Model $record) => self::downloadPdfReport($record)),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ChangeTeacherAccessStatusBulkAction::make(),
                ]),
            ]);
    }

    protected static function downloadPdfReport(Model $record)
    {
        $teacherName = $record->name;
        $disciplinesData = [];
        $totalScores = 0;

        $ccds = $record->taughtDisciplines()->with(['courseClass.course', 'discipline'])->get();

        foreach ($ccds as $ccd) {
            $evaluations = Evaluation::where('course_class_discipline_id', $ccd->id)->get();
            $totalEvaluations = $evaluations->count();

            if ($totalEvaluations === 0) {
                continue;
            }

            $planningAvg = $evaluations->avg('planning_score');
            $postureAvg = $evaluations->avg('posture_score');
            $attendanceAvg = $evaluations->avg('attendance_score');
            $punctualityAvg = $evaluations->avg('punctuality_score');
            $executionAvg = $evaluations->avg('execution_score');
            $assessmentAvg = $evaluations->avg('assessment_score');

            $classScore = ($planningAvg + $postureAvg + $attendanceAvg + $punctualityAvg + $executionAvg + $assessmentAvg) / 2;

            $disciplinesData[] = [
                'course_name' => $ccd->courseClass->course->name ?? 'N/A',
                'discipline_name' => $ccd->discipline->name ?? 'N/A',
                'total_evaluations' => $totalEvaluations,
                'planning_avg' => $planningAvg,
                'posture_avg' => $postureAvg,
                'attendance_avg' => $attendanceAvg,
                'punctuality_avg' => $punctualityAvg,
                'execution_avg' => $executionAvg,
                'assessment_avg' => $assessmentAvg,
                'class_score' => $classScore,
            ];

            $totalScores += $classScore;
        }

        $disciplinesCount = count($disciplinesData);
        $consolidatedScore = $disciplinesCount > 0 ? ($totalScores / $disciplinesCount) : 0;

        $team = Filament::getTenant();

        $managerUser = User::whereHas('rolesWithTeams', function ($query) use ($team) {
            $query->where('roles.name', RoleType::MANAGER->value);
            if ($team) {
                $query->where('model_has_roles.team_id', $team->id);
            }
        })->first();

        $managerTeacher = $managerUser ? Teacher::where('email', $managerUser->email)->first() : null;
        $managerName = $managerUser ? $managerUser->name : 'Walmir (Gerente)';
        $managerSiape = $managerTeacher ? $managerTeacher->registration_number : 'Não informado';

        $logoPath = public_path('images/brasao.png');
        $logoSrc = 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath));

        $data = [
            'teacher_name' => $teacherName,
            'teacher_siape' => $record->registration_number ?? 'Não informado',
            'period' => '2026.1',
            'disciplines' => $disciplinesData,
            'consolidated_score' => $consolidatedScore,
            'logo_src' => $logoSrc,
            'manager_name' => $managerName,
            'manager_siape' => $managerSiape,
        ];

        return response()->streamDownload(function () use ($data) {
            echo Pdf::loadView('pdf.evaluation-report', ['data' => $data])->stream();
        }, 'RelatorioAvaliacaoDocente_'.Str::slug($teacherName).'.pdf');
    }
}
