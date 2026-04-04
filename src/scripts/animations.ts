/**
 * GeAD — Script de Animações
 * IntersectionObserver para reveal animations on scroll
 * Respeita prefers-reduced-motion
 */

function initRevealAnimations(): void {
  // Respeita preferência de movimento reduzido
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (prefersReducedMotion) {
    // Marca todos como ativos imediatamente
    document.querySelectorAll<HTMLElement>('.reveal, .reveal-scale').forEach((el) => {
      el.classList.add('active');
    });
    return;
  }

  const revealElements = document.querySelectorAll<HTMLElement>('.reveal, .reveal-scale');

  if (revealElements.length === 0) return;

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.12,
      rootMargin: '0px 0px -40px 0px',
    }
  );

  revealElements.forEach((el) => observer.observe(el));
}

// Navbar scroll effect
function initNavbarScroll(): void {
  const navbar = document.getElementById('main-navbar');
  if (!navbar) return;

  const handleScroll = (): void => {
    if (window.scrollY > 20) {
      navbar.classList.add('navbar--scrolled');
    } else {
      navbar.classList.remove('navbar--scrolled');
    }
  };

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll(); // Check initial scroll
}

// Mobile menu toggle
function initMobileMenu(): void {
  const toggle = document.getElementById('mobile-menu-toggle');
  const menu = document.getElementById('mobile-menu');

  if (!toggle || !menu) return;

  toggle.addEventListener('click', () => {
    const isOpen = menu.classList.contains('mobile-menu--open');
    menu.classList.toggle('mobile-menu--open');
    toggle.setAttribute('aria-expanded', String(!isOpen));
  });

  // Fechar ao clicar em um link
  menu.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      menu.classList.remove('mobile-menu--open');
      toggle.setAttribute('aria-expanded', 'false');
    });
  });
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
  initRevealAnimations();
  initNavbarScroll();
  initMobileMenu();
});
