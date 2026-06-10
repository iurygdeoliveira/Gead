# 6. Geração de PDF Server-Side via DomPDF

Date: 2026-06-09
Status: accepted

## Context

RF-03 e UC-03 exigem geração de relatório PDF institucional por professor, contendo médias consolidadas das avaliações do ciclo. O template é tabular simples (notas por critério, médias, cabeçalho institucional) — não requer gráficos complexos nem layout de alta fidelidade visual.

**Opções avaliadas:**
- DomPDF (via `barryvdh/laravel-dompdf`): PHP puro, sem dependência binária.
- Browsershot/Puppeteer: Alta fidelidade, mas exige Node.js + Chromium.
- TCPDF/FPDI: Controle fino, mas construção de template mais trabalhosa.
- wkhtmltopdf: Descontinuado, problemas em serverless.

## Decision

Usar **DomPDF** via pacote `barryvdh/laravel-dompdf` para geração de PDF, estruturado exatamente conforme o modelo padrão do IFTO Campus Araguaína:

1. **Template Blade (HTML/CSS) com Layout Oficial**:
   - **Cabeçalho**: Brasão da República centralizado, seguido pelos textos oficiais: *MINISTÉRIO DA EDUCAÇÃO*, *SECRETARIA DE EDUCAÇÃO PROFISSIONAL E TECNOLÓGICA*, *INSTITUTO FEDERAL DO TOCANTINS*, *CAMPUS ARAGUAÍNA* e *RESULTADO AVALIAÇÃO DISCENTE*.
   - **Metadados**: Campos estruturados para *Avaliação* (nome e intervalo de datas), *Matrícula* e *Docente* (nome do professor).
   - **Tabelas de Turmas**: Uma tabela independente para cada turma do docente que foi avaliada, contendo o nome da turma no topo, as médias (MC) dos 6 critérios estáticos de avaliação e uma linha de encerramento da tabela com o título: **PONTUAÇÃO DA TURMA (SOMATÓRIA DAS MÉDIAS DIVIDIDO POR DOIS)**.
   - **Resultado Final Consolidado**: Tabela destacada ao final com a linha **MÉDIA DA PONTUAÇÃO DAS TURMAS CONSOLIDADO- RESULTADO FINAL**.
   - **Rodapé de Assinatura**: Alinhamento à direita para local e data (ex: `Araguaína, [dia] de [mês] de [ano]`) e uma linha centralizada para a área do carimbo de assinatura digital do Gerente de Ensino.
2. **Cálculos Matemáticos na Consolidação**:
   - A *Média de cada Critério (MC)* é a média simples das respostas do critério daquela turma.
   - A *Pontuação da Turma (PT)* é calculada por: $\text{PT} = \frac{\sum_{i=1}^{6} MC_i}{2}$ (escala 0-30), com o resultado final da turma arredondado em 2 casas decimais. Os cálculos intermediários devem utilizar a precisão de ponto flutuante do banco de dados/PHP para evitar desvios cumulativos de arredondamento.
   - A *Média da Pontuação das Turmas Consolidado (Resultado Final)* é a média simples das PTs de todas as turmas válidas: $\text{Resultado Final} = \frac{\sum_{j=1}^{N} PT_j}{N}$.
3. **Geração síncrona**: No motor de consolidação (UC-03, Fase A) — volume baixo (~54 PDFs por ciclo).

## Consequences

- **Prós:** Zero dependência binária externa; integração nativa com Blade/Laravel; suficiente para layout tabular; pacote maduro e amplamente utilizado.
- **Contras:** Suporte limitado a CSS complexo (sem flexbox/grid completo); não adequado para gráficos SVG interativos (não necessário no escopo).
- **Decisões adiadas:** Nenhuma (o layout exato e regras matemáticas foram consolidados e aprovados conforme o modelo PDF).
- **Reversibilidade:** Alta — trocar para Browsershot exige apenas mudar o driver de renderização, não o template Blade.
