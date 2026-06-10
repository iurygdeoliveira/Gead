# 5. Importação Exclusiva via CSV (Sem API SUAP)

Date: 2026-06-09
Status: proposed

## Context

RES-01 é explícita e classificada como **one-way door**: o SUAP local não permite integração via API para consumo direto. O CSV é a **única via** de entrada dos dados acadêmicos (alunos, professores, vínculos turma/disciplina).

O reimport com conflitos (RF-09, UC-01 Exceção 3) já está detalhado nos artefatos: ajustes manuais prévios não são sobrescritos silenciosamente; o sistema pausa e exige resolução item a item.

**Forças:**
- Restrição técnica do SUAP (sem API disponível).
- Volume baixo (importação semestral, ~600 alunos + 54 professores).
- Necessidade de correções pontuais pós-import (UC-07, UC-08).

## Decision

1. A entrada de dados acadêmicos será **exclusivamente via upload de arquivos CSV** gerados a partir do SUAP.
2. O formato CSV será **documentado** (colunas esperadas, encoding, delimitador).
3. Reimport com detecção de conflitos: registros ajustados manualmente são marcados e exigem decisão explícita por item antes de persistir.
4. **Não investir** em integração via API SUAP no MVP nem no horizonte de 12 meses.

## Consequences

- **Prós:** Implementação simples; sem dependência de API externa para dados acadêmicos; processo já familiar à Gerência (exportar CSV do SUAP).
- **Contras:** Operação manual semestral (upload + resolução de conflitos); erros de formato no CSV podem gerar retrabalho; sem sincronização automática com o SUAP.
- **Irreversibilidade:** One-way door — se o SUAP liberar API no futuro, uma nova ADR deverá ser criada para avaliar a migração.
- **Fora de escopo:** Definição das colunas exatas do CSV (será feita na Specify).
