# Projeto: GeAD (Gestão de Desempenho Discente/Docente)

> Documento núcleo gerado pela skill idea-to-spec; enriquecido após sessão **grill-me** (ciclo de elegibilidade, operação SUAP/CSV e papéis institucionais).
> Versão: 1.2 | Data: 2026-05-16 | Autor: Iury / GeAD Team

## Sumário Executivo
O projeto GeAD visa digitalizar e automatizar o ciclo semestral de avaliação de professores pelos alunos. A solução substituirá processos exaustivos (Google Forms manuais, consolidação via planilhas e assinatura de PDFs um a um) por um sistema centralizado, capaz de importar dados do SUAP via CSV, coletar as respostas, calcular médias, assinar digitalmente os relatórios em lote e enviá-los diretamente aos professores, devolvendo capacidade estratégica para a Gerência de Ensino.

## 1. Visão e Contexto de Negócio
- **Objetivo de negócio (OST):** Reduzir em 90% o tempo operacional da Gerência de Ensino gasto no ciclo de avaliação docente (da criação dos questionários até o envio dos resultados assinados), garantindo a viabilidade de execução do processo a cada semestre.
- **Problema central:** O processo atual é integralmente manual, exigindo criar forms por turma/professor, consolidar respostas no Excel, gerar PDFs e assinar um a um. Devido a esse gargalo, a avaliação nem sequer ocorreu no semestre passado.
- **Razão de agora (urgência):** O acúmulo de trabalho braçal paralisa a área e impossibilita a entrega do resultado da avaliação.
- **Stakeholders:** Gerente de Ensino, TAEs (apoio operacional à Gerência), Professores (avaliados), Alunos (avaliadores).

## 2. Métricas
| ID | Métrica | Baseline | Meta | Prazo |
|---|---|---|---|---|
| M-01 | Tempo gasto no ciclo de fechamento/consolidação | Semanas/Dias ou Abandono | < 1 dia de trabalho | Lançamento V1 |
| M-02 | Grau de automação do disparo de resultados | 0% (Manual 1 a 1) | 100% (Em Lote via 1 clique) | Lançamento V1 |

## 3. Personas
| ID | Persona | Contexto | Dor principal | Comportamento atual |
|---|---|---|---|---|
| P-01 | Gerente de Ensino (ex: Isaac) | Profissional que gerencia o ciclo institucional de avaliações. | Sobrecarga extrema com tarefas manuais repetitivas que inviabilizam o seu trabalho estratégico. | Tenta usar ferramentas avulsas (Google Forms, planilhas), mas não consegue fechar o ciclo pelo alto volume (54 profs). |
| P-02 | TAE (apoio à Gerência) | Dois técnicos administrativos que operam importações, filas e conferências. | Volume operacional (conflitos de import, alunos fora do export, e-mail com falha). | Executam o dia a dia; o Gerente retém atos de assinatura/disparo em lote. |

## 4. Dores Mapeadas
| ID | Dor | Persona afetada | Evidência (MOM Test) |
|---|---|---|---|
| D-01 | Processo manual e exaustivo para criar forms, consolidar, gerar PDF, assinar e enviar. | Gerente de Ensino | No último semestre, devido ao volume de trabalho, a Gerência ficou sem tempo hábil e a avaliação não foi realizada. |

## 5. Como resolver as dores mapeadas
> Ponte entre dor e RF: **abordagem** e **entregáveis de alto nível** (não substitui RFs).

| ID | Dor(es) | Abordagem (produto/processos) | Entregáveis de alto nível (capacidades) | MoSCoW |
|----|---------|----------------------------------|------------------------------------------|--------|
| R-01 | D-01 | Automação centralizada do fluxo de coleta e consolidação. | 1. Módulo de importação de professores, alunos e turmas via CSV do SUAP (Setup), com reimport e tratamento de conflitos. | Must |
| R-02 | D-01 | Automação centralizada do fluxo de coleta e consolidação. | 2. Formulário web padrão (Filament) para alunos avaliarem os professores vinculados. | Must |
| R-03 | D-01 | Automação centralizada do fluxo de coleta e consolidação. | 3. Motor de consolidação de médias com geração de relatório PDF institucional. | Must |
| R-04 | D-01 | Automação centralizada do fluxo de coleta e consolidação. | 4. Interface para assinatura digital de PDFs em lote e disparo por e-mail em um clique. | Must |
| R-05 | D-01 | Automação e governança de cadastro. | 5. Inclusão pontual de discentes, correção pontual de vínculos, ciclo de conta (incl. bloqueio para avaliar) e fila de liberação pela Gerência. | Must |

## 6. Riscos de Produtos
| ID | Domínio | Descrição | Severidade | Experimento de validação |
|---|---|---|---|---|
| RSK-01 | Valor | Sistema exigir mais esforço de configuração inicial que os forms atuais. | Alto | Setup projetado com importação via CSV do SUAP de forma massiva + correções pontuais. |
| RSK-02 | Usabilidade | Baixa adesão dos alunos por dificuldade de uso em relação ao Google Forms; e-mail do link mágico não entregue (quarentena). | Médio | Form web simples via Filament, **RF-14** com mensagens claras, layout mobile-friendly; monitorar entregabilidade (SPF/DKIM) na implantação. |
| RSK-03 | Técnico | Falha ou inviabilidade técnica na assinatura digital em lote dos relatórios com validade institucional. | Alto | Criar PoC antecipada focada na geração do PDF e integração da assinatura via Gov.br (ou fallback). |
| RSK-04 | Negócio | Quebra de confidencialidade (professor ver nota de outro ou descobrir quem o avaliou). | Médio | Implementar controle rígido (IDOR checks) nos painéis e anonimato na listagem das notas. |
| RSK-05 | Operação | Papéis Gerente vs TAE mal configurados geram erro irreversível (fecho ou envio indevido). | Médio | Matriz de permissões mínima em RF-12 + testes com ambos os perfis. |
| RSK-06 | Segurança / Infra | **Sem rate limit** nas solicitações de link mágico no MVP (**aluno e professor**): risco de abuso (fila de e-mail, custo, DoS leve). | Médio | Monitorar volume na implantação; considerar throttling em versão futura se necessário. |
| Dimensão | Valor declarado | Implicação para RNFs |
|---|---|---|
| Tamanho e composição do time | Pequeno (Alunos/Acadêmico, 2-4 pessoas) | Código monolítico simplificado (Laravel + Filament). Evitar microsserviços. |
| Equipe institucional | 1 Gerente de Ensino + 2 TAEs | Permissões distintas: ver RF-12. |
| Capacidade operacional | Best-effort / Horário comercial | Sem necessidade de alta disponibilidade 24/7 on-call severa. |
| Volume esperado no 1º ano | ≤500 a 10k req/dia (com picos) | Solução Cloud Serverless atende bem à sazonalidade e ociosidade no resto do semestre. |
| Trajetória 12 meses | Estável (~600 alunos, 54 profs) | O banco de dados relacional clássico suprirá o volume sem gargalos de escala massiva. |

## 8. Requisitos Funcionais
| ID | Requisito | Critérios de aceite | MoSCoW | Dor |
|---|---|---|---|---|
| RF-01 | Importação de setup via CSV do SUAP | Perfil operacional (Gerente ou TAE) faz upload de CSV; o sistema cadastra alunos, profs e turmas com e-mail institucional obrigatório; reimport com conflitos conforme RF-09. | Must | D-01 |
| RF-02 | Formulário de avaliação web (Filament) | O **Gerente** define **apenas** a **data de início** e a **data de término** do período em que alunos podem avaliar; o aluno autentica-se conforme **RF-14** e, se elegível no ciclo e não bloqueado para avaliar, avalia apenas seus professores **enquanto** o período estiver aberto (entre esses limites). | Must | D-01 |
| RF-03 | Motor de consolidação e geração de PDF | O sistema processa as respostas de todos os alunos e gera os relatórios em PDF por professor. | Must | D-01 |
| RF-04 | Assinatura digital em lote | A partir da lista de relatórios, **somente o Gerente de Ensino** aciona assinatura em lote integrando API. | Must | D-01 |
| RF-05 | Envio de e-mails em lote | **Somente o Gerente** dispara o envio em lote; o sistema anexa o PDF por professor; **TAEs** podem consultar status e **reenviar** falhas. | Must | D-01 |
| RF-06 | Controle de Acesso e Painéis | Professor vê só o dele (**autenticação** igual ao discente — **RF-14** / **RNF-04**). Gerente e TAEs veem visão institucional conforme RF-12. Sistema impede IDOR. | Must | D-01 |
| RF-07 | Inclusão pontual de discente | Sem teto por ciclo; cada inclusão exige **motivo obrigatório** (máx. **255** caracteres) e auditoria (quem/quando). | Must | D-01 |
| RF-08 | Correção pontual de vínculos | Gerente ou TAE ajusta casos isolados (troca de professor, turma errada) sem remontar o semestre inteiro na mão. | Must | D-01 |
| RF-09 | Reimport com conflitos | Se reimport divergir de registros ajustados manualmente, o fluxo **pausa** e exige **resolução item a item** antes de concluir. | Must | D-01 |
| RF-10 | Ciclo de vida da conta do aluno | Conta desativada automaticamente **3 anos** após a **última evidência de vínculo** acadêmico nos dados oficiais importados; enquanto houver vínculo renovado semestralmente no recorte, o relógio não expira por essa regra. | Must | D-01 |
| RF-11 | Bloqueio para avaliar e fila de liberação | Aluno elegível pode estar **bloqueado para avaliar**; fila única para pedido (app ou registro de atendimento externo); liberação **válida só no ciclo atual**; indeferimento com motivo **visível** (máx. **255** caracteres); sem novo pedido pelo app no mesmo ciclo após indeferimento; **aprovação** com motivo obrigatório (**máx. 255** caracteres). Pedido pelo aluno: texto **obrigatório**, máx. **500** caracteres, **sem mínimo**. | Must | D-01 |
| RF-12 | Permissões Gerente × TAE | **TAE:** import/reimport (com resolução de conflitos), correções pontuais, inclusão pontual, fila de liberação, **fechar período e consolidar**, ver/baixar PDFs consolidados na etapa pré-assinatura, status/reenvio de e-mail. **Gerente:** tudo o que o TAE faz **quando aplicável** + **assinatura em lote** + **disparo em lote** de e-mails. | Must | D-01 |
| RF-13 | Notificação digest | E-mail **resumo diário** em **horário fixo** definido na **implantação** (variável de ambiente, ex.: **08:30** no fuso **America/Fortaleza**), **não** configurável pelo Gerente; enviado **somente se** houver pedidos pendentes na fila e, **enquanto** o período de avaliação (RF-02) estiver **aberto**. **Destinatários:** todos os usuários cadastrados no painel com papel **Gerente de Ensino** ou **TAE** (evita lista fixa em variável de ambiente quando o time mudar). Corpo com quantidade de pendentes e links ao painel. | Should | D-01 |
| RF-14 | Autenticação por link mágico (discente e docente) | **Sem senha no MVP** para **aluno** e **professor** no respectivo portal público: o usuário informa o **e-mail institucional** cadastrado (importação CSV); o sistema valida existência, envia **um** e-mail com **link assinado** (token); ao acessar o link dentro da **validade** do token (**padrão MVP: 30 minutos**, configurável na implantação), obtém **sessão** regida por **RNF-04**. **Sem rate limit** na frequência de novos pedidos de link no MVP (aceite institucional; risco em **RSK-06**). | Must | D-01 |

## 9. Requisitos Não Funcionais
| ID | Categoria | Requisito | Métrica | Âncora na seção 7 |
|---|---|---|---|---|
| RNF-01 | Arquitetura | Monólito simplificado (Laravel/Filament). | N/A | Tamanho e composição do time |
| RNF-02 | Infraestrutura | Compatibilidade com Cloud Serverless para absorver picos de tráfego (avaliação). | N/A | Volume e Trajetória |
| RNF-03 | Segurança | Sigilo da autoria das respostas **na exibição ao docente**; isolamento de painéis; trilhas de auditoria para inclusões e liberações. | Testes IDOR 100% OK; logs de liberação/inclusão | RSK-04 |
| RNF-04 | Segurança (link mágico — discente e docente) | **Link mágico:** token **uso único** ou invalidação após primeiro sucesso; **TTL para consumir o link: padrão 30 minutos** (override por ambiente). **Sessão após login:** expira no **primeiro** instante entre (i) **8 horas** após autenticação bem-sucedida e (ii) **23:59:59** do **mesmo dia civil** no fuso do campus; além disso, **encerra por inatividade** após **2 horas** sem requisições (valores ajustáveis via ambiente). **Não** há **rate limit** nas solicitações de envio de link no MVP. | Testes de expiração, inatividade e reutilização de token | RF-14 |

## 10. Regras de Negócio
| ID | Regra | UCs aplicáveis |
|---|---|---|
| RN-01 | Restrição institucional | Acesso e avaliação somente para discentes com **vínculo ativo** no recorte do ciclo, salvo inclusão pontual (RF-07) ou liberação válida no ciclo (RF-11). E-mail institucional obrigatório. | UC-01, UC-02, UC-07 |
| RN-02 | Período fechado | Encerramento do período de coleta é feito por **perfil operacional** (TAE ou Gerente). Após o encerramento acionado conforme UC-03, nenhuma avaliação nova é recebida. | UC-02, UC-03 |
| RN-03 | Confidencialidade docente | PDF e painel de um professor X são sigilosos para outros professores. | UC-05 |
| RN-04 | Reimport e conflitos | Ajustes manuais conflitantes com novo CSV não são sobrescritos automaticamente; exige decisão explícita por item. | UC-01 |
| RN-05 | Liberação para avaliar | Liberação manual vale **apenas no ciclo atual**. Indeferimento exibe motivo ao aluno. Não há segundo pedido pelo app no mesmo ciclo após indeferimento. | UC-06 |
| RN-06 | Conta e bloqueio | Regras de desativação por **3 anos** sem nova evidência de vínculo; bloqueio para avaliar pode exigir fila (RF-11). | UC-02, UC-06 |
| RN-07 | Textos obrigatórios | Inclusão pontual: motivo ≤255. Pedido de liberação (aluno): texto obrigatório ≤500, sem mínimo. Motivos de **aprovação** e **indeferimento** na fila (UC-06): obrigatórios, **cada um ≤255** caracteres; indeferimento **visível** ao aluno. | UC-06, UC-07 |
| RN-08 | Parâmetros do período de avaliação | No MVP, a janela de coleta é definida **exclusivamente** por **data de início** e **data de término** (sem campo de hora na interface) informadas pelo **Gerente**. A implementação normaliza **início** para **00:00:00** e **término** para **23:59:59** no **fuso horário do campus** (ex.: `America/Fortaleza`). O digest (RF-13) não altera essas datas. | UC-09, UC-02, UC-06 |

> **Casos de uso:** ver `casos-de-uso/README.md` e arquivos `UC-xx-*.md`
>
> **MVP:** ver `releases/release-01.md`

## 11. Restrições
| ID | Descrição | Reversibilidade | Justificativa |
|---|---|---|---|
| RES-01 | Importação primária via CSV | One-way door | O SUAP local não permite integração via API para consumo direto. É a única via. |

## 12. Matriz de Rastreabilidade
| Dor | RF | RNF | UC |
|---|---|---|---|
| D-01 | RF-01 | RES-01 | UC-01 |
| D-01 | RF-02 | RNF-01 | UC-02, UC-09 |
| D-01 | RF-14 | RNF-04 | UC-02, UC-05 |
| D-01 | RF-03 | RNF-02 | UC-03 |
| D-01 | RF-04 | RNF-01 | UC-03 |
| D-01 | RF-05 | RNF-01 | UC-04 |
| D-01 | RF-06 | RNF-03 | UC-05 |
| D-01 | RF-07, RF-08, RF-09 | RNF-03 | UC-01, UC-07, UC-08 |
| D-01 | RF-10, RF-11 | RNF-03 | UC-02, UC-06 |
| D-01 | RF-12 | RNF-03 | UC-03, UC-04, UC-05 |
| D-01 | RF-13 | RNF-02 | UC-06, UC-09 |

## 13. Log de Auditoria (Maker-Checker)
| Iteração | Itens reprovados | Correções |
|---|---|---|
| 1 | Nenhum item reprovado na avaliação manual em conformidade com as respostas do usuário. | N/A |
| 2 | Lacunas de elegibilidade, reimport, papéis Gerente/TAE e fila de liberação levantadas na sessão grill-me. | Versão 1.1: novos RF-07–RF-13, RNs 04–07, UCs 06–08 e ajustes nos UCs 01–05. |
| 3 | Digest (horário fixo) e período de avaliação (só início/fim pelo Gerente). | Versão 1.2: RF-02/RF-13/RN-08, UC-09. |
| 4 | Semântica de “dia” no período (grill-me). | RN-08 e UC-09: 00:00–23:59:59 no fuso do campus. |
| 5 | Tamanho dos motivos de decisão na fila (grill-me). | RN-07, RF-11, UC-06: aprovação e indeferimento ≤255. |
| 6 | Destinatários do digest (grill-me). | RF-13 e UC-06: todos usuários com papel Gerente ou TAE. |
| 7 | Autenticação do discente (grill-me). | RF-14, RNF-04, UC-02: link mágico por e-mail institucional. |
| 8 | Duração da sessão do discente após o link (grill-me). | RNF-04: até 8 h ou fim do dia civil (fuso); inatividade 2 h. |
| 9 | TTL do token do link mágico (grill-me). | RNF-04 / RF-14 / UC-02: **30 minutos** padrão. |
| 10 | Ausência de rate limit nas solicitações de link (decisão explícita). | RF-14 / RNF-04 / UC-02; risco **RSK-06**. |
| 11 | Professor autentica como o aluno: link mágico (RF-14 / RNF-04, UC-05). | RF-06, UC-05, README. |

## Glossário
- **GeAD:** Gestão de Desempenho Discente/Docente (nome provisório).
- **SUAP:** Sistema Unificado de Administração Pública, de onde provêm os dados acadêmicos iniciais em CSV.
- **Filament:** Framework PHP para construção rápida de painéis administrativos.
- **TAE:** Técnico(a) em Assuntos Educacionais (apoio operacional à Gerência de Ensino).
- **Link mágico:** URL de login única/temporária enviada por e-mail, sem senha fixa no MVP para **discente** e **docente** nos portais públicos (RF-14).
