# UC-07: Inclusão Pontual de Discente

## 1. Breve Descrição
Permite que **Gerente** ou **TAE** incluam **manualmente** um discente que **não** veio no CSV do período, **sem teto** de quantidade por ciclo (decisão de produto), mas com **motivo obrigatório** limitado a **255 caracteres** (RF-07, RN-07) para fins de auditoria.

## 2. Atores
- Gerente de Ensino
- TAE (apoio à Gerência)

## 3. Pré-condições
- Ator autenticado com permissão RF-12.
- Ciclo de avaliação configurado; dados mínimos do discente disponíveis (incluindo e-mail institucional).

## 4. Fluxo Principal (Happy Path)
1. O ator acessa "Inclusão pontual de discente".
2. O ator informa os dados necessários (nome, e-mail institucional, vínculos mínimos ao ciclo conforme modelo de dados).
3. O ator preenche **motivo obrigatório** (≤ **255** caracteres).
4. O sistema valida duplicidade e regras de e-mail.
5. O sistema persiste o discente e registra **auditoria** (ator, timestamp, motivo, ciclo).
6. O sistema confirma sucesso na tela.

## 5. Fluxos Alternativos e Exceções

### Exceção 1: Duplicidade ou e-mail inválido
1. O sistema rejeita a inclusão com mensagem clara.
2. O ator corrige e reenvia.

## 6. Pós-condições
- O discente passa a poder autenticar-se (sujeito a RN-01, RF-10 e RF-11) e aparecer nas listas de avaliação conforme vínculos.
