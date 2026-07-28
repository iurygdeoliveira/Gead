<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description"
    content="GeAD — Sistema de Gestão de Avaliação Docente do Campus Araguaína. Avaliação segura, anônima e automatizada.">
  <meta name="theme-color" content="#0D0D0D">
  <title>GeAD — Gestão de Avaliação Docente</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/website/style.css') }}">
</head>

<body>
  <!-- Header Sticky -->
  @yield('content')

  <!-- Footer -->
  <footer class="footer" role="contentinfo">
    <div class="footer__content">
      <p class="footer__text">
        &copy; 2026 GeAD &middot; Campus Araguaína &middot; Gerência de Ensino
      </p>
      <div class="footer__labsis">
        <span class="footer__labsis-text">Um projeto</span>
        <a href="https://labsis.dev.br/" target="_blank" rel="noopener noreferrer"
          aria-label="Laboratório de Sistemas Inovadores (LabSIS)">
          <img src="{{ asset('images/labsis_logo.png') }}" alt="LabSIS" class="footer__labsis-logo" width="109"
            height="30" loading="lazy">
        </a>
      </div>
    </div>
  </footer>

  <script src="{{ asset('js/website/script.js') }}" defer></script>
</body>

</html>
