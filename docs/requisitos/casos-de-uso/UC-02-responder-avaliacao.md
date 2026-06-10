# UC-02: Responder Avaliação Docente

## 1. Breve Descrição
Permite que o Aluno **acesse o sistema por link mágico** (RF-14) e responda ao questionário de avaliação para cada um dos professores associados às suas disciplinas no semestre atual, desde que **elegível**, com **conta ativa** e **sem bloqueio para avaliar** no ciclo.

## 2. Atores
- Aluno (Discente)

## 3. Pré-condições
- O período de avaliação deve estar aberto (configurado por perfil operacional — UC-09).
- O Aluno deve estar cadastrado na base de dados (via importação CSV e/ou inclusão pontual — UC-07).
- O Aluno deve possuir **vínculo ativo** no recorte do ciclo (RN-01), salvo se estiver coberto por **liberação válida** para o ciclo em curso (RF-11 / UC-06).
- A conta do aluno não pode estar **desativada** por política de tempo sem vínculo (RF-10), nem **bloqueada para avaliação** sem liberação pendente/concedida conforme RF-11.

## 4. Fluxo Principal (Happy Path)
1. O Aluno acessa a página de entrada do GeAD para discentes e informa seu **e-mail institucional**.
2. O Aluno solicita **“Enviar link de acesso”** (sem **rate limit** no MVP — RF-14 / RNF-04).
3. Se o e-mail corresponder a um discente **elegível** para receber link no estado atual, o sistema envia **um** e-mail com **link mágico** (token com **TTL padrão de 30 minutos** para consumo, salvo override na implantação — RNF-04). *(Mensagem de confirmação na tela pode ser **genérica** para reduzir enumeração de contas.)*
4. O Aluno abre o e-mail e aciona o link **antes do vencimento** do token.
5. O sistema valida o token, invalida conforme política de **uso único** (RNF-04), abre **sessão** autenticada (duração e inatividade conforme **RNF-04**) e aplica **RF-10** e **RF-11**.
6. Se o aluno estiver **bloqueado para avaliar** (RF-11), segue-se o **Fluxo Alternativo 2** no passo seguinte em vez do painel completo.
7. O painel exibe a lista das disciplinas/professores pendentes de avaliação para aquele aluno.
8. O Aluno seleciona um professor pendente e clica em "Avaliar".
9. O sistema carrega o formulário web (Filament) com as perguntas estáticas da avaliação institucional.
10. O Aluno preenche todas as perguntas obrigatórias e clica em "Enviar Avaliação".
11. O sistema persiste as respostas com **anonimato na visualização ao docente** (RNF-03); internamente pode manter vínculo técnico para deduplicação e auditoria.
12. O sistema marca a avaliação daquele professor como concluída para aquele aluno e retorna ao painel.

## 5. Fluxos Alternativos e Exceções

### Fluxo Alternativo 1: Todas as Avaliações Concluídas
1. No passo 7 do fluxo principal, o sistema verifica que o Aluno já avaliou todos os professores.
2. O sistema exibe mensagem de agradecimento e oculta o botão de "Avaliar".

### Fluxo Alternativo 2: Bloqueado para Avaliar (RF-11)
1. Após o passo 5, o sistema determina que o aluno está **bloqueado para avaliação** no ciclo.
2. O sistema exibe tela explicativa e a opção **Solicitar liberação** (UC-06).
3. O Aluno informa texto **obrigatório** com **no máximo 500 caracteres** (sem mínimo além de não vazio) e envia o pedido.
4. O fluxo de avaliação permanece indisponível até decisão da Gerência no mesmo ciclo.

### Exceção 1: E-mail não cadastrado, conta inativa ou fora da política
1. No passo 3, o sistema **não** envia link (ou trata de forma que não revele existência da conta — política anti-enumeração recomendada).
2. A tela pode exibir a mesma confirmação genérica (“Se o e-mail estiver cadastrado, você receberá um link.”).
3. Para tentativas com e-mail claramente inválido (formato), o sistema pode rejeitar imediatamente.

### Exceção 2: Link expirado, inválido ou já utilizado
1. No passo 4 ou 5, o token está fora do TTL, foi revogado ou já consumido.
2. O sistema nega a sessão e orienta o Aluno a **solicitar novo link** na página inicial.

### Exceção 3: Período de Avaliação Encerrado
1. Após autenticação, ou antes do envio do link conforme implementação, o sistema detecta que o período (UC-09) **não** está aberto.
2. O sistema impede o acesso ao painel de avaliação ou exibe que o período não está aberto.

### Exceção 4: Indeferimento no Mesmo Ciclo (RF-11)
1. Após indeferimento da solicitação de liberação, o sistema **não permite** novo pedido **pelo aplicativo** no **mesmo** ciclo.
2. O aluno visualiza o **motivo** cadastrado pela Gerência. Recurso ocorre por **canais oficiais externos** ou no **próximo** ciclo.

### Exceção 5: Sessão expirada (RNF-04)
1. Durante o uso do painel ou do formulário, a sessão atinge o limite de **8 horas**, ou **23:59:59** do dia civil no fuso do campus, ou **inatividade** de **2 horas**.
2. O sistema encerra a sessão e solicita **novo link mágico** para continuar (se o período de avaliação ainda estiver aberto).

## 6. Pós-condições
- As respostas estão salvas no banco de dados, prontas para consolidação, ou o aluno encontra-se em estado bloqueado/documentado conforme RF-11.
