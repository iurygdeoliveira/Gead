# 2. Infraestrutura Laravel Cloud + PostgreSQL Gerenciado

Date: 2026-06-09
Status: proposed

## Context

RNF-02 exige compatibilidade com cloud serverless para absorver picos sazonais (período de avaliação) e manter custo baixo na ociosidade (restante do semestre). O volume é modesto (≤10k req/dia com picos, ~600 alunos, 54 professores). A trajetória de 12 meses é estável.

O time é acadêmico e precisa de infra gerenciada que minimize ops. O Laravel oferece o **Laravel Cloud** como plataforma nativa de deploy, otimizada para o ecossistema.

**Forças:**
- Sazonalidade extrema (ativo por poucas semanas por semestre).
- Time sem equipe de operações dedicada.
- PostgreSQL oferece escalabilidade futura e features robustas (JSON, full-text search, advisory locks para filas).

## Decision

1. **Deploy:** Usar **Laravel Cloud** como plataforma de hospedagem.
2. **Banco de dados:** **PostgreSQL gerenciado** (via serviço integrado ao Laravel Cloud ou provedor externo como Neon/Supabase).
3. **Fila:** Driver `database` do Laravel Queue (reutiliza o PostgreSQL — ver ADR-007).

## Consequences

- **Prós:** Zero ops para o time; escala automática; ecossistema Laravel nativo (deploy via `git push`); PostgreSQL é robusto e bem suportado pelo Laravel.
- **Contras:** Custo recorrente do Laravel Cloud + PostgreSQL gerenciado (mesmo que baixo); dependência do ecossistema Laravel Cloud (lock-in leve, mitigável por Docker).
- **Decisões adiadas:** Provedor específico de PostgreSQL (integrado ao Laravel Cloud ou externo) será definido na implantação.
- **Fora de escopo:** Alta disponibilidade 24/7 — a operação é best-effort/horário comercial.
