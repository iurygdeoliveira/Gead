<?php

namespace App\Filament\Resources\Evaluations\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;

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
                ->with([
                    'courseClassDiscipline.courseClass.course',
                    'courseClassDiscipline.discipline',
                    'courseClassDiscipline.teacher',
                ])
            )
            ->defaultSort('course_class_discipline_id')
            ->columns([
               
                        TextColumn::make('courseClassDiscipline.teacher.name')
                            ->label('Professor')
                            ->searchable(isIndividual: true, isGlobal: false)
                            ->weight(FontWeight::Medium)
                            ->alignLeft(),
                        
                        TextColumn::make('courseClassDiscipline.discipline.name')
                            ->label('Disciplina')
                            ->searchable(isIndividual: true, isGlobal: false)
                            ->color('gray')
                            ->alignLeft(),
                
                            
                        TextColumn::make('teaching_period')
                            ->label('Período Letivo')
                            ->getStateUsing(fn ($record) => self::calculateTeachingPeriod($record))
                            ->color('gray')
                            ->alignLeft(),
                
            ])
            ->filters([
                
            ])
            
            ->recordActions([
               
                    ViewAction::make()
                        ->color('secondary'),
                   
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function calculateTeachingPeriod($record): string
    {
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
    }
}
