# 6. Geração de PDF Server-Side via DomPDF

Date: 2026-06-09
Status: proposed

## Context

RF-03 e UC-03 exigem geração de relatório PDF institucional por professor, contendo médias consolidadas das avaliações do ciclo. O template é tabular simples (notas por critério, médias, cabeçalho institucional) — não requer gráficos complexos nem layout de alta fidelidade visual.

**Opções avaliadas:**
- DomPDF (via `barryvdh/laravel-dompdf`): PHP puro, sem dependência binária.
- Browsershot/Puppeteer: Alta fidelidade, mas exige Node.js + Chromium.
- TCPDF/FPDI: Controle fino, mas construção de template mais trabalhosa.
- wkhtmltopdf: Descontinuado, problemas em serverless.

## Decision

Usar **DomPDF** via pacote `barryvdh/laravel-dompdf` para geração de PDF:

1. Templates escritos em **Blade** (HTML/CSS) → renderizados para PDF via DomPDF.
2. Layout institucional com cabeçalho, tabela de notas/médias e rodapé.
3. Geração síncrona no motor de consolidação (UC-03, Fase A) — volume baixo (54 PDFs).

## Consequences

- **Prós:** Zero dependência binária externa; integração nativa com Blade/Laravel; suficiente para layout tabular; pacote maduro e amplamente utilizado.
- **Contras:** Suporte limitado a CSS complexo (sem flexbox/grid completo); não adequado para gráficos SVG interativos (não necessário no escopo).
- **Decisões adiadas:** Layout exato do PDF (cabeçalho, rodapé, carimbo de assinatura) será definido na Specify/Build.
- **Reversibilidade:** Alta — trocar para Browsershot exige apenas mudar o driver de renderização, não o template Blade.
