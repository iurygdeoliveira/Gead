# PLAN.md — Login por Link Mágico Integrado (GeaD)

> Plano técnico derivado de SPEC.md.
> Constitution: N/A
> Spec: docs/sdd/specs/login-link-magico.md
> Status: RASCUNHO
> Data: 2026-06-10

## 1. Visão Técnica

### 1.1 Abordagem Arquitetural
Conforme recomendado pela skill `laravel-simplifier`, dado o tamanho enxuto da equipe (2 devs) e o escopo bem delimitado da feature (autenticação), a abordagem escolhida é **simples e direta**: extensão nativa da página de Login do Filament com lógica encapsulada em Actions de propósito único (Single-Action classes). Evitaremos a criação de Service Layers genéricas ou Repositories para não gerar overhead desnecessário e facilitar a leitura do código. O disparo de e-mails utilizará a estrutura de Notificações do Laravel.

### 1.2 Stack Confirmado
- Laravel 13
- Filament v5
- Banco de Dados
- Notificações por E-mail (SMTP / Resend)

### 1.3 Capacidade Operacional (constraints que calibram este plano)

| Dimensão | Valor declarado | Implicação para o plano |
|---|---|---|
| Tamanho e composição do time | 2 devs | Evitar overengineering arquitetural (ex: Repository Pattern). Usar Eloquent direto e Single-Action classes (`laravel-simplifier`). |
| Capacidade operacional | Horário comercial (inferido) | Disparo de links mágicos monitorados via logs básicos da infraestrutura atual. |
| Volume esperado no 1º ano | 500–10k req/dia (inferido) | Índices simples nas colunas de busca transacionais, sem necessidade de read-replicas. |
| Trajetória 12 meses | Uso focado em 1 campus | Manter tenant único e fixo ("Campus Araguaína" com CNPJ) minimizando rotas de onboarding dinâmicas (`laravel-simplifier`). |

## 2. Modelo de Dados

### 2.1 Diagrama Entidade-Relacionamento (DBML)

```dbml
Table users {
  id integer [primary key]
  email varchar
  is_approved boolean
  is_suspended boolean
  email_verified_at timestamp
  created_at timestamp
}

Table teams {
  id integer [primary key]
  name varchar
  slug varchar
  cnpj varchar [note: 'CNPJ exclusivo do Campus Araguaína']
  is_active boolean
  created_at timestamp
}

Table team_user {
  id integer [primary key]
  team_id integer
  user_id integer
  role varchar
}

Table magic_login_tokens {
  id integer [primary key]
  email varchar
  token varchar [note: 'Hash SHA256 do token gerado para maior segurança']
  expires_at timestamp
  created_at timestamp
  
  Indexes {
    email [name: 'idx_magic_tokens_email']
    token [name: 'idx_magic_tokens_token', unique]
  }
}

Ref: team_user.user_id > users.id // 1:N
Ref: team_user.team_id > teams.id // 1:N
```

#### Justificativa das Cardinalidades e Relacionamentos
- **`teams` ↔ `users` (N:M):** Mantemos a cardinalidade muitos-para-muitos original do template Filament (via tabela pivot `team_user` / `Membership`). Conforme `laravel-simplifier`, usar o padrão existente do pacote evita refatoração massiva da engine multi-tenant nativa, mesmo focando temporariamente em 1 campus.
- **`magic_login_tokens`:** Tabela isolada (sem FK rígida com `users`), permitindo geração de token por e-mail antes da ativação final da conta, evitando quebra relacional.

### 2.2 Detalhamento dos Models

#### Team
**Casts:** (conforme `laravel-best-practices`)
- `is_active` → `'boolean'`

#### MagicLoginToken
**Casts:** (conforme `laravel-best-practices`)
- `expires_at` → `'datetime'`

**Scopes:** (conforme `laravel-best-practices` e `laravel-query-builder`)
- `scopeValid()` — encapsula filtro `expires_at > now()`.

### 2.3 Migrations e Planejamento de Performance

| #   | Migration                         | Descrição                                                                  | Índices Planejados                                              | Review   | Observações |
| --- | --------------------------------- | -------------------------------------------------------------------------- | --------------------------------------------------------------- | -------- | ----------- |
| 1   | `add_cnpj_to_teams_table`         | Adiciona a coluna `cnpj` (string) à tabela `teams`.                        | —                                                               | aprovado |             |
| 2   | `create_magic_login_tokens_table` | Criação da tabela para armazenar os hashes dos links de login temporários. | Index em `email` e `token` (unique) (`laravel-best-practices`). | aprovado |             |

## 3. Camada de Negócio

### 3.1 Actions (single-purpose)
Consultando a skill `laravel-simplifier` (para equipes enxutas), delegaremos a lógica a Single-Action classes, removendo o acoplamento do Livewire de Login sem criar Services "gordos".

| Action                        | Input           | Output              | Req.                                                                                                                                                          | Review   | Observações |
| ----------------------------- | --------------- | ------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------- | -------- | ----------- |
| `SendMagicLinkAction`         | `string $email` | `void`              | Geração do Hash, salvar no DB, validar Role `Admin` (usando `$user->hasRole()` da skill `laravel-permission-development`) e enviar e-mail via `Notification`. | aprovado |             |
| `AuthenticateMagicLinkAction` | `string $token` | `User` ou Exception | Valida o hash, ativa user, concede Role (`$user->assignRole()`) e cadastra no Team "Campus Araguaína".                                                        | aprovado |             |

### 3.2 Notificações

| Notification            | Trigger               | Canal  | Req.                                                                                    | Review   | Observações |
| ----------------------- | --------------------- | ------ | --------------------------------------------------------------------------------------- | -------- | ----------- |
| `MagicLinkNotification` | `SendMagicLinkAction` | E-mail | Mensagem de uso único formatada no padrão nativo do Laravel (`laravel-best-practices`). | aprovado |             |

## 4. Camada de Apresentação (Filament)

### 4.1 Login Customizado
Consultando a skill `filament-blueprint`, optamos por NÃO criar uma Custom Page e sim utilizar os utilitários de esquemas. Estenderemos `Filament\Pages\Auth\Login` para aproveitar toda a estrutura, Rate Limiting e UI de formulário nativos, inserindo os componentes dinamicamente.

| Aspecto                               | Detalhes                                                                                                                        | Review   | Observações |
| ------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------- | -------- | ----------- |
| Form Schema (`filament-blueprint`)    | Componentes nativos `Placeholder::make()` para mensagens HTML (Sucesso) e link alternador reativo para ocultar campos de senha. | aprovado |             |
| Livewire State (`filament-blueprint`) | Variáveis públicas booleanas para manter o estado `magicLinkMode` e `magicLinkSent` sem recarregar a página.                    | aprovado |             |
| Autenticação                          | Sobrescrita de `authenticate()` para desviar o fluxo nativo se `magicLinkMode` for true.                                        | aprovado |             |

### 4.2 Rotas e Controllers (Web)
| Método | Rota                        | Controller@method              | Middleware                       | Req.                                                                                            | Review   | Observações |
| ------ | --------------------------- | ------------------------------ | -------------------------------- | ----------------------------------------------------------------------------------------------- | -------- | ----------- |
| GET    | `/auth/magic-login/{token}` | `MagicLinkController@callback` | `web` (`laravel-best-practices`) | Invoca a Action de autenticação, exibe `ValidationException` na página de login ou redireciona. | aprovada |             |

## 5. Autorização

Conforme as diretrizes de `laravel-permission-development`:

| Permission/Role | Descrição                                                                                                                                                                                   | Review   | Observações |
| --------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | -------- | ----------- |
| `Admin` (Role)  | Verificação imperativa (`hasRole()`) injetada na `SendMagicLinkAction`: contas `Admin` sofrem `ValidationException` (impedindo link mágico). Estudantes receberão `$user->assignRole(...)`. | aprovado |             |

## 7. Rastreabilidade Spec → Plan

| Requisito | Componente(s) técnico(s) |
|---|---|
| Envio de token único via e-mail | `SendMagicLinkAction`, `MagicLinkNotification`, `magic_login_tokens`. |
| Bloquear role Admin no Link | `$user->hasRole(RoleType::ADMIN->value)` em `SendMagicLinkAction`. |
| Autenticar e ativar flag no User | `AuthenticateMagicLinkAction` → `update(['is_approved' => true])`. |
| Associar ao Campus Araguaína | Eloquent nativo: `Team::firstOrCreate(...)` e `Membership::create(...)`. |

## 8. Decisões Arquiteturais

| ID     | Decisão                                                                 | Alternativas consideradas                       | Razão                                                                    | Reversibilidade | Review   | Observações |
| ------ | ----------------------------------------------------------------------- | ----------------------------------------------- | ------------------------------------------------------------------------ | --------------- | -------- | ----------- |
| AD-001 | Hashes SHA256 na tabela de tokens (`laravel-best-practices`).           | Salvar tokens plain-text.                       | Evita falha grave de segurança se DB for acessado sem autorização.       | Two-way         | aprovado |             |
| AD-002 | Estender componente `Filament\Pages\Auth\Login` (`filament-blueprint`). | `CustomPage` separada em Blade `/login-magico`. | Rate-limit gratuito, aproveita layout responsivo/branding global nativo. | Two-way         | aprovado |             |

### 8.1 Stress-test das Decisões (Devil's Advocate)

| Decisão                         | Alternativa simples                 | Argumento contra a decisão                                | Por que ainda escolhida (âncora: SPEC, CLAUDE ou seção 1.3)                                                                                                                                                    | Review   |
| ------------------------------- | ----------------------------------- | --------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | -------- |
| Single-Actions (`Send`, `Auth`) | Tudo dentro do Controller/Livewire. | Menos código num arquivo só (mais "rápido" pra escrever). | Requisito da `laravel-simplifier`: isolar Single Actions mitiga "componentes god" onde Livewire se enrola com lógica de acesso, falhas do Resend e regras de `HasRoles`. Facilita testes do Pest isoladamente. | aprovado |

## 9. Riscos Técnicos

| Risco                                                | Impacto | Mitigação                                                                                                                                           | Review   | Observações |
| ---------------------------------------------------- | ------- | --------------------------------------------------------------------------------------------------------------------------------------------------- | -------- | ----------- |
| Entrega do SMTP (Resend) falhar em horários de pico. | Alto    | Log nativo do Laravel (`laravel-best-practices` e `laravel-simplifier`). Rate-limiting ativo no formulário previne sobrecarga. Admins não afetados. | aprovado |             |

## 10. Sustentabilidade (10-Questions Test condensado)

| Pergunta | Resposta | Status |
|---|---|---|
| Q1 — Cada decisão resolve requisito SPEC ou risco mapeado? | Sim. UI integrada `filament-blueprint` previne código legadão; Actions separam fluxo `laravel-simplifier`. | ✅ |
| Q2 — Alternativa simples listada e justificada para cada componente? | Sim. Repositories e Observers substituídos por Actions e Eloquent direto. | ✅ |
| Q3 — Time mantém o padrão mais complexo proposto? | Sim, 2 devs absorvem `Single-Actions` e traits de `Spatie/Permission`. | ✅ |
| Q4 — Feature roda local ponta-a-ponta sem dependência exótica? | Sim. Tokens via SQLite e MailPit local (`laravel-best-practices`). | ✅ |
| Q5 — Blast radius documentado na seção 9? | Sim, mitigação de indisponibilidade de email. | ✅ |
| Q6 — Caminho de retorno estimado para decisões one-way? | Zero one-ways pesadas listadas. UI pode ser removida do Provider no Filament em 2 min. | ✅ |
| Q7 — Trajetória da seção 1.3 justifica a sofisticação proposta? | Sim, tenant hardcoded e zero filas pesadas alinha com 1 campus apenas. | ✅ |

---

> ⚠️ Após aprovação, este documento serve como input para TASKS.md.
