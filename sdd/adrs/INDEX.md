# CONSTITUTION — GeAD (Gestão de Desempenho Discente/Docente)

Índice de ADRs. Detalhes completos nos arquivos abaixo.

## ADRs

| # | Arquivo | Título | Status | Data |
|---|---------|--------|--------|------|
| 1 | [001-monolito-laravel-filament-v5.md](./001-monolito-laravel-filament-v5.md) | Monólito Laravel + Filament v5 (Multi-Panel) | accepted | 2026-06-09 |
| 2 | [002-infra-laravel-cloud-postgresql.md](./002-infra-laravel-cloud-postgresql.md) | Infraestrutura Laravel Cloud + PostgreSQL Gerenciado | accepted | 2026-06-09 |
| 3 | [003-autenticacao-link-magico.md](./003-autenticacao-link-magico.md) | Autenticação por Link Mágico (Sem Senha no MVP) | accepted | 2026-06-09 |
| 4 | [004-assinatura-digital-lote-govbr.md](./004-assinatura-digital-lote-govbr.md) | Assinatura Digital em Lote via Gov.br com Fallback Local | accepted | 2026-06-09 |
| 5 | [005-importacao-exclusiva-csv.md](./005-importacao-exclusiva-csv.md) | Importação Exclusiva via CSV (Sem API SUAP) | accepted | 2026-06-09 |
| 6 | [006-geracao-pdf-dompdf.md](./006-geracao-pdf-dompdf.md) | Geração de PDF Server-Side via DomPDF | accepted | 2026-06-09 |
| 7 | [007-fila-assincrona-queue-database.md](./007-fila-assincrona-queue-database.md) | Fila Assíncrona via Laravel Queue (Driver Database) | accepted | 2026-06-09 |
| 8 | [008-anonimato-isolamento-idor.md](./008-anonimato-isolamento-idor.md) | Anonimato na Exibição ao Docente e Isolamento IDOR | accepted | 2026-06-09 |
| 9 | [009-permissoes-gerente-tae-spatie.md](./009-permissoes-gerente-tae-spatie.md) | Modelo de Permissões Gerente × TAE (Spatie + Panels Separados) | accepted | 2026-06-09 |
| 10 | [010-ciclo-vida-conta-bloqueio-liberacao.md](./010-ciclo-vida-conta-bloqueio-liberacao.md) | Ciclo de Vida da Conta e Bloqueio/Liberação para Avaliar | accepted | 2026-06-09 |

## Notas

- Todas as ADRs aprovadas no gate em 2026-06-09.
- ADR-004 depende de PoC (RSK-03) para confirmar viabilidade técnica da integração Gov.br.
- ADR-001 define 4 panels Filament v5: Gerente, TAE, Aluno, Professor.
- Pacote idea-to-spec (v1.2) lido integralmente: `projeto.md` (§1–§13 + glossário), 9 UCs, matriz de rastreabilidade.
