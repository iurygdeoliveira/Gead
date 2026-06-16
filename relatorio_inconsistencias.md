# Relatório de Inconsistências de Dados - Sistema de Avaliação Docente

**Destinatário:** Gerência de Ensino / Coordenação Pedagógica  
**Assunto:** Regularização de matrículas de turmas e diários para avaliação de professores  

---

Prezada equipe pedagógica,

Durante o processo de parametrização e carga de dados dos alunos e professores no sistema de avaliação docente, identificamos uma inconsistência crítica que impede a avaliação de uma disciplina específica. Solicitamos apoio para a regularização ou envio de informações adicionais sobre a situação abaixo:

### 1. Ausência de Alunos Matriculados no Semestre Correto
* **Curso:** Técnico em Análises Clínicas Subsequente ao Ensino Médio (Código do Curso: `195`)
* **Professora:** Sabrina Guimarães Paiva
* **Disciplina:** *Projeto Integrador 4* (ocorrida nos semestres letivos `2024/2` e `2025/2`)

#### Descrição do Problema:
A disciplina **Projeto Integrador 4** faz parte do **4º período** da matriz curricular do curso. Para que a professora Sabrina possa ser avaliada por esta disciplina no período letivo de `2025/2` (momento em que a turma estava na 4ª fase), o sistema exige a existência da turma de estudantes que ingressaram em **`2024.1`**.

No entanto, no banco de dados de matrículas fornecido para importação, **não existem estudantes cadastrados com período de ingresso/entrada em `2024.1`** para este curso (temos registros de matrículas apenas para alunos ingressantes em `2025.1` e `2026.1`). Devido a essa ausência de alunos cadastrados em `2024.1`, a turma correspondente não pôde ser criada no sistema, impedindo que a disciplina e a professora sejam avaliadas por este grupo.

#### Ações Necessárias:
1. Confirmar se de fato houve turma ingressante em **`2024.1`** para o curso Técnico em Análises Clínicas Subsequente.
2. Em caso positivo, solicitamos o fornecimento da listagem oficial de alunos com seus respectivos números de matrícula e e-mails para que possamos cadastrá-los no sistema.
3. Caso a turma não tenha existido ou não deva ser avaliada, solicitamos a orientação sobre como proceder com este diário letivo.

---
*Relatório gerado em 15 de junho de 2026 para suporte à parametrização do sistema GeAD.*
