# 8. Anonimato na Exibição ao Docente e Isolamento IDOR

Date: 2026-06-09
Status: proposed

## Context

RNF-03 exige sigilo da autoria das respostas na exibição ao docente e isolamento de painéis. RSK-04 identifica risco médio de quebra de confidencialidade (professor ver nota de outro ou descobrir quem o avaliou). UC-02 permite manter vínculo técnico interno para deduplicação e auditoria.

**Trade-off:** Anonimato total (sem vínculo técnico) impossibilita deduplicar respostas; vínculo técnico com exibição anônima equilibra privacidade e integridade.

## Decision

1. **Anonimato na exibição:** O professor vê notas/médias agregadas, **sem identificação nominal dos alunos avaliadores** no painel e no PDF.
2. **Vínculo técnico interno:** O banco de dados mantém a relação `aluno → resposta → professor` para fins de deduplicação (impedir avaliação duplicada) e auditoria administrativa.
3. **Isolamento IDOR:** Cada panel do Filament opera com guard separado (ADR-001). Endpoints de dados validam ownership (`professor_id == user.id`). Tentativas de IDOR retornam 403 e são logadas.
4. **Trilha de auditoria:** Inclusões pontuais (UC-07) e liberações (UC-06) registram quem, quando e motivo.

## Consequences

- **Prós:** Privacidade preservada para o aluno; integridade dos dados (sem duplicatas); auditoria disponível para a Gerência; IDOR mitigado por design (multi-panel + policies).
- **Contras:** Se o banco de dados for comprometido, o vínculo técnico pode revelar autorias — mitigado por controle de acesso ao banco e backups cifrados.
- **Fora de escopo:** Criptografia de campo no banco (não justificada para o MVP, dado o volume e o contexto institucional).
