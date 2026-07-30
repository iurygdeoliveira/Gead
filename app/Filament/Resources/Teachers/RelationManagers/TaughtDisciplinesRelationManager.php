<?php

namespace App\Filament\Resources\Teachers\RelationManagers;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseClassDiscipline;
use App\Models\Discipline;
use App\Models\Teacher;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TaughtDisciplinesRelationManager extends RelationManager
{
    use NotificationsTrait;

    protected static string $relationship = 'taughtDisciplines';

    protected static ?string $title = 'Turmas e Disciplinas Ministradas';

    protected static ?string $modelLabel = 'Disciplina Ministrada';

    protected static ?string $pluralModelLabel = 'Disciplinas Ministradas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Not used because we use a custom action in the header
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('courseClass.name')
                    ->label('Turma')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('courseClass.entry_period')
                    ->label('Período')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('discipline.name')
                    ->label('Disciplina')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('vincular')
                    ->label('Vincular Turma/Disciplina')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\Select::make('course_id')
                            ->label('Curso')
                            ->options(Course::pluck('name', 'id'))
                            ->live()
                            ->dehydrated(false),
                        Forms\Components\Select::make('period')
                            ->label('Período')
                            ->options(function (Get $get) {
                                $courseId = $get('course_id');
                                if (! $courseId) {
                                    return CourseClass::pluck('entry_period', 'entry_period')->unique();
                                }

                                return CourseClass::where('course_id', $courseId)
                                    ->pluck('entry_period', 'entry_period')
                                    ->unique();
                            })
                            ->live()
                            ->dehydrated(false),
                        Forms\Components\Select::make('course_class_id')
                            ->label('Turma')
                            ->options(function (Get $get) {
                                $courseId = $get('course_id');
                                $period = $get('period');

                                $query = CourseClass::query();
                                if ($courseId) {
                                    $query->where('course_id', $courseId);
                                }
                                if ($period) {
                                    $query->where('entry_period', $period);
                                }

                                return $query->get()->mapWithKeys(fn ($cc): array => [$cc->id => $cc->name ?? $cc->code]);
                            })
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('discipline_id')
                            ->label('Disciplina')
                            ->options(function (Get $get) {
                                $courseId = $get('course_id');

                                $query = Discipline::query();
                                if ($courseId) {
                                    $query->where('course_id', $courseId);
                                }

                                return $query->pluck('name', 'id');
                            })
                            ->required(),
                    ])
                    ->action(function (array $data, RelationManager $livewire): void {
                        /** @var Teacher $teacher */
                        $teacher = $livewire->getOwnerRecord();

                        // Remover possíveis vínculos "órfãos" dessa turma/disciplina para não herdar avaliações antigas
                        CourseClassDiscipline::where('course_class_id', $data['course_class_id'])
                            ->where('discipline_id', $data['discipline_id'])
                            ->whereNull('teacher_id')
                            ->delete();

                        CourseClassDiscipline::updateOrCreate([
                            'course_class_id' => $data['course_class_id'],
                            'discipline_id' => $data['discipline_id'],
                            'teacher_id' => $teacher->id,
                        ]);

                        $livewire->notifySuccess('Vínculo criado com sucesso.');
                        $livewire->dispatch('refreshTeacherInfolist');
                    }),
            ])
            ->recordActions([
                Action::make('desvincular')
                    ->label('Desvincular')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Desvincular Professor')
                    ->modalDescription('Tem certeza que deseja desvincular o professor desta turma e disciplina? ATENÇÃO: Todas as avaliações já realizadas para este professor nesta disciplina serão apagadas definitivamente.')
                    ->modalSubmitActionLabel('Sim, desvincular')
                    ->action(function (CourseClassDiscipline $record, RelationManager $livewire): void {
                        $record->delete();
                        $livewire->notifySuccess('Professor desvinculado e avaliações removidas com sucesso.');
                        $livewire->dispatch('refreshTeacherInfolist');
                    }),
            ])
            ->toolbarActions([
                // Remove Bulk Delete to avoid accidental cascade deletes
            ]);
    }
}
