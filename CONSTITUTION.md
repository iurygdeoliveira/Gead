# CONSTITUTION — GeAD (Gestão de Desempenho Discente/Docente)

Índice de ADRs. Detalhes completos em `sdd/adrs/`.

## ADRs

| # | Arquivo | Título | Status | Data |
|---|---------|--------|--------|------|
| 1 | [001-monolito-laravel-filament-v5.md](./sdd/adrs/001-monolito-laravel-filament-v5.md) | Monólito Laravel + Filament v5 (Multi-Panel) | proposed | 2026-06-09 |
| 2 | [002-infra-laravel-cloud-postgresql.md](./sdd/adrs/002-infra-laravel-cloud-postgresql.md) | Infraestrutura Laravel Cloud + PostgreSQL Gerenciado | proposed | 2026-06-09 |
| 3 | [003-autenticacao-link-magico.md](./sdd/adrs/003-autenticacao-link-magico.md) | Autenticação por Link Mágico (Sem Senha no MVP) | proposed | 2026-06-09 |
| 4 | [004-assinatura-digital-lote-govbr.md](./sdd/adrs/004-assinatura-digital-lote-govbr.md) | Assinatura Digital em Lote via Gov.br com Fallback Local | proposed | 2026-06-09 |
| 5 | [005-importacao-exclusiva-csv.md](./sdd/adrs/005-importacao-exclusiva-csv.md) | Importação Exclusiva via CSV (Sem API SUAP) | proposed | 2026-06-09 |
| 6 | [006-geracao-pdf-dompdf.md](./sdd/adrs/006-geracao-pdf-dompdf.md) | Geração de PDF Server-Side via DomPDF | proposed | 2026-06-09 |
| 7 | [007-fila-assincrona-queue-database.md](./sdd/adrs/007-fila-assincrona-queue-database.md) | Fila Assíncrona via Laravel Queue (Driver Database) | proposed | 2026-06-09 |
| 8 | [008-anonimato-isolamento-idor.md](./sdd/adrs/008-anonimato-isolamento-idor.md) | Anonimato na Exibição ao Docente e Isolamento IDOR | proposed | 2026-06-09 |
| 9 | [009-permissoes-gerente-tae-spatie.md](./sdd/adrs/009-permissoes-gerente-tae-spatie.md) | Modelo de Permissões Gerente × TAE (Spatie + Panels Separados) | proposed | 2026-06-09 |
| 10 | [010-ciclo-vida-conta-bloqueio-liberacao.md](./sdd/adrs/010-ciclo-vida-conta-bloqueio-liberacao.md) | Ciclo de Vida da Conta e Bloqueio/Liberação para Avaliar | proposed | 2026-06-09 |

## Notas

- Todas as ADRs estão em status `proposed` até aprovação no gate.
- ADR-004 depende de PoC (RSK-03) para confirmar viabilidade técnica da integração Gov.br.
- ADR-001 define 4 panels Filament v5: Gerente, TAE, Aluno, Professor.
- Pacote idea-to-spec (v1.2) lido integralmente: `projeto.md` (§1–§13 + glossário), 9 UCs, matriz de rastreabilidade.
