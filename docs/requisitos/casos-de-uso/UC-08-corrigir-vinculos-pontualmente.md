# UC-08: Corrigir Vínculos Pontualmente

## 1. Breve Descrição
Permite que **Gerente** ou **TAE** corrijam **casos isolados** de vínculo turma/disciplina/professor **sem** remontar o semestre inteiro manualmente (RF-08). Correções manuais interagem com **reimportações** CSV conforme RF-09 (vide UC-01, Exceção 3).

## 2. Atores
- Gerente de Ensino
- TAE (apoio à Gerência)

## 3. Pré-condições
- Dados base importados (UC-01) ou discente já existente com estrutura mínima.
- Permissão RF-12.

## 4. Fluxo Principal (Happy Path)
1. O ator localiza o registro incorreto (busca por turma, professor ou discente).
2. O ator ajusta o vínculo pontualmente (ex.: troca de docente, correção de turma).
3. O sistema marca o registro como **ajustado manualmente** (ou equivalente técnico) para participar da lógica de **conflito** em reimports.
4. O sistema persiste e confirma a alteração.

## 5. Fluxos Alternativos e Exceções

### Exceção 1: Conflito com futuro CSV
1. Tratado em UC-01, Exceção 3 — não há sobrescrita silenciosa.

## 6. Pós-condições
- Avaliações futuras e listagens do aluno refletem o vínculo corrigido; reimports exigem decisão explícita se divergirem.
