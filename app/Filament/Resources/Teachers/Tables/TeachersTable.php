<?php

namespace App\Filament\Resources\Teachers\Tables;

use App\Filament\Resources\Teachers\Actions\ChangeTeacherAccessStatusBulkAction;
use App\Filament\Resources\Teachers\Actions\DeleteTeacherAction;
use App\Filament\Resources\Teachers\Actions\ToggleTeacherSuspensionAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TeachersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->defaultSort('name')
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
            $evaluations = \App\Models\Evaluation::where('course_class_discipline_id', $ccd->id)->get();
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

        $team = \Filament\Facades\Filament::getTenant();
        
        $managerUser = \App\Models\User::whereHas('rolesWithTeams', function ($query) use ($team) {
            $query->where('roles.name', \App\Enums\RoleType::MANAGER->value);
            if ($team) {
                $query->where('model_has_roles.team_id', $team->id);
            }
        })->first();

        $managerTeacher = $managerUser ? \App\Models\Teacher::where('email', $managerUser->email)->first() : null;
        $managerName = $managerUser ? $managerUser->name : 'Walmir (Gerente)';
        $managerSiape = $managerTeacher ? $managerTeacher->registration_number : 'Não informado';

        $data = [
            'teacher_name' => $teacherName,
            'period' => '2026.1',
            'disciplines' => $disciplinesData,
            'consolidated_score' => $consolidatedScore,
            'manager_name' => $managerName,
            'manager_siape' => $managerSiape,
        ];

        return response()->streamDownload(function () use ($data) {
            echo \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.evaluation-report', ['data' => $data])->stream();
        }, 'RelatorioAvaliacaoDocente_' . \Illuminate\Support\Str::slug($teacherName) . '.pdf');
    }
}
