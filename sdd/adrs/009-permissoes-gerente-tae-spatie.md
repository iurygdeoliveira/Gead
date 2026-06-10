# 9. Modelo de Permissões Gerente × TAE (Spatie Permission + Panels Separados)

Date: 2026-06-09
Status: proposed

## Context

RF-12 detalha com precisão as permissões:
- **TAE:** import/reimport, correções pontuais, inclusão pontual, fila de liberação, fechar período e consolidar, ver/baixar PDFs pré-assinatura, status/reenvio de e-mail.
- **Gerente:** tudo o que o TAE faz + **assinatura em lote** + **disparo em lote** de e-mails.

RSK-05 alerta para erro irreversível se papéis estiverem mal configurados (ex.: TAE acionar assinatura ou envio em lote por engano).

A decisão do projeto é ter **panels separados** no Filament v5 (ADR-001) — um para Gerente e outro para TAE.

## Decision

1. **Dois panels admin separados** no Filament v5: `/gerente` e `/tae`.
2. Permissões implementadas com **Spatie Permission** (`spatie/laravel-permission`) + **Laravel Policies/Gates**.
3. Dois roles fixos no sistema: `gerente` e `tae`.
4. Actions exclusivas do Gerente (assinar em lote, disparar em lote) **não existem** na UI do panel TAE — proteção by design, não apenas por policy.
5. Policies adicionais como segunda camada: mesmo que alguém acesse a rota diretamente, o Gate nega (defense in depth).

## Consequences

- **Prós:** Separação clara de responsabilidades; risco RSK-05 mitigado por design (ações exclusivas nem aparecem na UI do TAE); auditoria granular com Spatie.
- **Contras:** Dois panels admin significam alguma duplicação de Resources (ex.: listagem de professores aparece em ambos) — mitigável com Resources compartilhados e visibilidade condicional.
- **Decisões adiadas:** Se futuramente houver mais papéis (ex.: coordenador de curso), o modelo Spatie suporta adição sem refactor.
- **Fora de escopo:** Autenticação de Gerente/TAE (usa e-mail + senha nativo do Filament, não link mágico).
