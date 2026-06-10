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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/website/style.css') }}">
</head>

<body>
  <!-- Ambient orbs -->
  <div class="bg-orbs" aria-hidden="true">
    <div class="orb orb--1"></div>
    <div class="orb orb--2"></div>
    <div class="orb orb--3"></div>
  </div>

  @yield('content')

  <!-- Footer -->
  <footer class="footer" role="contentinfo">
    <div class="footer__content">
      <p class="footer__text">
        &copy; 2026 GeAD — Campus Araguaína &middot; Gerência de Ensino
      </p>
    </div>
  </footer>

  <script src="{{ asset('js/website/script.js') }}" defer></script>
</body>

</html>
