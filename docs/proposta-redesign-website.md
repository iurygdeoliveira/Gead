# Proposta de Redesign — Website GeAD

## 1. Diagnóstico do Estado Atual

### Estrutura Existente

| Arquivo | Função |
|---------|--------|
| [landing.blade.php](file:///home/iury/Projetos/GeaD/resources/views/website/layouts/landing.blade.php) | Layout base (head, orbs, footer) |
| [home.blade.php](file:///home/iury/Projetos/GeaD/resources/views/website/pages/home.blade.php) | Conteúdo da landing page |
| [style.css](file:///home/iury/Projetos/GeaD/public/css/website/style.css) | CSS completo (558 linhas) |
| [script.js](file:///home/iury/Projetos/GeaD/public/js/website/script.js) | Parallax orbs + ripple no CTA |

### Problemas Identificados

| # | Problema | Impacto |
|---|----------|---------|
| 1 | **Split layout 50/50** — metade da viewport é "branding" estático sem CTA | O login fica confinado à coluna direita; em mobile, o usuário precisa scrollar para chegar ao botão |
| 2 | **Zero evidências de valor** — a lista de features é genérica ("Sigilo total", "Rápido e prático", "Análise inteligente") sem dados concretos | Usuário não entende *por que* o GeAD é melhor que o processo anterior |
| 3 | **CTA único sem hierarquia** — "Entrar com E-mail Institucional" aparece uma vez, sem repetição nem destaque visual persistente | Perde-se a conversão conforme o usuário explora a página |
| 4 | **Orbs decorativos desativados** (`display: none`) — o background é flat puro sem nenhum elemento visual de suporte | A página parece vazia e sem vida |
| 5 | **Sem dados de impacto** — os números do ciclo 2026.1 (785 alunos, 10.198 avaliações, consolidação de meses para <1 dia) não aparecem | Desperdiça a evidência mais persuasiva do sistema |

---

## 2. Evidências de Melhoria (Extraídas do Carrossel Instagram)

Da publicação [gead-divulgacao-carrossel.html](file:///home/iury/Projetos/Instagram/Labsis/gead-divulgacao-carrossel.html), extraí os seguintes dados e argumentos:

### Dados Quantitativos (Slide 7)

| Métrica | Valor |
|---------|-------|
| Alunos avaliadores | **785** |
| Avaliações processadas | **10.198** |
| Tempo de consolidação | **De meses → menos de 1 dia** |

### Problemas do Processo Anterior (Slide 2)

| Problema | Descrição |
|----------|-----------|
| Processos Dispersos | Dezenas de formulários avulsos no Google Forms e consolidação em planilhas |
| Lentidão Extrema | Enorme volume operacional; processo leva meses para ser executado |

### Funcionalidades-Chave do GeAD (Slides 3–6)

| Feature | Descrição curta |
|---------|----------------|
| **Segurança & Governança** | Completo sigilo do discente, sem comprometer integridade dos dados |
| **Integração SUAP** | Leitura direta do CSV de exportação; cadastro automático de turmas, disciplinas, alunos e docentes |
| **Envio em Lote por E-mail** | Processamento instantâneo de médias; envio automático das avaliações docentes por e-mail, eliminando trabalho operacional da Gerência de Ensino |
| **Engenharia de Software de IA** | Spec-Driven Development; arquitetura validada por IA; mapeamento estrito de requisitos |

---

## 3. Diretrizes de Design (Consolidação)

### Do Design System Orbit ([design-system.html](file:///home/iury/Projetos/GeaD/docs/LP/design-system.html))

| Token | Valor |
|-------|-------|
| **Cor primária** | Yellow-Green OKLCH (primary-500: `oklch(0.931 0.228 123.104)`) |
| **Font-family** | Poppins (principal) + Inter Variable (fallback) |
| **Border radius** | Card: `1rem`, Shell: `1.2rem`, Pill: `2rem` |
| **Transitions** | `0.14s` ease (`cubic-bezier(0.4, 0, 0.2, 1)`) |
| **Surfaces (dark)** | Body bg: `gray-950`, Card: gradient escuro, Borders: `gray-800` |
| **Ícones** | SVG inline, Phosphor Icons style, `fill="currentColor"` |
| **Filosofia** | Flat-by-default, elevate-on-hover |

### Da Skill UI/UX Pro-Max

| Princípio | Aplicação |
|-----------|-----------|
| **Touch targets ≥ 44pt** | CTA mínimo de 56px de altura (já respeitado) |
| **Contraste ≥ 4.5:1** | Texto primary sobre dark bg — validar |
| **Sem emojis como ícones** | Usar SVG vetoriais consistentes |
| **Semântica tokens** | Não usar cores hardcoded; tudo via CSS custom properties |
| **8dp spacing rhythm** | Manter escala de 4px/8px |
| **Reduced motion** | `@media (prefers-reduced-motion: reduce)` já presente, manter |

---

## 4. Proposta de Nova Estrutura

### Conceito: "Login-First com Prova Social"

A página deixa de ser um split passivo e vira uma **single-page vertical** com o **CTA de login acima do fold**, seguido de uma seção compacta de provas de valor. O login permanece acessível via **header sticky**.

### Wireframe (Seções)

```
┌────────────────────────────────────────────────────┐
│  HEADER (sticky, transparente → opaco ao scroll)   │
│  [Logo GeAD]                    [Acessar o GeAD →] │
├────────────────────────────────────────────────────┤
│                                                    │
│  HERO (100vh, acima do fold)                       │
│                                                    │
│  Logo GeAD (glow, centralizado)                    │
│  "Gestão de Avaliação Docente"                     │
│  Subtítulo: "Plataforma institucional do           │
│   Campus Araguaína"                                │
│                                                    │
│  [████ Acessar o GeAD ████] ← CTA principal       │
│  "Acesso via link mágico — sem senha"              │
│                                                    │
│  🛡 Garantia de anonimato (selo inline)            │
│                                                    │
│  ↓ scroll indicator sutil                          │
│                                                    │
├────────────────────────────────────────────────────┤
│                                                    │
│  NÚMEROS DE IMPACTO (3 cards em row)               │
│                                                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────────────┐ │
│  │   785    │  │  10.198  │  │    < 1 dia       │ │
│  │  alunos  │  │ avaliaç. │  │  consolidação    │ │
│  │avaliador.│  │processad.│  │  (antes: meses)  │ │
│  └──────────┘  └──────────┘  └──────────────────┘ │
│                                                    │
│  "Ciclo acadêmico 2026.1"                         │
│                                                    │
├────────────────────────────────────────────────────┤
│                                                    │
│  ANTES vs DEPOIS (2 colunas)                       │
│                                                    │
│  ✗ ANTES                ✓ COM O GeAD               │
│  Formulários Google    Plataforma unificada        │
│  Planilhas manuais     Importação SUAP automática  │
│  Meses de consolidação Consolidação < 1 dia        │
│  Sem envio organizado  Envio em lote por e-mail    │
│                                                    │
├────────────────────────────────────────────────────┤
│                                                    │
│  FUNCIONALIDADES (grid 2×2, ícones SVG)            │
│                                                    │
│  ┌─────────────────┐  ┌─────────────────┐         │
│  │ Sigilo Total    │  │ Integração      │         │
│  │ Respostas       │  │ SUAP            │         │
│  │ anônimas        │  │ CSV automático  │         │
│  └─────────────────┘  └─────────────────┘         │
│  ┌─────────────────┐  ┌─────────────────┐         │
│  │ Envio em Lote   │  │ Consolidação    │         │
│  │ por E-mail das  │  │ Instantânea     │         │
│  │ avaliações      │  │                 │         │
│  └─────────────────┘  └─────────────────┘         │
│                                                    │
├────────────────────────────────────────────────────┤
│                                                    │
│  CTA FINAL (repetição)                             │
│                                                    │
│  "Pronto para avaliar?"                            │
│  [████ Acessar o GeAD ████]                        │
│                                                    │
├────────────────────────────────────────────────────┤
│  FOOTER                                            │
│  © 2026 GeAD · Campus Araguaína · Gerência de      │
│  Ensino · Um projeto LabSIS                        │
└────────────────────────────────────────────────────┘
```

---

## 5. Detalhamento por Seção

### 5.1 Header Sticky

- **Posição:** `position: sticky; top: 0`
- **Visual:** Transparente no topo, `backdrop-filter: blur(12px)` + `background: oklch(gray-950 / 0.85)` ao scroll
- **Conteúdo:** Logo GeAD (pequeno, ~32px) à esquerda + botão "Acessar o GeAD" à direita (pill, `border-radius: 2rem`, background `primary-600`)
- **Comportamento:** Sempre visível — o acesso ao login nunca sai da tela

### 5.2 Hero (acima do fold)

- **Height:** `min-height: 100vh` (ou `100dvh`)
- **Layout:** Flexbox centralizado verticalmente
- **Elementos:**
  1. Logo com glow (`drop-shadow` multi-layer, como já existe)
  2. Título: `"Gestão de Avaliação Docente"` — `font-size: clamp(1.75rem, 3vw, 2.5rem)`, `font-weight: 700`
  3. Subtítulo: `"Plataforma institucional do Campus Araguaína"` — `color: gray-300`
  4. **CTA principal** — mesmo estilo do atual, full-width até `max-width: 400px`
  5. Hint do link mágico
  6. Selo de anonimato (compacto, inline, ícone + texto)
- **Background:** Gradient radial sutil de `primary-950` no centro para `gray-950` nas bordas

### 5.3 Números de Impacto

- **Layout:** 3 cards em `grid-template-columns: repeat(3, 1fr)` (stack em mobile)
- **Cada card:**
  - Número grande: `font-size: 3rem`, `font-weight: 800`, `color: primary-500`
  - Label: `font-size: 0.875rem`, `color: gray-300`, uppercase
- **Entrada:** `animation: fade-in-up` com `IntersectionObserver` (scroll-triggered)
- **Subtítulo da seção:** `"Ciclo acadêmico 2026.1"` — badge pill, `background: gray-800`

### 5.4 Antes vs Depois

- **Layout:** 2 colunas, cada uma é um card com `border: 1px solid gray-800`, `border-radius: 1rem`
- **Coluna "Antes":** Ícone ✗ em `danger-500`, fundo com tint sutil `danger-950`
- **Coluna "Depois":** Ícone ✓ em `primary-500`, fundo com tint sutil `primary-950`
- **Itens:** Lista simples com ícone + texto curto (4 itens cada)
- **Propósito:** Contraste visceral, sem ser prolixo

### 5.5 Funcionalidades (Grid 2×2)

- **Layout:** `grid-template-columns: repeat(2, 1fr)`, gap `1rem`
- **Cada card:**
  - Ícone SVG inline (24px, `color: primary-500`)
  - Título: `font-weight: 600`, `font-size: 1rem`
  - Descrição: 1 linha, `font-size: 0.875rem`, `color: gray-400`
- **Cards:**
  1. **Sigilo Total** — "Respostas anônimas, identidade protegida"
  2. **Integração SUAP** — "Importação automática de turmas via CSV"
  3. **Envio em Lote** — "Envio automático das avaliações docentes por e-mail"
  4. **Consolidação Instantânea** — "Médias processadas em tempo real"

### 5.6 CTA Final

- **Layout:** Seção curta, texto centralizado
- **Título:** `"Pronto para avaliar?"` — `font-size: 1.75rem`, `font-weight: 600`
- **Botão:** Réplica do CTA do hero
- **Background:** Leve gradient de `primary-950` nas bordas para dar destaque

### 5.7 Footer

- **Compacto:** Uma linha: `© 2026 GeAD · Campus Araguaína · Gerência de Ensino`
- **LabSIS:** Logo pequeno + "Um projeto LabSIS" à direita
- **Separador:** `border-top: 1px solid gray-800`

---

## 6. Regras de Implementação

### Arquivos a Modificar

| Arquivo | Ação |
|---------|------|
| `resources/views/website/layouts/landing.blade.php` | Adicionar header sticky, remover orbs inutilizados |
| `resources/views/website/pages/home.blade.php` | Reescrever com nova estrutura de seções |
| `public/css/website/style.css` | Reescrever para nova estrutura (manter tokens Orbit) |
| `public/js/website/script.js` | Adicionar scroll-triggered animations + header scroll behavior |

### Constraints

- **Sem frameworks JS externos** — vanilla JS com `IntersectionObserver`
- **Sem Tailwind** — CSS vanilla com custom properties (como já está)
- **Todos os tokens Orbit preservados** — cores, radius, spacing
- **Mobile-first** — breakpoints em 640px e 1024px
- **Acessibilidade:** `aria-label` nos CTAs, `role="banner"` no header, `role="main"` no main, `prefers-reduced-motion` respeitado

### Comportamento do Header ao Scroll

```css
/* Pseudo-código do efeito */
.header {
  position: sticky;
  top: 0;
  background: transparent;
  transition: background 0.2s ease, border-color 0.2s ease;
}
.header--scrolled {
  background: oklch(0.154 0 89.876 / 0.9); /* gray-950 com alpha */
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--color-border);
}
```

```js
// JS mínimo
const header = document.querySelector('.header');
const observer = new IntersectionObserver(
  ([e]) => header.classList.toggle('header--scrolled', !e.isIntersecting),
  { threshold: 0.95 }
);
observer.observe(document.querySelector('.hero'));
```

---

## 7. Resumo das Decisões de Design

| Decisão | Justificativa |
|---------|---------------|
| **Login no header sticky** | Nunca sai da tela — máxima acessibilidade |
| **Hero com CTA above-the-fold** | Primeiro contato já permite login imediato |
| **Números antes de features** | Dados concretos geram credibilidade antes de explicar "como" |
| **Antes vs Depois** | Formato mais eficaz para mostrar melhoria sem ser prolixo |
| **Grid 2×2 para features** | Compacto, escaneável, sem scroll desnecessário |
| **CTA repetido no final** | Captura quem scrollou toda a página |
| **Vertical single-page** | Mais natural em mobile, remove o split que desperdiça espaço |

> **Nota:** Esta proposta mantém o **design system Orbit existente** (cores, tipografia, radius, shadows) e **não introduz dependências externas**. Toda a implementação usa CSS vanilla + JS vanilla.
