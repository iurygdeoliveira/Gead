# UC-05: Visualizar Painel de Resultados

## 1. Breve Descrição
Permite que o **Professor** acesse **por link mágico** (mesmo padrão do aluno — **RF-14**, **RNF-04**) e visualize **exclusivamente** o seu próprio resultado (dashboard e PDF consolidado). **Gerente de Ensino** e **TAEs** utilizam o **painel administrativo** (autenticação própria do Filament/backoffice, fora do escopo deste UC) para a visão **institucional**, conforme RF-12. Assegura confidencialidade entre docentes (IDOR).

## 2. Atores
- Professor
- Gerente de Ensino
- TAE (apoio à Gerência)

## 3. Pré-condições
- Dados de avaliação e, quando aplicável, PDFs do ciclo disponíveis nos níveis de permissão corretos.
- Para o **Professor:** cadastro com **e-mail institucional** presente na base (ex.: importação UC-01).

## 4. Fluxo Principal (Painel do Professor — Happy Path)
1. O Professor acessa a **página de entrada do GeAD para docentes** e informa seu **e-mail institucional**.
2. O Professor solicita **“Enviar link de acesso”** (sem rate limit no MVP — **RF-14** / **RNF-04**).
3. Se o e-mail corresponder a um **docente** cadastrado, o sistema envia **um** e-mail com **link mágico** (token, **TTL padrão 30 minutos** — **RNF-04**). *(Confirmação na tela pode ser **genérica** anti-enumeração.)*
4. O Professor aciona o link **antes do vencimento** do token.
5. O sistema valida o token (**uso único** conforme **RNF-04**), abre **sessão** (duração e inatividade — **RNF-04**) e redireciona ao **Dashboard pessoal** do professor.
6. O sistema exibe o histórico de avaliações consolidadas por semestre/ciclo.
7. O Professor seleciona o ciclo desejado.
8. O sistema apresenta indicadores globais das suas notas (sem identificar nominalmente os alunos avaliadores).
9. O Professor clica em "Baixar Relatório Oficial" quando existir PDF assinado.
10. O sistema disponibiliza o download do PDF correspondente.

## 5. Fluxo Principal (Painel da Gerência — Gerente ou TAE)
1. O ator com perfil institucional autentica-se no **painel administrativo** (mecanismo nativo do backoffice — ex.: Filament) e acessa o Dashboard Institucional.
2. O sistema exibe métricas gerais (engajamento, pendências) quando implementadas.
3. O ator acessa a área **Resultados por Professor**.
4. O sistema lista docentes com notas/status (incl. assinatura e envio quando existirem).
5. O ator pode abrir/baixar o PDF consolidado **de qualquer professor** nas fases em que RF-12 permitir (ex.: conferência pós-consolidação até envio).

## 6. Fluxos Alternativos e Exceções

### Exceção 1: Tentativa de Acesso Indevido (IDOR)
1. O Professor altera URL tentando acessar painel de terceiros.
2. O sistema compara o dono do recurso com o usuário autenticado.
3. O sistema retorna **403**, registra tentativa e encerra.

### Exceção 2: Token, sessão ou e-mail (espelha UC-02 / RNF-04)
1. **Link expirado, inválido ou já utilizado:** negar sessão; orientar novo pedido de link.
2. **Sessão expirada** (8 h, fim do dia civil ou inatividade 2 h): encerrar sessão; novo link para continuar.
3. **E-mail não cadastrado / formato inválido:** tratamento **anti-enumeração** ou validação de formato, conforme política alinhada ao UC-02.

## 7. Pós-condições
- Cada ator consome dados conforme seu escopo (RF-06, RF-12), preservando confidencialidade entre professores.
