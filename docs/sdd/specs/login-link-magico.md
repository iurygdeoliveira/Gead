# Login por Link Mágico Integrado (GeaD)

## Contexto
Visando simplificar o acesso para os Alunos e Docentes avaliados (público de maior volume) e garantir a segurança das contas administrativas (Gerência de Ensino e TAEs), precisamos de uma solução híbrida e reativa na tela de login. O fluxo alternará entre login sem senha (Link Mágico) para os domínios institucionais de alunos e docentes (`@estudante.ifto.edu.br` e `@ifto.edu.br`) e o tradicional login por senha para os administradores. Esta implementação visa uma experiência sem atrito e sem necessidade de plugins externos modais, integrada diretamente no componente de login do Filament.

## OBJETIVO
Implementar um fluxo de login reativo na tela padrão do Filament que alterne entre "Link Mágico" e "Senha".
- Entregar um envio seguro de token de uso único (15 minutos) via notificação por e-mail.
- Validar se o domínio do e-mail pertence aos alunos ou docentes.
- Bloquear a utilização do Link Mágico para usuários com o papel (role) de `Admin`, obrigando-os a usar senha.
- Validar se o usuário está pré-cadastrado no sistema.
- Associar automaticamente o usuário logado via link mágico (caso não tenha um Team) ao Team único do sistema, "Campus Araguaína" (que preverá também o campo CNPJ na modelagem), e definir as flags de aprovação.

## Critérios de aceite
- **Dado** que um Aluno ou Docente acesse a tela de login, **quando** inserir um e-mail válido institucional e clicar em enviar, **então** o sistema deve gerar um token de 15 minutos, enviar por e-mail e exibir uma mensagem de sucesso reativa sem recarregar a página.
- **Dado** que um usuário tente usar um e-mail com domínio não autorizado ou não cadastrado, **quando** submeter o formulário de link mágico, **então** uma mensagem de validação de erro deve ser exibida no respectivo campo.
- **Dado** que um usuário com a role `Admin` tente solicitar o link mágico, **quando** submeter o formulário, **então** o sistema deve retornar um erro no campo instruindo-o a utilizar o login por senha.
- **Dado** que um usuário acesse o link mágico recebido no e-mail, **quando** o token for válido e não expirado, **então** ele deve ser autenticado, ativado (flags de aprovação e verificação), associado à role `User` e ao Team "Campus Araguaína", e redirecionado para o dashboard.
- **Dado** que o link mágico já tenha sido consumido ou expirado, **quando** acessado na URL de callback, **então** o sistema deve redirecionar à tela de login com erro alertando sobre a invalidade do link.
- **Dado** que um Gerente ou TAE utilize a opção de login por senha, **quando** inserir credenciais válidas, **então** o sistema deve autenticá-lo pelo fluxo tradicional do Filament.
