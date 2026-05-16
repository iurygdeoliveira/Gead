# UC-01: Importar Dados de Setup (CSV)

## 1. Breve Descrição
Permite que **Gerente de Ensino** ou **TAE** importem dados extraídos do SUAP via arquivos CSV para configurar o ciclo de avaliação. Os dados incluem o cadastro de alunos, professores e a vinculação às turmas/disciplinas do semestre. Importações subsequentes (**reimport**) podem exigir **resolução de conflitos** quando divergirem de **ajustes manuais** já realizados no GeAD.

## 2. Atores
- Gerente de Ensino
- TAE (apoio à Gerência)

## 3. Pré-condições
- O ator deve estar autenticado no painel administrativo do GeAD com permissão de importação (RF-12).
- Deve possuir os arquivos CSV gerados a partir do SUAP no formato padronizado.

## 4. Fluxo Principal (Happy Path)
1. O ator acessa o módulo de "Importação de Dados (Setup)".
2. O sistema exibe os campos de upload para Alunos, Professores e Vínculos de Turmas.
3. O ator seleciona os arquivos CSV correspondentes em sua máquina.
4. O ator clica em "Importar Dados".
5. O sistema processa os arquivos, validando a integridade das colunas e dos dados (ex.: e-mail institucional obrigatório).
6. Se não houver **conflitos** com registros previamente ajustados manualmente (vide RF-09), o sistema insere/atualiza os registros na base de dados.
7. O sistema exibe uma mensagem de sucesso com o resumo da importação (ex.: "X alunos cadastrados, Y vínculos criados") e atualiza evidências de vínculo para políticas de conta (RF-10), quando aplicável.

## 5. Fluxos Alternativos e Exceções

### Exceção 1: Arquivo CSV Inválido (Formato incorreto)
1. No passo 5 do fluxo principal, o sistema detecta que o CSV não possui as colunas esperadas ou a formatação está corrompida.
2. O sistema interrompe o processamento e exibe uma mensagem de erro detalhando qual arquivo e quais colunas estão com problema.
3. O ator corrige o arquivo e reinicia o fluxo a partir do passo 3.

### Exceção 2: Dados Incompletos ou Duplicados
1. No passo 5 do fluxo principal, o sistema encontra linhas com e-mails ausentes ou duplicidades não permitidas.
2. O sistema processa as linhas válidas, mas alerta o ator sobre os registros ignorados (gerando um log na tela).
3. O ator toma ciência do ocorrido e pode corrigir e fazer um novo upload complementar.

### Exceção 3: Reimport com Conflitos (RF-09)
1. No passo 6 do fluxo principal, o sistema detecta divergência entre o CSV e **vínculos ou cadastros** previamente corrigidos **manualmente** no GeAD (UC-08 / operações pontuais).
2. O sistema **não aplica** automaticamente as alterações conflitantes.
3. O sistema apresenta a **lista de conflitos item a item** e exige que o ator escolha, para cada item, qual versão prevalece (ou una ação equivalente definida na implementação) antes de concluir a importação.
4. Somente após todas as decisões serem registradas o sistema persiste as mudanças e exibe o resumo final.

## 6. Pós-condições
- A base de dados do sistema está alimentada (ou atualizada) e pronta para que os alunos elegíveis façam login e respondam às avaliações, respeitando RN-01, RF-10 e RF-11.
