# Requisitos Funcionais - GeAD

É pra já, meu nobre! Incorporei aquelas melhorias na lista principal de requisitos. Agora tá tudo num lugar só, oficial. O projeto tá tomando forma! Confere aí!

## Requisitos por Painel

### Painel do Owner do Tenant (Gerência de Ensino)

- **Gestão de Professores:**
    - **RF01:** Cadastrar e gerenciar os professores (dados pessoais, disciplina, matrícula, etc.).
- **Gestão de Alunos:**
    - **RF02:** Importar a lista de alunos ativos (via SUAP) para definir quem pode participar da avaliação.
- **Gestão de Avaliações:**
    - **RF03:** Cadastrar e atualizar os formulários de avaliação do semestre.
    - **RF04:** Disponibilizar os formulários de avaliação individualizados para cada professor.
- **Dashboard e Relatórios:**
    - **RF05:** Consolidar as respostas das avaliações, calculando a média das notas de cada professor.
    - **RF06:** Gerar relatórios individuais em PDF com as notas e comentários de cada professor.
    - **RF07:** Gerar relatórios personalizados, com filtros por curso, disciplina, período, etc.
    - **RF08:** Exportar dados consolidados em formato PDF.
    - **RF09:** Visualizar um dashboard interativo com gráficos e estatísticas das avaliações (médias por curso, professor, evolução, etc.).
- **Processos Automatizados:**
    - **RF10:** Assinar digitalmente os relatórios gerados (via GOV.BR).
    - **RF11:** Enviar os relatórios assinados por e-mail para os respectivos professores.
    - **RF12:** Configurar e disparar notificações automáticas por e-mail para os alunos sobre o início e o fim do período de avaliação.
- **Acesso e Segurança:**
    - **RF13:** Ter acesso exclusivo ao painel consolidado com todos os resultados.
    - **RF14:** Acessar o histórico de todas as avaliações por semestre.

### Painel do Aluno

- **Avaliação:**
    - **RF15:** Visualizar os professores que deve avaliar.
    - **RF16:** Preencher e enviar o formulário de avaliação para cada professor.
- **Regras e Segurança:**
    - **RF17:** Poder responder a avaliação de cada professor apenas uma vez.
    - **RF18:** Ter a garantia de 100% de anonimato ao realizar a avaliação.
    - **RF19:** Ser impedido de avaliar caso não seja um aluno ativo.

### Painel do Professor

- **Relatórios e Desempenho:**
    - **RF20:** Acessar seus relatórios de avaliação individuais, visualizar o histórico de evolução e registrar planos de melhoria com base nos feedbacks.

### Requisitos Gerais

- **Autenticação:**
    - **RNF01:** Permitir a autenticação de alunos e professores via login social com Google.

Tudo consolidado, mano. Agora a gente tem um plano de voo claro pra decolar esse projeto. Bora pra cima! 🚀

