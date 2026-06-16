<?php

namespace App\Filament\Resources\Students\Tables;

use App\Filament\Resources\Students\Actions\ChangeStudentAccessStatusBulkAction;
use App\Filament\Resources\Students\Actions\DeleteStudentAction;
use App\Filament\Resources\Students\Actions\ToggleStudentSuspensionAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use App\Models\CourseClassDiscipline;
use App\Models\Evaluation;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('user')
                    ->select('students.*')
                    ->addSelect([
                        'evaluations_done' => Evaluation::selectRaw('count(*)')
                            ->join('class_enrollments', 'evaluations.class_enrollment_id', '=', 'class_enrollments.id')
                            ->join('enrollments', 'class_enrollments.enrollment_id', '=', 'enrollments.id')
                            ->whereColumn('enrollments.student_id', 'students.id')
                            ->whereNotNull('evaluations.planning_score'),
                        
                        'evaluations_total' => CourseClassDiscipline::selectRaw('count(*)')
                            ->join('class_enrollments', 'course_class_disciplines.course_class_id', '=', 'class_enrollments.course_class_id')
                            ->join('enrollments', 'class_enrollments.enrollment_id', '=', 'enrollments.id')
                            ->whereColumn('enrollments.student_id', 'students.id'),
                    ]);
            })
            ->recordUrl(fn (\App\Models\Student $record): string => \App\Filament\Resources\Students\StudentResource::getUrl('view', ['record' => $record]))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable()
                    ->wrap(),
                TextColumn::make('enrollments.registration_number')
                    ->label('Matrícula(s)')
                    ->listWithLineBreaks()
                    ->wrap(),
                TextColumn::make('enrollments.course.name')
                    ->label('Curso')
                    ->listWithLineBreaks()
                    ->wrap(),
                TextColumn::make('evaluations_status')
                    ->label('Avaliações')
                    ->getStateUsing(fn ($record) => ($record->evaluations_done ?? 0) . ' / ' . ($record->evaluations_total ?? 0))
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
                    DeleteStudentAction::make()
                        ->icon(Heroicon::Trash),
                    ToggleStudentSuspensionAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ChangeStudentAccessStatusBulkAction::make(),
                ]),
            ]);
    }
}
