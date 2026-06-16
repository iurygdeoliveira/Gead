<?php

namespace App\Filament\Resources\Evaluations\Tables;

use App\Filament\Resources\Evaluations\Actions\DeleteEvaluationAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;

class EvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->whereIn('id', function ($q) {
                    $q->selectRaw('MAX(id)')
                      ->from('evaluations')
                      ->groupBy('course_class_discipline_id');
                })
            )
            ->defaultSort('course_class_discipline_id')
            ->columns([
               
                        TextColumn::make('courseClassDiscipline.teacher.name')
                            ->label('Professor')
                            ->searchable()
                            ->weight(FontWeight::Medium)
                            ->alignLeft(),
                        
                        TextColumn::make('courseClassDiscipline.discipline.name')
                            ->label('Disciplina')
                            ->searchable()
                            ->color('gray')
                            ->alignLeft(),
                    
                        TextColumn::make('courseClassDiscipline.courseClass.course.name')
                            ->label('Turma')
                            ->alignLeft(),
                            
                        TextColumn::make('teaching_period')
                            ->label('Período Letivo')
                            ->getStateUsing(function ($record) {
                                $ccd = $record->courseClassDiscipline;
                                if (!$ccd) return '-';
                                $courseClass = $ccd->courseClass;
                                $discipline = $ccd->discipline;
                                if (!$courseClass || !$discipline || empty($discipline->period) || !is_numeric($discipline->period)) {
                                    return $courseClass ? $courseClass->entry_period : '-';
                                }
                                
                                $entryPeriod = $courseClass->entry_period;
                                $isAnnual = $courseClass->course ? str_contains(mb_strtolower($courseClass->course->name, 'UTF-8'), 'integrado') : false;
                                $disciplinePeriod = (int)$discipline->period;
                                
                                $normalized = str_replace('/', '.', $entryPeriod);
                                $parts = explode('.', $normalized);
                                $year = (int)$parts[0];
                                $sem = (int)($parts[1] ?? 1);

                                if ($isAnnual) {
                                    $teachingYear = $year + $disciplinePeriod - 1;
                                    return "{$teachingYear}.1";
                                }

                                $semestersToAdd = $disciplinePeriod - 1;
                                for ($i = 0; $i < $semestersToAdd; $i++) {
                                    if ($sem === 2) {
                                        $year++;
                                        $sem = 1;
                                    } else {
                                        $sem = 2;
                                    }
                                }

                                return "{$year}.{$sem}";
                            })
                            ->color('gray')
                            ->alignLeft(),
                
            ])
            ->filters([
                
            ])
            
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('secondary'),
                    EditAction::make(),
                    DeleteEvaluationAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
