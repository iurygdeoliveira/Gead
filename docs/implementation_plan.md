# Plano de Implementação: Vinculação e Importação de Dados do SUAP

Este plano descreve como o sistema será reestruturado para utilizar exclusivamente os novos arquivos extraídos do SUAP (`base de alunos.csv` e `diarios.csv`). Com a presença explícita da `Turma` em ambos os arquivos, simplificaremos significativamente o processo de vinculação e a geração das fichas de avaliação.

> [!WARNING]
> Todos os arquivos de dados de seed antigos (como `Bacharelado em Farmacia`, `disciplinas dos professores`, etc.) serão **ignorados**. O sistema dependerá estritamente da estrutura de `base de alunos.csv` e `diarios.csv`.

## User Review Required

> [!IMPORTANT]
> **Definição de Período/Ano Letivo:** O arquivo `diarios.csv` representa os diários de um semestre específico (por exemplo, 2026/1)? Atualmente, o widget de gerar avaliações pede para selecionar um período e fazia cálculos complexos com base na grade curricular. Com a nova estrutura, a sugestão é que a geração de avaliações simplesmente pegue **todas** as disciplinas vinculadas a uma turma e crie a ficha para **todos** os alunos matriculados nela, eliminando o cálculo "adivinhado". Você aprova essa simplificação?

> [!IMPORTANT]
> **Alunos sem turma:** Alunos sem turma informada no CSV serão cadastrados no sistema (criando o `Student` e o `Enrollment`), mas **não** terão um `ClassEnrollment` gerado no momento do seed. Eles poderão ser vinculados a uma turma manualmente pela interface do Filament. Isso atende à sua necessidade?

## Proposed Changes

### Seeders (Base de Dados)

Os seguintes Seeders serão totalmente reescritos:

- **CourseSeeder**: Lerá `base de alunos.csv` e `diarios.csv` para extrair os Cursos únicos (Código e Descrição).
- **CourseClassSeeder**: Extrairá os códigos de Turma de ambos os arquivos para criar os registros na tabela `course_classes`.
- **DisciplineSeeder**: Lerá `diarios.csv` e extrairá as disciplinas (Sigla e Nome), associando-as ao respectivo Curso.
- **TeacherSeeder**: Lerá `diarios.csv` na coluna "Professores". Irá separar nomes múltiplos e extrair a matrícula (SIAPE) entre parênteses para cadastrar todos os professores mencionados.
- **StudentSeeder**: Lerá `base de alunos.csv`. Cadastrará o Aluno (`Student`), a Matrícula no curso (`Enrollment`) e, **se** a turma for informada, já criará a vinculação aluno-turma (`ClassEnrollment`).
- **CourseClassDisciplineSeeder**: Lerá `diarios.csv`. Para cada linha, fará o vínculo exato entre a Turma (`CourseClass`), a Disciplina (`Discipline`) e o Professor (`Teacher`). Se houver múltiplos professores na mesma linha, criará um vínculo para cada professor.
- **UserSeeder**: Removerá qualquer lógica legada, garantindo apenas a criação dos usuários iniciais (como o `admin` e staff listado em código).
- **DatabaseSeeder**: Ajustará a ordem e fará chamadas limpas apenas para os novos seeders.

### Resources (Interface de Gestão - Filament)

- **Students Resource** (`app/Filament/Resources/Students`):
  - Adição de coluna na tabela indicando a `Turma` atual do aluno.
  - No formulário/infolist, adição de um gerenciador de `ClassEnrollments` para permitir vincular o aluno a uma turma manualmente (para aqueles que vieram sem turma).
- **CourseClasses Resource** (`app/Filament/Resources/CourseClasses`):
  - Atualização para focar no `code` da turma extraído do SUAP (ex: `20261.1.213.291.1I`).
- **Teachers / Courses / Evaluations Resources**:
  - Revisão das tabelas e relacionamentos para refletir que as avaliações e disciplinas agora derivam da vinculação explícita do diário.

### Widgets e Lógica de Avaliação

- **GenerateEvaluationsWidget**: 
  - **Remoção** da lógica complexa de `calculateTeachingPeriod`. 
  - **Nova Lógica**: A geração de avaliação agora iterará pelas Turmas. Para cada Aluno na Turma (`ClassEnrollment`) e para cada Disciplina/Professor da Turma (`CourseClassDiscipline`), o sistema gerará a ficha de avaliação pendente. Isso garante que a ficha seja gerada exatamente conforme o diário do SUAP.
- **SelectPeriodWidget, EvaluationsOverviewWidget, CourseEvaluationsWidget**:
  - Ajustes pontuais para ler os dados a partir dos vínculos diretos estabelecidos pelo novo fluxo, refletindo o modelo simplificado.

## Verification Plan

### Manual Verification
1. Rodar `php artisan migrate:fresh --seed` (via um comando controlado `rtk`) para garantir que as migrations rodem e os seeders leiam os arquivos CSVs novos sem erro.
2. Acessar o painel Manager (Filament) e conferir:
   - Os Alunos foram importados (inclusive os sem turma).
   - As Turmas e Cursos possuem os nomes/códigos fiéis ao CSV.
   - Os Diários resultaram na vinculação exata de "Turma + Professor(es) + Disciplina" na visualização do Filament.
3. Clicar em "Gerar Avaliações" e garantir que o total de fichas (Evaluations) é igual ao Produto do "Nº de alunos na turma" X "Nº de diários/professores da turma".
