# 10. Ciclo de Vida da Conta e Bloqueio/Liberação para Avaliar

Date: 2026-06-09
Status: proposed

## Context

RF-10 define desativação automática de conta de aluno **3 anos** após a última evidência de vínculo acadêmico nos dados importados. RF-11 e UC-06 detalham o mecanismo de bloqueio para avaliar e fila de liberação (aprovação com motivo ≤255, indeferimento visível ao aluno, sem segundo pedido no mesmo ciclo).

RN-06 consolida: desativação por tempo + bloqueio + fila. O volume é baixo (casos pontuais por ciclo).

## Decision

1. **Desativação automática:** Job periódico via Laravel Scheduler (ex.: diário ou semanal) verifica se a última evidência de vínculo do aluno tem mais de 3 anos. Se sim, aplica **soft-delete** na conta (preserva histórico para auditoria).
2. **Bloqueio para avaliar:** Flag booleana ou estado na conta do aluno, verificado no login/painel (UC-02).
3. **Fila de liberação (UC-06):** Modelo `LiberationRequest` com estados `pending`, `approved`, `denied`. Aprovação e indeferimento com motivo obrigatório (≤255 caracteres). Aprovação válida apenas no ciclo atual. Indeferimento visível ao aluno, bloqueia novo pedido pelo app no mesmo ciclo.
4. **Auditoria:** Todas as transições (inclusão pontual, liberação, indeferimento) registram quem, quando, motivo, ciclo.

## Consequences

- **Prós:** Conformidade com RN-01/RN-06; soft-delete preserva auditoria e permite reativação manual; fila de liberação é simples e atende o modelo híbrido (app + atendimento externo).
- **Contras:** Job de desativação precisa de teste cuidadoso para não desativar contas ativas (edge cases com reimport); a lógica de "evidência de vínculo" precisa ser definida com precisão na Specify.
- **Decisões adiadas:** Definição exata de "evidência de vínculo" (presença no CSV mais recente? Matrícula ativa no SUAP?) — será refinada na Specify.
