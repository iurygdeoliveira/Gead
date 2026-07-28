@extends('website.layouts.landing')

@section('content')
  <!-- Hero — Above the fold -->
  <section class="hero" id="hero" aria-label="Apresentação do GeAD">
    <div class="hero__bg" aria-hidden="true"></div>
    <div class="hero__content">
      <div class="hero__logo-wrapper">
        <img src="{{ asset('images/logo.png') }}" alt="Logo GeAD — capelo acadêmico verde com lupa vermelha"
          class="hero__logo" width="140" height="170" fetchpriority="high"
          style="filter: drop-shadow(0 0 12px rgba(250, 255, 229, 0.95)) drop-shadow(0 0 32px rgba(244, 255, 199, 0.75)) drop-shadow(0 0 60px rgba(229, 255, 138, 0.35)) drop-shadow(0 4px 16px rgba(0, 0, 0, 0.65));">
      </div>

      <h1 class="hero__title">Gestão de Avaliação Docente</h1>
      <p class="hero__subtitle">Plataforma institucional do <strong>Campus Araguaína</strong></p>

      <!-- LabSIS attribution -->
      <div class="hero__labsis">
        <span class="hero__labsis-text">Um projeto</span>
        <a href="https://labsis.dev.br/" target="_blank" rel="noopener noreferrer"
          aria-label="Laboratório de Sistemas Inovadores (LabSIS)">
          <img src="{{ asset('images/labsis_logo.png') }}" alt="LabSIS" class="hero__labsis-logo" width="109"
            height="30" loading="lazy">
        </a>
      </div>

      <a href="{{ route('filament.auth.auth.login') }}" class="cta-button" id="cta-login" role="button">
        <svg class="cta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
          <polyline points="10 17 15 12 10 7" />
          <line x1="15" y1="12" x2="3" y2="12" />
        </svg>
        Acessar o GeAD
      </a>

      <p class="cta-hint">Acesso via link mágico — sem necessidade de senha.</p>

      <!-- Anonymity seal -->
      <div class="seal" role="status">
        <svg class="seal__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
          <path d="M9 12l2 2 4-4" />
        </svg>
        <div class="seal__text">
          <strong>Garantia de anonimato</strong>
          <span>Sua autenticação valida a matrícula. As respostas são totalmente sigilosas.</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <main id="main-content" role="main">

    <!-- Impact Numbers -->
    <section class="impact" aria-label="Números de impacto">
      <div class="impact__content">
        <h2 class="impact__title reveal">Ciclo acadêmico 2026.1</h2>

        <div class="impact__grid">
          <div class="impact__card reveal">
            <span class="impact__number">785</span>
            <span class="impact__label">Alunos Avaliadores</span>
          </div>
          <div class="impact__card reveal">
            <span class="impact__number">10.198</span>
            <span class="impact__label">Avaliações Processadas</span>
          </div>
          <div class="impact__card reveal">
            <span class="impact__number">&lt; 1 dia</span>
            <span class="impact__label">Tempo de Consolidação</span>
            <span class="impact__detail">Antes: meses</span>
          </div>
        </div>
      </div>
    </section>

    <!-- What changes with GeAD — unified section -->
    <section class="showcase" aria-label="O que muda com o GeAD">
      <div class="showcase__content">
        <h2 class="showcase__title reveal">O que muda com o GeAD</h2>

        <div class="showcase__grid">

          <!-- Before card -->
          <div class="showcase__card showcase__card--before reveal">
            <div class="showcase__card-header">
              <div class="showcase__card-icon-wrap showcase__card-icon-wrap--danger">
                <svg class="showcase__card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <line x1="18" y1="6" x2="6" y2="18" />
                  <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
              </div>
              <span class="showcase__card-label">Antes</span>
            </div>
            <ul class="showcase__list">
              <li>Formulários avulsos no Google Forms</li>
              <li>Consolidação manual em planilhas</li>
              <li>Processo leva meses para concluir</li>
              <li>Sem envio organizado dos resultados</li>
            </ul>
          </div>

          <!-- After card -->
          <div class="showcase__card showcase__card--after reveal">
            <div class="showcase__card-header">
              <div class="showcase__card-icon-wrap showcase__card-icon-wrap--primary">
                <svg class="showcase__card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <polyline points="20 6 9 17 4 12" />
                </svg>
              </div>
              <span class="showcase__card-label">Com o GeAD</span>
            </div>
            <ul class="showcase__list">
              <li>Plataforma unificada e segura</li>
              <li>Importação automática via SUAP</li>
              <li>Consolidação em menos de 1 dia</li>
              <li>Envio em lote por e-mail</li>
            </ul>
          </div>

          <!-- Feature: Sigilo -->
          <div class="showcase__card showcase__card--feature reveal">
            <div class="showcase__card-header">
              <div class="showcase__card-icon-wrap showcase__card-icon-wrap--primary">
                <svg class="showcase__card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
              </div>
              <span class="showcase__card-label">Sigilo Total</span>
            </div>
            <p class="showcase__card-desc">Respostas anônimas, identidade protegida</p>
          </div>

          <!-- Feature: SUAP -->
          <div class="showcase__card showcase__card--feature reveal">
            <div class="showcase__card-header">
              <div class="showcase__card-icon-wrap showcase__card-icon-wrap--primary">
                <svg class="showcase__card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                  <polyline points="14 2 14 8 20 8" />
                  <path d="M8 13h2" />
                  <path d="M8 17h2" />
                  <path d="M14 13h2" />
                  <path d="M14 17h2" />
                </svg>
              </div>
              <span class="showcase__card-label">Integração SUAP</span>
            </div>
            <p class="showcase__card-desc">Importação automática de turmas via CSV</p>
          </div>

          <!-- Feature: Envio em Lote -->
          <div class="showcase__card showcase__card--feature reveal">
            <div class="showcase__card-header">
              <div class="showcase__card-icon-wrap showcase__card-icon-wrap--primary">
                <svg class="showcase__card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <rect width="20" height="16" x="2" y="4" rx="2" />
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
              </div>
              <span class="showcase__card-label">Envio em Lote</span>
            </div>
            <p class="showcase__card-desc">Envio automático das avaliações docentes por e-mail</p>
          </div>

          <!-- Feature: Consolidação -->
          <div class="showcase__card showcase__card--feature reveal">
            <div class="showcase__card-header">
              <div class="showcase__card-icon-wrap showcase__card-icon-wrap--primary">
                <svg class="showcase__card-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <line x1="18" y1="20" x2="18" y2="10" />
                  <line x1="12" y1="20" x2="12" y2="4" />
                  <line x1="6" y1="20" x2="6" y2="14" />
                </svg>
              </div>
              <span class="showcase__card-label">Consolidação Instantânea</span>
            </div>
            <p class="showcase__card-desc">Médias processadas em tempo real</p>
          </div>

        </div>
      </div>
    </section>

    <!-- Final CTA -->
    <section class="cta-final" aria-label="Acesso ao sistema">
      <div class="cta-final__content">
        <div class="cta-final__logo-wrapper">
          <img src="{{ asset('images/logo.png') }}" alt="Logo GeAD" class="cta-final__logo" width="80" height="97" loading="lazy"
            style="filter: drop-shadow(0 0 12px rgba(250, 255, 229, 0.95)) drop-shadow(0 0 32px rgba(244, 255, 199, 0.75)) drop-shadow(0 0 60px rgba(229, 255, 138, 0.35)) drop-shadow(0 4px 16px rgba(0, 0, 0, 0.65));">
        </div>
        
        <div class="cta-final__bg" aria-hidden="true"></div>
        <h2 class="cta-final__title">Pronto para avaliar?</h2>
        <a href="{{ route('filament.auth.auth.login') }}" class="cta-button cta-button--final" id="cta-login-final"
          role="button">
          <svg class="cta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
            <polyline points="10 17 15 12 10 7" />
            <line x1="15" y1="12" x2="3" y2="12" />
          </svg>
          Acessar o GeAD
        </a>
      </div>
    </section>

  </main>
@endsection
