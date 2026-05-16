# UC-06: Gerir Fila de Liberação para Avaliar

## 1. Breve Descrição
Centraliza em **uma única fila** os pedidos de **liberação para avaliar** quando o aluno encontra-se **bloqueado** por política (RF-11). O pedido pode surgir **pelo app** (aluno) ou ser **registrado pela Gerência** ao atender canal externo (**modelo híbrido**). Inclui **aprovação** (motivo obrigatório **≤255** caracteres, válido **só no ciclo atual**) ou **indeferimento** (motivo **obrigatório ≤255** e **visível** ao aluno). Envia **digest diário** com resumo de pendências (RF-13).

## 2. Atores
- Aluno (Discente)
- Gerente de Ensino
- TAE (apoio à Gerência)

## 3. Pré-condições
- Período de avaliação **aberto** para que alunos possam solicitar liberação quando bloqueados.
- Perfis operacionais autenticados com permissão RF-12 para tratar a fila.

## 4. Fluxo Principal (Aluno solicita)
1. Aluno em estado **bloqueado para avaliar** acessa fluxo descrito em UC-02 (Fluxo Alternativo 2).
2. Aluno preenche texto **obrigatório** com **no máximo 500 caracteres** (sem mínimo além de não vazio) e envia.
3. O sistema registra o pedido na **fila única** com status **Pendente**.

## 5. Fluxo Principal (Registro de atendimento externo — Gerente ou TAE)
1. Atendente registra na mesma fila um item iniciado fora do app (ex.: presencial, e-mail institucional recebido), vinculando o discente e o **ciclo**.
2. O item segue **Pendente** até decisão.

## 6. Fluxo Principal (Decisão — Gerente ou TAE)
1. Ator abre o módulo da fila e seleciona um pedido **Pendente**.
2. Ator escolhe **Aprovar** ou **Indeferir**.
3. **Se Aprovar:** informa **motivo obrigatório** (**máximo 255** caracteres, mínimo: não vazio).
4. **Se Indeferir:** informa **motivo obrigatório** (**máximo 255** caracteres, mínimo: não vazio), que será **exibido ao aluno** integralmente (até o limite).
5. O sistema registra decisão com **auditoria** (quem, quando, aluno, ciclo).
6. Se **Aprovado**: o aluno fica **liberado para avaliar apenas naquele ciclo** (RF-11).
7. Se **Indeferido**: o aluno **vê o motivo**; **não pode** abrir novo pedido pelo app **no mesmo ciclo** (recurso por canais externos ou próximo ciclo).

## 7. Fluxos Alternativos e Exceções

### Fluxo Alternativo 1: Digest diário (RF-13)
1. Em **dias úteis**, em **horário fixo** definido na implantação (ex.: variável de ambiente com **08:30** em **America/Fortaleza**), o sistema verifica se o **período de avaliação** está **aberto** (UC-09) **e** se existe **ao menos um** pedido **Pendente** na fila.
2. Se ambas as condições forem verdadeiras, o sistema envia **o mesmo** resumo por e-mail a **cada** usuário do painel que possua papel **Gerente de Ensino** ou **TAE** e **e-mail válido** cadastrado (evita manutenção manual de lista de destinatários).
3. O corpo inclui **quantidade de pendentes** e **links** para o painel da fila.
4. O **Gerente não configura** hora do digest nem a lista de destinos na interface; horário altera-se via **deploy/ambiente**; destinatários seguem o cadastro de usuários/papéis.

### Exceção 1: Tentativa de segundo pedido pelo app após indeferimento
1. O sistema bloqueia nova solicitação pelo app no mesmo ciclo e informa o aluno conforme UC-02.

## 8. Pós-condições
- Estados de bloqueio/liberação do aluno no ciclo refletem a decisão; trilhas de auditoria disponíveis para a Gerência.
