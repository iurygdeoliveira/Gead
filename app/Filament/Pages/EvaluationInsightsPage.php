<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\RoleType;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Teacher;
use App\Models\User;
use App\Traits\Filament\HasConfigurableNavigationSort;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class EvaluationInsightsPage extends Page implements HasForms
{
    use HasConfigurableNavigationSort;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $slug = 'insights-avaliacao';

    protected string $view = 'filament.pages.evaluation-insights-page';

    protected static ?string $navigationLabel = 'Diagnóstico';

    protected static ?string $title = 'Diagnóstico da Avaliação Docente';

    protected static string|\UnitEnum|null $navigationGroup = 'Avaliação Docente';

    public ?string $question = 'kpi_2_adesao';

    public ?int $courseId = null;

    public ?int $courseClassId = null;

    public ?int $teacherId = null;

    public ?int $correlationScope = 0;

    public function mount(): void
    {
        $firstCourse = Course::first();
        if ($firstCourse) {
            $this->courseId = $firstCourse->id;
        }

        $firstClass = CourseClass::first();
        if ($firstClass) {
            $this->courseClassId = $firstClass->id;
        }

        $firstTeacher = Teacher::first();
        if ($firstTeacher) {
            $this->teacherId = $firstTeacher->id;
        }

        $this->form->fill([ // @phpstan-ignore-line
            'question' => $this->question,
            'courseId' => $this->courseId,
            'courseClassId' => $this->courseClassId,
            'teacherId' => $this->teacherId,
            'correlationScope' => $this->correlationScope,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('question')
                    ->label('Pergunta analítica:')
                    ->searchable()
                    ->options([
                        'kpi_2_adesao' => '1. Qual é a taxa de participação/adesão dos alunos na avaliação?',
                        'kpi_8_dispensados' => '2. Qual é o percentual e distribuição dos alunos dispensados da avaliação?',
                        'kpi_1_dimensoes' => '3. Quais são as dimensões avaliativas mais fracas do campus?',
                        'kpi_3_evolucao' => '4. Como o desempenho docente evoluiu ao longo dos semestres?',
                        'kpi_4_distribuicao' => '5. Existe viés de avaliação ou polarização nas notas dos alunos?',
                        'kpi_5a_docente_indiv' => '6. Qual é o perfil de desempenho individual de um professor?',
                        'kpi_5b_docente_curso' => '7. Qual é o perfil geral e as deficiências coletivas do curso?',
                        'kpi_6_disciplinas' => '8. Quais disciplinas possuem os piores índices de avaliação docente?',
                        'kpi_7_turmas' => '9. Como as diferentes turmas de um curso realizaram a avaliação docente?',
                        'kpi_9_correlacoes' => '10. Existe correlação estatística entre o planejamento, postura e execução?',
                    ])
                    ->live()
                    ->required(),

                Select::make('correlationScope')
                    ->label('Escopo da Análise:')
                    ->options(fn (): array => [0 => 'Campus Inteiro (Todos os Cursos)'] + Course::orderBy('name')->pluck('name', 'id')->toArray())
                    ->default(0)
                    ->live()
                    ->searchable()
                    ->visible(fn ($get): bool => $get('question') === 'kpi_9_correlacoes'),

                Select::make('teacherId')
                    ->label('Filtrar por Professor:')
                    ->options(fn () => Teacher::orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => $this->teacherId ?? Teacher::orderBy('name')->first()?->id)
                    ->live()
                    ->searchable()
                    ->visible(fn ($get): bool => $get('question') == 'kpi_5a_docente_indiv'),

                Select::make('courseId')
                    ->label('Filtrar por Curso:')
                    ->options(fn () => Course::orderBy('name')->pluck('name', 'id'))
                    ->default(fn () => $this->courseId ?? Course::orderBy('name')->first()?->id)
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('courseClassId', null))
                    ->visible(fn ($get): bool => in_array($get('question'), ['kpi_3_evolucao', 'kpi_4_distribuicao', 'kpi_5b_docente_curso', 'kpi_6_disciplinas', 'kpi_7_turmas'])),

                Select::make('courseClassId')
                    ->label('Filtrar por Turma:')
                    ->options(function ($get) {
                        $query = CourseClass::query();
                        if ($courseId = $get('courseId')) {
                            $query->where('course_id', $courseId);
                        }

                        return $query->orderBy('name')->pluck('name', 'id');
                    })
                    ->default(function ($get) {
                        $query = CourseClass::query();
                        if ($courseId = $get('courseId')) {
                            $query->where('course_id', $courseId);
                        }

                        return $query->orderBy('name')->first()?->id;
                    })
                    ->live()
                    ->visible(fn ($get): bool => $get('question') === 'kpi_6_disciplinas'),
            ]);
    }

    #[\Override]
    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }

        return $user->hasRole(RoleType::MANAGER->value);
    }
}
