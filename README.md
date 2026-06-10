# GeAD — Gestão de Desempenho Discente/Docente

<div align="center">
  <img src="public/images/labsis_logo_bg.png" alt="GeAD — IFTO Campus Araguaína" width="520" />
  <br>
  <strong>IFTO — Instituto Federal do Tocantins, Campus Araguaína</strong><br>
  <em>Digitalização e automatização do ciclo semestral de avaliação docente pelo discente.</em>
</div>

<br>
<p align="center">
    <a href="https://filamentphp.com"><img alt="Filament v5" src="https://img.shields.io/badge/Filament-v5-eab308?style=for-the-badge"></a>
    <a href="https://laravel.com"><img alt="Laravel v13+" src="https://img.shields.io/badge/Laravel-v13+-FF2D20?style=for-the-badge&logo=laravel"></a>
    <a href="https://livewire.laravel.com"><img alt="Livewire v4" src="https://img.shields.io/badge/Livewire-v4-FB70A9?style=for-the-badge"></a>
    <a href="https://php.net"><img alt="PHP 8.5+" src="https://img.shields.io/badge/PHP-8.5+-777BB4?style=for-the-badge&logo=php"></a>
</p>

---

## 📌 Sobre o Projeto

O **GeAD** é um sistema projetado para automatizar por completo o ciclo semestral de avaliação de professores pelos alunos no **IFTO - Campus Araguaína**. A aplicação substitui o fluxo manual anterior (baseado em formulários individuais do Google Forms, consolidação em planilhas Excel e assinatura/disparo manual de PDFs) por um sistema centralizado e integrado.

### 🎯 Objetivos de Negócio (OST)
* **Redução Operacional**: Reduzir em até **90%** o tempo gasto pela Gerência de Ensino na consolidação do ciclo (meta de fechamento em **< 1 dia** de trabalho).
* **Automação de Assinaturas e Envio**: Automatizar o envio dos relatórios finais em formato PDF assinados em lote e disparados por e-mail em apenas um clique.
* **Integridade e Confiabilidade**: Garantir o sigilo da autoria das respostas na exibição para o professor, enquanto mantém o controle de elegibilidade e combate à duplicidade.

---

## 🏗️ Arquitetura e Decisões Técnicas (ADRs)

A arquitetura do GeAD foi documentada e validada através de **ADRs (Architectural Decision Records)** detalhadas na pasta [docs/sdd/adrs](file:///home/iury/Projetos/GeaD/docs/sdd/adrs):

* **[Monólito Laravel 13 + Filament v5](file:///home/iury/Projetos/GeaD/docs/sdd/adrs/001-monolito-laravel-filament-v5.md)**: Aplicação construída como monólito, utilizando Filament v5 com múltiplos painéis isolados por guards e middlewares nativos.
* **[Infraestrutura Serverless](file:///home/iury/Projetos/GeaD/docs/sdd/adrs/002-infra-laravel-cloud-postgresql.md)**: Hospedagem via **Laravel Cloud** integrada com banco de dados **PostgreSQL Gerenciado** para lidar com picos de tráfego sazonais (período de avaliação) com baixo custo operacional.
* **[Login por Link Mágico](file:///home/iury/Projetos/GeaD/docs/sdd/adrs/003-autenticacao-link-magico.md)**: Alunos e professores não utilizam senhas. O acesso se dá via link dinâmico enviado ao e-mail institucional, com token de uso único e tempo de expiração padrão de **30 minutos** (configurável via `MAGIC_LINK_TTL_MINUTES`). Gerentes e TAEs usam autenticação nativa (e-mail/senha).
* **[Assinatura Digital em Lote](file:///home/iury/Projetos/GeaD/docs/sdd/adrs/004-assinatura-digital-lote-govbr.md)**: Integração principal com a API **Gov.br (Assina GOV)** para assinatura em lote dos relatórios pelo Gerente, possuindo fallback local com certificado digital padrão A1/A3.
* **[Importação Exclusiva via CSV](file:///home/iury/Projetos/GeaD/docs/sdd/adrs/005-importacao-exclusiva-csv.md)**: Cadastro massivo de alunos, professores, turmas e vínculos a partir de arquivos CSV exportados do SUAP, com pausa e resolução interativa de conflitos em reimportações.
* **[Geração de PDF Server-Side](file:///home/iury/Projetos/GeaD/docs/sdd/adrs/006-geracao-pdf-dompdf.md)**: Geração de relatórios de desempenho docentes via **DomPDF** formatados estritamente segundo as normas do IFTO Campus Araguaína, incluindo cabeçalho institucional oficial e notas/médias agregadas em tabelas por turma.
* **[Filas Assíncronas](file:///home/iury/Projetos/GeaD/docs/sdd/adrs/007-fila-assincrona-queue-database.md)**: Processamento assíncrono para disparo de e-mails em lote, digest diário e consolidação de relatórios usando Laravel Queue no banco de dados PostgreSQL.
* **[Isolamento e Segurança (IDOR)](file:///home/iury/Projetos/GeaD/docs/sdd/adrs/008-anonimato-isolamento-idor.md)**: Garantia de anonimato nas avaliações ao docente (apenas notas consolidadas são expostas). O banco de dados mantém o vínculo técnico apenas para auditoria interna e evitar duplicidade. Aplicação de policies robustas contra falhas IDOR nos painéis Filament.
* **[Matriz de Permissões Gerente × TAE](file:///home/iury/Projetos/GeaD/docs/sdd/adrs/009-permissoes-gerente-tae-spatie.md)**: Separação de ações administrativas entre o Gerente de Ensino (assinatura/envio de relatórios em lote) e TAEs (operadores do sistema, suporte técnico e importações), controlada via **Spatie Laravel Permission** e UIs restritas nos respectivos painéis.
* **[Ciclo de Vida da Conta e Fila de Liberação](file:///home/iury/Projetos/GeaD/docs/sdd/adrs/010-ciclo-vida-conta-bloqueio-liberacao.md)**: Desativação automática de contas discentes após **3 anos** sem evidência de matrícula/vínculo ativa (soft-delete). Fluxo de liberação de conta bloqueada para avaliar no ciclo vigente com auditoria do motivo inserido (limite de 255 caracteres).

---

## 👥 Estrutura de Painéis e Acesso

O GeAD está estruturado em quatro portais distintos:

1. **Painel Gerente (`/gerente`)**: Destinado ao Gerente de Ensino. Permite realizar todas as operações do TAE e as ações de alta criticidade (assinatura em lote com API Gov.br/local e disparo em lote dos e-mails com PDFs anexados).
2. **Painel TAE (`/tae`)**: Destinado à equipe técnica e administrativa. Permite fazer a importação de CSVs, inclusões pontuais, liberar alunos bloqueados para avaliar, fechar o período de avaliações, consolidar relatórios pré-assinatura e consultar o status de envios de e-mails.
3. **Painel Aluno (`/aluno`)**: Acessado via link mágico. Apresenta a lista de professores do aluno no período letivo atual e o formulário de respostas (6 critérios estáticos), além de permitir solicitações de liberação.
4. **Painel Professor (`/professor`)**: Acessado via link mágico. Fornece acesso ao dashboard pessoal contendo o histórico de avaliações consolidadas e download dos relatórios oficiais assinados em formato PDF.

---

## 💻 Pré-requisitos

Para rodar a aplicação em ambiente de desenvolvimento local, certifique-se de possuir:

* **Docker** & **Docker Compose v2+** (para rodar os containers via Laravel Sail)
* **Git**
* **Composer**
* **Node.js** (versão 18 ou superior) & **NPM**

---

## 🚀 Instalação e Execução

Este projeto fornece um script automatizado interativo para instalar as dependências e subir o ambiente.

1. Clone o repositório e acesse a pasta do projeto:
   ```bash
   git clone git@github.com:iurygdeoliveira/Gead.git
   cd Gead
   ```

2. Execute o assistente de instalação local:
   ```bash
   ./install.php
   ```
   *O script irá validar os requisitos do sistema (PHP 8.5+, extensões, Docker), configurar permissões, criar o arquivo `.env`, executar `composer install`, subir os containers Sail, rodar migrations, seeds e preparar os assets frontend.*

3. Caso queira operar os containers manualmente via **Laravel Sail**:
   * Iniciar aplicação: `./vendor/bin/sail up -d`
   * Parar containers: `./vendor/bin/sail down`
   * Executar comandos artisan: `./vendor/bin/sail artisan [comando]`
   * Rodar testes: `./vendor/bin/sail test`

---

## ⚙️ Variáveis de Ambiente Relevantes

Configure no arquivo `.env`:

```env
# Validade do token do link mágico em minutos (padrão 30)
MAGIC_LINK_TTL_MINUTES=30

# Canal de execução de jobs (PostgreSQL)
QUEUE_CONNECTION=database

# Horário de disparo do e-mail de resumo diário (digest de solicitações TAE/Gerente)
NOTIFICACAO_DIGEST_TIME="08:30"
NOTIFICACAO_DIGEST_TIMEZONE="America/Fortaleza"

# Driver de assinatura digital (govbr | local)
SIGNATURE_DRIVER=govbr
```

---

## 🗂️ Documentação do Projeto

A documentação detalhada do projeto GeAD e seus requisitos estão disponíveis na pasta `/docs`:
* [**Requisitos e Especificações do Projeto**](file:///home/iury/Projetos/GeaD/docs/requisitos/projeto.md): Visão de negócio, dores mapeadas, requisitos funcionais (RFs), requisitos não funcionais (RNFs), regras de negócio (RNs) e log de auditoria do sistema.
* [**Casos de Uso (UCs)**](file:///home/iury/Projetos/GeaD/docs/requisitos/casos-de-uso): Passos detalhados e cenários de exceção de cada fluxo mapeado.
* [**Decisões Arquiteturais (ADRs)**](file:///home/iury/Projetos/GeaD/docs/sdd/adrs/INDEX.md): Indexador e detalhamento das 10 decisões técnicas críticas de design do sistema.

---

## 📄 Licença e Créditos

Este projeto é desenvolvido sob a **[MIT License](file:///home/iury/Projetos/GeaD/LICENSE)**.

A estrutura inicial e componentes estruturais baseados na stack TALL foram herdados do **[LabSIS Starter Kit](https://github.com/iurygdeoliveira/labSIS-SaaS-KIT-V4)**, desenvolvido pelo **Laboratório de Sistemas Inovadores (LabSIS)** para acelerar o desenvolvimento de soluções inteligentes. Agradecemos às comunidades do Laravel, Filament e Spatie pelo ferramental disponibilizado.

---

<div align="center">
  <strong>LabSIS - Transformando desafios reais em soluções inteligentes</strong>
</div>
