<?php

namespace App\Filament\Widgets;

use App\Models\ClassEnrollment;
use App\Models\CourseClassDiscipline;
use App\Models\Evaluation;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StudentDisciplinesWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Disciplinas para Avaliar';

    #[\Override]
    public function table(Table $table): Table
    {
        return $table
            ->query(
                CourseClassDiscipline::query()
                    ->whereHas('courseClass.classEnrollments.enrollment.student', function (Builder $query): void {
                        $query->where('user_id', Filament::auth()->user()->id);
                    })
                    ->with(['discipline', 'teacher'])
                    ->withExists(['evaluations as is_evaluated' => function ($query): void {
                        $query->whereHas('classEnrollment.enrollment.student', function ($sub): void {
                            $sub->where('user_id', Filament::auth()->user()->id);
                        });
                    }])
                    ->orderBy('is_evaluated', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('discipline.name')
                    ->label('Nome da Disciplina')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Nome do Professor')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_evaluated')
                    ->label('Status')
                    ->getStateUsing(fn (CourseClassDiscipline $record): string => $record->getAttribute('is_evaluated') ? 'Avaliada' : 'Pendente')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'Avaliada' => 'success',
                        'Pendente' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                
            ])
            ->defaultSort('is_evaluated', 'asc')
            ->paginated(false)
            ->recordActions([
                Action::make('avaliar')
                    ->label('Avaliar')
                    ->modalHeading(fn (CourseClassDiscipline $record): string => "Avaliar: {$record->discipline->name} - {$record->teacher->name}")
                    ->button()
                    ->slideOver()
                    ->hidden(fn (CourseClassDiscipline $record): bool => (bool) $record->getAttribute('is_evaluated'))
                    ->schema([
                        TextInput::make('planning_score')
                            ->label('O docente apresenta seu plano de ensino (PLANEJAMENTO) no início do semestre ou ano letivo, indicando a ementa, competências e habilidades, recursos didáticos que serão utilizados, formas de avaliações, referências bibliográficas?')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->required(),
                        TextInput::make('posture_score')
                            ->label('O docente apresenta uma POSTURA adequada ao cargo e responsabilidade que ocupa?')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->required(),
                        TextInput::make('attendance_score')
                            ->label('O docente é ASSÍDUO, ou seja, não falta às aulas e quando falta, apresenta justificativa e promove suas devidas reposições ou anteposições?')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->required(),
                        TextInput::make('punctuality_score')
                            ->label('O docente é PONTUAL, ou seja, não chega atrasado ou libera a turma mais cedo?')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->required(),
                        TextInput::make('execution_score')
                            ->label('O docente na REALIZAÇÃO de suas aulas procura contextualizar os conteúdos trabalhados; domina o conteúdo; utiliza bem os recursos didáticos; possui fala(dicção) clara, coerente e fluente?')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->required(),
                        TextInput::make('assessment_score')
                            ->label('O docente nas AVALIAÇÕES mostra coerência entre o que foi ensinado e o que é exigido do estudante, entrega as avaliações e comenta os resultados, auxilia no processo de recuperação daqueles conteúdos não apreendidos?')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->required(),
                    ])
                    ->action(function (array $data, CourseClassDiscipline $record): void {
                        // Find the student's class_enrollment for this course_class
                        $student = Auth::user()->student;
                        if (! $student) {
                            return;
                        }

                        $classEnrollment = ClassEnrollment::where('course_class_id', $record->getAttribute('course_class_id'))
                            ->whereHas('enrollment', function ($q) use ($student): void {
                                $q->where('student_id', $student->id);
                            })
                            ->first();

                        if (! $classEnrollment) {
                            return;
                        }

                        Evaluation::create([
                            'class_enrollment_id' => $classEnrollment->id,
                            'course_class_discipline_id' => $record->id,
                            'team_id' => $student->team_id ?? 1,
                            'planning_score' => $data['planning_score'],
                            'posture_score' => $data['posture_score'],
                            'attendance_score' => $data['attendance_score'],
                            'punctuality_score' => $data['punctuality_score'],
                            'execution_score' => $data['execution_score'],
                            'assessment_score' => $data['assessment_score'],
                        ]);
                    }),
            ]);
    }
}
