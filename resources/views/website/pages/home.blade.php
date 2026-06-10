@extends('website.layouts.landing')

@section('content')
  <!-- Main split layout -->
  <main class="split" id="main-content">

    <!-- LEFT: Branding + Features -->
    <section class="split__brand" aria-label="Sobre o GeAD">
      <div class="brand-content">
        <div class="logo-wrapper">
          <img src="{{ asset('images/logo.png') }}" alt="Logo GeAD — capelo acadêmico verde com lupa vermelha" class="brand-logo" width="140"
            height="170" fetchpriority="high">
        </div>

        <p class="brand-description">
          Plataforma institucional de avaliação de desempenho docente do
          <strong>Campus Araguaína</strong>.
        </p>

        <!-- Lean feature list — 3 items only, no cards -->
        <ul class="features" role="list">
          <li class="feature-item">
            <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
            <div>
              <strong>Sigilo total</strong>
              <span>Respostas anônimas, identidade protegida</span>
            </div>
          </li>
          <li class="feature-item">
            <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="12" r="10" />
              <polyline points="12 6 12 12 16 14" />
            </svg>
            <div>
              <strong>Rápido e prático</strong>
              <span>Avaliação em poucos minutos</span>
            </div>
          </li>
          <li class="feature-item">
            <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <line x1="18" y1="20" x2="18" y2="10" />
              <line x1="12" y1="20" x2="12" y2="4" />
              <line x1="6" y1="20" x2="6" y2="14" />
            </svg>
            <div>
              <strong>Análise inteligente</strong>
              <span>Painéis e dashboards em tempo real</span>
            </div>
          </li>
        </ul>

        <!-- LabSIS attribution -->
        <div class="brand-labsis">
          <span class="brand-labsis__text">Um projeto</span>
          <a href="https://labsis.dev.br/" target="_blank" rel="noopener noreferrer" class="brand-labsis__link"
            aria-label="Laboratório de Sistemas Inovadores (LabSIS)">
            <img src="{{ asset('images/labsis_logo.png') }}" alt="LabSIS" class="brand-labsis__logo" width="109" height="30" loading="lazy">
          </a>
        </div>
      </div>
    </section>

    <!-- RIGHT: Access / CTA -->
    <section class="split__access" aria-label="Acesso ao sistema">
      <div class="access-section">
        <h1 class="access-title">Acessar o GeAD</h1>
        <p class="access-subtitle">
          Discentes, docentes e gestores.
        </p>

        <!-- Anonymity seal — prominent per spec -->
        <div class="seal" role="status">
          <svg class="seal__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            <path d="M9 12l2 2 4-4" />
          </svg>
          <div class="seal__text">
            <strong>Garantia de anonimato</strong>
            <span>Sua autenticação valida a matrícula. As respostas são totalmente sigilosas — nenhum docente tem acesso
              à identidade dos avaliadores.</span>
          </div>
        </div>

        <a href="{{ route('filament.auth.auth.login') }}" class="cta-button" id="cta-login" role="button">
          <svg class="cta-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
            <polyline points="10 17 15 12 10 7" />
            <line x1="15" y1="12" x2="3" y2="12" />
          </svg>
          Entrar com E-mail Institucional
        </a>

        <p class="cta-hint">
          Acesso via link mágico — sem necessidade de senha.
        </p>
      </div>
    </section>
  </main>
@endsection
