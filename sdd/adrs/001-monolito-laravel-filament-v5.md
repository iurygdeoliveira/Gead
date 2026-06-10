# 1. Monólito Laravel + Filament v5 (Multi-Panel)

Date: 2026-06-09
Status: accepted

## Context

O time é pequeno (2–4 pessoas, acadêmico) e a trajetória de escala é estável (~600 alunos, 54 professores). RNF-01 exige explicitamente "monólito simplificado". O Filament foi escolhido como framework de UI administrativa, e a decisão do projeto é que **todos os perfis** (Gerente, TAE, Aluno, Professor) operem dentro do Filament — não há portal Blade/SPA separado.

O Filament v5 suporta nativamente **múltiplos panels** com guards e middleware separados, o que permite isolar contextos por perfil sem overhead de aplicações separadas.

**Forças:**
- Time acadêmico sem experiência em SPA/frontend complexo.
- Todos os perfis já estão definidos nos UCs com fluxos distintos.
- Isolamento de UI reduz superfície de IDOR (RSK-04).
- Manter toda a base de código no mesmo repo/deploy.

## Decision

Adotar **Laravel** como framework backend e **Filament v5** como framework de UI para **todos os perfis**, usando **4 panels separados**:

1. **Panel Gerente** (`/gerente`) — acesso total: import, correções, fila, fechar período, assinar em lote, disparar e-mails.
2. **Panel TAE** (`/tae`) — tudo exceto assinatura em lote e disparo em lote de e-mails.
3. **Panel Aluno** (`/aluno`) — login por link mágico, formulário de avaliação, solicitação de liberação.
4. **Panel Professor** (`/professor`) — login por link mágico, dashboard pessoal, download de PDF.

Cada panel terá seu próprio guard, middleware e sidebar. Sem SPA, sem build JS separado, sem microsserviços.

## Consequences

- **Prós:** Simplicidade máxima; um único deploy; UI consistente entre perfis; proteção IDOR by design (panels isolados); curva de aprendizado menor para o time.
- **Contras:** Filament v5 é mais recente e pode ter menos plugins maduros que o v3; 4 panels aumentam a quantidade de Resources/Pages a manter (mas a lógica de negócio é compartilhada no model layer).
- **Bloqueios futuros:** Migração para SPA exigiria reescrita significativa da camada de UI (mas não é prevista).
- **Fora de escopo:** Autenticação do Gerente/TAE usa mecanismo nativo do Filament (e-mail + senha), não link mágico.
