# UC-09: Configurar Período de Avaliação

## 1. Breve Descrição
Permite que o **Gerente de Ensino** defina, no MVP, **somente** a **data de início** e a **data de término** da janela em que os discentes podem enviar avaliações (RN-08, RF-02). Outras parametrizações de calendário ficam fora do escopo mínimo.

## 2. Atores
- Gerente de Ensino

## 3. Pré-condições
- Gerente autenticado com permissões adequadas (RF-12, papel Gerente para esta parametrização).
- Ciclo corrente existente ou criado conforme modelo de dados da implementação.

## 4. Fluxo Principal (Happy Path)
1. O Gerente acessa a configuração do **período de avaliação** do ciclo.
2. O Gerente informa apenas a **data de início** e a **data de término** (sem hora). O sistema expande para **00:00:00** no primeiro dia e **23:59:59** no último dia, no **fuso do campus** configurado na implantação.
3. O Gerente salva.
4. O sistema valida que **início ≤ término** e persiste.
5. O sistema passa a considerar o período **aberto** ou **fechado** para alunos conforme a data corrente e esses limites **até** que um **encerramento operacional** (UC-03) bloqueie novas respostas antes ou na transição para consolidação.

## 5. Fluxos Alternativos e Exceções

### Exceção 1: Datas inválidas
1. Término anterior ao início ou campos obrigatórios vazios.
2. O sistema rejeita e solicita correção.

### Exceção 2: Tentativa por TAE
1. Se a política institucional reservar **apenas** ao Gerente a parametrização das datas, TAE não acessa esta tela (403).

## 6. Pós-condições
- Alunos elegíveis só respondem avaliações na janela configurada, salvo bloqueios RF-10/RF-11.
- O **digest** de fila (RF-13) opera em horário fixo de implantação e **somente** com período **aberto** nesses limites **e** pendências na fila, conforme UC-06.
