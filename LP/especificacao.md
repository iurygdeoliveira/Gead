# Especificação da Landing Page - Jet (Gestão de Desempenho de Docentes)

## 1. Visão Geral do Projeto
O GeAD é um sistema voltado para a automação e facilitação do processo de avaliação de docentes pelos discentes. Ele visa substituir o processo manual atual (que utiliza múltiplos Google Forms, planilhas Excel e envio manual de PDFs) por uma plataforma centralizada e automatizada.

## 2. Objetivo da Landing Page
A Landing Page servirá como a porta de entrada para o sistema GeAD, com o propósito de:
- Apresentar o sistema e seu propósito para a comunidade acadêmica.
- Fornecer acesso (Login) para os três perfis de usuários: Discentes, Docentes e Gerência de Ensino.
- Transmitir confiabilidade, segurança e modernidade.

## 3. Perfis de Usuário e Call-to-Action (CTA)
- **Discentes:** CTA focado em realizar a avaliação dos professores de forma rápida e segura. Acesso exclusivo via e-mail institucional.
- **Docentes:** CTA para visualizar o painel de resultados e feedbacks.
- **Gerência:** CTA para acessar o painel administrativo, gerenciar avaliações e relatórios.

## 4. Seções Propostas para a Landing Page
1. **Hero Section (Topo):**
   - Título claro e objetivo (ex: "Sistema Jet - Avaliação Docente").
   - Subtítulo explicando brevemente o propósito.
   - Botão de Login principal.
2. **Sobre o Sistema / Benefícios:**
   - Explicação de como o sistema automatiza e garante o sigilo do processo.
3. **Privacidade e Segurança:**
   - Destaque para o sigilo das informações e acesso restrito (apenas a Gerência tem visão geral, docentes veem apenas os próprios resultados).
4. **Rodapé:**
   - Identidade visual da instituição, suporte e links úteis.

## 5. Decisões de Design
1. **Objetivo da Landing Page:** Definido como um portal de acesso interno da instituição. O foco será explicar brevemente o sistema para a comunidade acadêmica e direcionar o login (via SUAP/Google), sem apelo comercial externo.
2. **Tom e Identidade Visual:** Abordagem moderna e dinâmica (estilo *startup*). O design será limpo, responsivo e premium, utilizando a cor verde como base, microinterações, *glassmorphism* e um aspecto fluido, de modo a promover uma adoção engajada, garantindo a logomarca oficial apenas para validação institucional.
3. **Estrutura e Layout:** Layout compacto e conciso (estilo *Split-Screen* / sem scroll longo). A interface exibirá os botões de acesso de forma imediata de um lado e, do outro, breves elementos visuais de marca e avisos de segurança. O objetivo é remover as barreiras de entrada.
4. **Copywriting e Privacidade:** Forte ênfase na Garantia de Anonimato. Um selo ou aviso destacado será exibido perto do login, assegurando que a autenticação serve apenas para validar a matrícula e que as respostas da avaliação são totalmente sigilosas e anônimas para os docentes.
5. **Autenticação e Botões de Acesso (CTA):** Utilização de um Botão Único central (Single Sign-On - SSO) do tipo "Entrar com SUAP / E-mail Institucional". A interface fica mais limpa e a lógica de redirecionamento (para painel do discente, docente ou gerência) será tratada no back-end, evitando cliques errados.
