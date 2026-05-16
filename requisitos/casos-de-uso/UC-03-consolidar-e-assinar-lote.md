# UC-03: Consolidar Relatórios e Assinar em Lote

## 1. Breve Descrição
Permite que **TAE ou Gerente** encerrem o período de avaliação e acionem o **motor de consolidação** (cálculo das médias e geração dos PDFs). **Gerente e TAEs** podem **visualizar e baixar** os PDFs consolidados **antes** da assinatura. **Somente o Gerente de Ensino** aciona a **assinatura digital em lote** (Gov.br ou fallback).

## 2. Atores
- Gerente de Ensino
- TAE (apoio à Gerência)

## 3. Pré-condições
- Existem respostas de alunos salvas no banco de dados para os professores do ciclo.
- O ator possui permissões conforme RF-12.
- Para a fase de assinatura: o **Gerente** possui credenciais/configuração do serviço de assinatura digital.

## 4. Fluxo Principal (Happy Path)

### Fase A — Encerramento e consolidação (TAE ou Gerente)
1. O ator com permissão acessa o painel administrativo e aciona **Encerrar período de avaliação e consolidar**.
2. O sistema **bloqueia** novas avaliações (alunos não enviam mais respostas).
3. O sistema calcula médias/somatórias para cada professor com base nas respostas.
4. O sistema gera um PDF institucional por professor.
5. O sistema exibe listagem com status **Aguardando assinatura** e disponibiliza **visualização/download** dos PDFs para **Gerente e TAEs** (conferência).

### Fase B — Assinatura em lote (**somente Gerente**)
6. O **Gerente** aciona **Assinar todos (em lote)**.
7. O sistema integra-se com a API de assinatura (ex.: Gov.br), solicitando a rubrica eletrônica.
8. O sistema armazena os PDFs assinados e atualiza o status para **Assinado**, habilitando o envio por e-mail (UC-04).

## 5. Fluxos Alternativos e Exceções

### Exceção 1: Falha na Integração de Assinatura (API fora do ar)
1. Na Fase B, passo 7, a API não responde ou retorna erro.
2. O processo de assinatura em lote é interrompido; PDFs permanecem em **Aguardando assinatura**.
3. O sistema registra falha e permite nova tentativa (**Re-assinar lote**) ao **Gerente**.

### Exceção 2: Professor sem Avaliações
1. Na Fase A, passo 3, o sistema identifica professor sem respostas.
2. O sistema gera relatório indicando "Sem dados de avaliação no período" ou equivalente; demais docentes seguem o fluxo.
3. A política de assinar documento vazio/zerado fica a critério institucional (registro explícito na implementação).

### Exceção 3: Tentativa de assinatura por TAE
1. Se TAE tentar executar a Fase B, o sistema nega a ação (403) conforme RF-12.

## 6. Pós-condições
- PDFs consolidados existem para todos os docentes do escopo; após sucesso da Fase B, encontram-se **assinados** e prontos para **disparo em lote pelo Gerente** (UC-04).
