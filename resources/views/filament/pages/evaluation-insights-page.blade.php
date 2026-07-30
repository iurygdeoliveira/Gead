<x-filament-panels::page>
    <style>
        .apexcharts-canvas text,
        .apexcharts-canvas tspan,
        .apexcharts-legend-text,
        .apexcharts-title-text,
        .apexcharts-subtitle-text,
        .apexcharts-text,
        .apexcharts-datalabel,
        .apexcharts-xaxis-label,
        .apexcharts-yaxis-label,
        .apexcharts-tooltip,
        .apexcharts-pie-label {
            font-family: var(--orbit-font-family, 'Poppins', sans-serif) !important;
            font-weight: 600 !important;
        }

        /* CONFIGURAÇÃO DE ESPAÇAMENTO DA PÁGINA */
        .insights-page-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 1.25rem !important; /* Espaço entre o Formulário (Selects) e o Card Principal */
        }

        .insights-card-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 1.25rem !important; /* Espaço entre o texto de ajuda (Section) e o Gráfico */
        }

        /* CORES DE DESTAQUE NOS TEXTOS DE AJUDA */
        .text-highlight-danger {
            color: #dc2626 !important;
        }
        .dark .text-highlight-danger {
            color: #f87171 !important;
        }

        .text-highlight-warning {
            color: #d97706 !important;
        }
        .dark .text-highlight-warning {
            color: #fbbf24 !important;
        }

        .text-highlight-success {
            color: #16a34a !important;
        }
        .dark .text-highlight-success {
            color: #4ade80 !important;
        }
    </style>
    
    <div class="insights-page-container">
        <!-- Filtros / Selects -->
        <div class="w-full">
            {{ $this->form }}
        </div>

        <!-- Seção do Gráfico + Ajuda -->
        <div class="insights-card-container bg-white dark:bg-gray-900 p-6 rounded-xl shadow border border-gray-200 dark:border-gray-800">
            @php
                $teamId = filament()->getTenant()?->id;
                $questionKey = $this->form->getState()['question'] ?? 'kpi_2_adesao';
                $selectedCourseId = ! empty($this->form->getState()['courseId']) ? (int) $this->form->getState()['courseId'] : ($this->courseId ?? \App\Models\Course::first()?->id);
                $selectedCourseClassId = ! empty($this->form->getState()['courseClassId']) ? (int) $this->form->getState()['courseClassId'] : ($this->courseClassId ?? \App\Models\CourseClass::first()?->id);
                $selectedTeacherId = ! empty($this->form->getState()['teacherId']) ? (int) $this->form->getState()['teacherId'] : ($this->teacherId ?? \App\Models\Teacher::first()?->id);
                $selectedCorrelationScope = ! empty($this->form->getState()['correlationScope']) ? (int) $this->form->getState()['correlationScope'] : ($this->correlationScope ?? 0);
            @endphp

            @switch($questionKey)
                @case('kpi_1_dimensoes')
                    <x-filament::section heading="Como analisar este gráfico" compact>
                        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed text-justify">
                            O mapa de calor (heatmap) funciona como um painel de alertas para o campus. A leitura deve ser feita observando a média de cada uma das 6 dimensões pedagógicas: células em <strong class="text-highlight-danger">vermelho (&lt; 5.0)</strong> indicam deficiências graves que exigem intervenção pedagógica imediata da gestão; em <strong class="text-highlight-warning">amarelo (5.0 a 6.9)</strong> apontam fragilidades em estado de atenção preventiva; e em <strong class="text-highlight-success">verde (&ge; 7.0)</strong> representam dimensões consolidadas e satisfatórias. A conclusão deste gráfico orienta exatamente onde a instituição deve priorizar investimentos, capacitações docentes e planos de ação.
                        </p>
                    </x-filament::section>
                    @livewire(\App\Filament\Widgets\Insights\WeakestDimensionsHeatmapWidget::class, ['teamId' => $teamId], key('kpi_1_' . $teamId))
                    @break

                @case('kpi_2_adesao')
                    <x-filament::section heading="Como analisar este gráfico" compact>
                        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed text-justify">
                            Este gráfico de barras empilhadas exibe a quantidade de avaliações <strong class="text-highlight-success">realizadas (verde)</strong> em contraste com as <strong class="text-highlight-danger">pendentes (vermelho)</strong> para cada curso. Cursos com taxa de participação inferior a <strong>50%</strong> exigem campanhas de engajamento, pois a baixa adesão compromete a validade estatística das conclusões.
                        </p>
                    </x-filament::section>
                    @livewire(\App\Filament\Widgets\Insights\ParticipationRateGaugeWidget::class, ['teamId' => $teamId], key('kpi_2_' . $teamId))
                    @break

                @case('kpi_3_evolucao')
                    <x-filament::section heading="Como analisar este gráfico" compact>
                        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed text-justify">
                            Cada linha representa a evolução histórica de uma dimensão ao longo dos semestres letivos. <strong class="text-highlight-success">Linhas ascendentes</strong> indicam melhoria após planos de ação, enquanto <strong class="text-highlight-danger">linhas descendentes</strong> apontam queda de desempenho. Cruzamentos entre linhas destacam mudanças nas percepções dos estudantes entre períodos.
                        </p>
                    </x-filament::section>
                    @livewire(\App\Filament\Widgets\Insights\TemporalEvolutionLineWidget::class, ['teamId' => $teamId, 'courseId' => $selectedCourseId], key('kpi_3_' . $selectedCourseId))
                    @break

                @case('kpi_4_distribuicao')
                    <x-filament::section heading="Como analisar este gráfico" compact>
                        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed text-justify">
                            O histograma de frequência das notas (0 a 10) permite identificar o comportamento avaliativo dos estudantes. A partir do formato da curva, quatro resultados fundamentais podem ser extraídos: (1) <strong class="text-highlight-success">Curva bem distribuída</strong> indica uma avaliação consciente e ponderada; (2) <strong class="text-highlight-warning">Picos na nota 10</strong> revelam viés de complacência (atribuição de nota máxima sem análise crítica); (3) <strong class="text-highlight-warning">Acúmulos nos dois extremos (0 e 10)</strong> evidenciam polarização de opiniões na turma; e (4) <strong class="text-highlight-danger">Picos isolados na nota 0</strong> sinalizam retaliações diretas ou protestos da turma decorrentes de atritos pontuais (ex: rigor em provas ou reprovações), exigindo mediação pedagógica da coordenação.
                        </p>
                    </x-filament::section>
                    @livewire(\App\Filament\Widgets\Insights\GradeDistributionHistogramWidget::class, ['teamId' => $teamId, 'courseId' => $selectedCourseId], key('kpi_4_' . $selectedCourseId))
                    @break

                @case('kpi_5a_docente_indiv')
                    <x-filament::section heading="Como analisar este gráfico" compact>
                        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed text-justify">
                            O gráfico radar apresenta o perfil 360° do docente. Um traçado mais amplo em direção às bordas demonstra alto desempenho. Recuos ou "dentes" na figura apontam exatamente em quais dimensões o professor necessita de apoio pedagógico.
                        </p>
                    </x-filament::section>
                    @livewire(\App\Filament\Widgets\Insights\TeacherProfileRadarWidget::class, ['teamId' => $teamId, 'teacherId' => $selectedTeacherId], key('kpi_5a_' . $selectedTeacherId))
                    @break

                @case('kpi_5b_docente_curso')
                    <x-filament::section heading="Como analisar este gráfico" compact>
                        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed text-justify">
                            Cada linha representa a média consolidada dos professores de uma turma do curso. Quando <strong>todas as linhas se estreitam em direção ao centro</strong> na mesma dimensão, trata-se de uma deficiência coletiva do curso. Quando apenas uma turma recua, o problema é isolado daquela turma específica.
                        </p>
                    </x-filament::section>
                    @livewire(\App\Filament\Widgets\Insights\CourseClassTeachersRadarWidget::class, ['teamId' => $teamId, 'courseId' => $selectedCourseId], key('kpi_5b_' . $selectedCourseId))
                    @break

                @case('kpi_6_disciplinas')
                    <x-filament::section heading="Como analisar este gráfico" compact>
                        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed text-justify">
                            As disciplinas estão ordenadas em ordem crescente pelas menores médias. As barras em <strong class="text-highlight-danger">vermelho (&lt; 5.0)</strong> indicam as disciplinas prioritárias para revisão de ementas, planos de aula ou apoio metodológico ao corpo docente.
                        </p>
                    </x-filament::section>
                    @livewire(\App\Filament\Widgets\Insights\ProblematicDisciplinesBarWidget::class, ['teamId' => $teamId, 'courseId' => $selectedCourseId, 'courseClassId' => $selectedCourseClassId], key('kpi_6_' . $selectedCourseId . '_' . $selectedCourseClassId))
                    @break

                @case('kpi_7_turmas')
                    <x-filament::section heading="Como analisar este gráfico" compact>
                        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed text-justify">
                            Este gráfico radar compara a forma como cada turma do curso realizou a avaliação docente nas 6 dimensões. A leitura permite identificar discrepâncias na percepção dos estudantes: se uma turma específica apresentar uma área significativamente <strong class="text-highlight-danger">menor ou recuada</strong> em relação às demais do mesmo curso, evidencia-se a presença de fatores contextuais desfavoráveis naquele período (ex: horários críticos, infraestrutura de laboratórios ou desafios na dinâmica da turma).
                        </p>
                    </x-filament::section>
                    @livewire(\App\Filament\Widgets\Insights\ClassComparisonRadarWidget::class, ['teamId' => $teamId, 'courseId' => $selectedCourseId], key('kpi_7_' . $selectedCourseId))
                    @break

                @case('kpi_8_dispensados')
                    <x-filament::section heading="Como analisar este gráfico" compact>
                        <p class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed text-justify">
                            O gráfico de rosca ilustra a proporção entre <strong class="text-highlight-success">alunos ativos que avaliam (verde)</strong> e <strong class="text-highlight-warning">alunos dispensados (amarelo)</strong>. Como o sistema libera as avaliações para todos os alunos ativos, um alto volume de dispensas prejudica a amostragem e compromete a precisão das estatísticas institucionais. É fundamental investigar o motivo real dessas dispensas — se há <strong class="text-highlight-danger">baixo engajamento</strong>, <strong class="text-highlight-danger">falha de comunicação</strong> ou falta de mobilização —, pois alunos ativos devem ser constantemente incentivados pela instituição a participar da avaliação.
                        </p>
                    </x-filament::section>
                    @livewire(\App\Filament\Widgets\Insights\DispensedStudentsDonutWidget::class, ['teamId' => $teamId], key('kpi_8_' . $teamId))
                    @break

                @case('kpi_9_correlacoes')
                    <x-filament::section heading="Como analisar este gráfico" compact>
                        <div class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 leading-relaxed text-justify space-y-2">
                            <p>
                                A matriz de correlação estatística de Pearson (coeficiente <em>r</em>) analisa o grau de associação entre as 6 dimensões pedagógicas. A leitura é feita cruzando duas dimensões nas linhas e colunas:
                            </p>
                            <ul class="list-disc pl-4 space-y-1.5">
                                <li>
                                    <strong class="text-highlight-success">Bom Resultado — Correlação Forte (Verde, 0,75 &le; <em>r</em> &le; 0,90)</strong>: Indica alta coerência entre os fatores. <em>Exemplo:</em> Professores com nota alta em Planejamento obtêm nota alta em Execução, comprovando que o preparo prévio se traduz em boa didática em sala. <em>(Atenção: como é quase impossível um docente ter desempenho rigorosamente idêntico em 6 aspectos distintos, valores &gt; 0,95 generalizados sinalizam o "Efeito Halo", onde o aluno votou por simpatia ou em piloto automático sem analisar criticamente cada dimensão)</em>.
                                </li>
                                <li>
                                    <strong class="text-highlight-warning">Resultado Moderado — Correlação Moderada (Amarelo, 0,40 &le; <em>r</em> &le; 0,74)</strong>: Indica associação parcial. <em>Exemplo:</em> Boa assiduidade e postura favorecem o clima de aula, mas não garantem sozinhos a clareza na avaliação.
                                </li>
                                <li>
                                    <strong class="text-highlight-danger">Resultado Ruim — Correlação Fraca ou Ausente (Vermelho, <em>r</em> &lt; 0,40)</strong>: Revela desconexão pedagógica entre dimensões. <em>Exemplo:</em> O docente possui excelente pontualidade/assiduidade, porém score muito baixo na execução de aulas ou avaliação, demonstrando que o simples cumprimento de horário não se converte em eficiência de ensino.
                                </li>
                            </ul>
                        </div>
                    </x-filament::section>
                    @livewire(\App\Filament\Widgets\Insights\DimensionCorrelationHeatmapWidget::class, ['teamId' => $teamId, 'courseId' => $selectedCorrelationScope], key('kpi_9_' . $selectedCorrelationScope))
                    @break
            @endswitch
        </div>
    </div>
</x-filament-panels::page>
