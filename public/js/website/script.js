/* ===================================================================
   GeAD Landing Page — Interactions & Scroll Behavior
   =================================================================== */

(function () {
  'use strict';

  // ── Header scroll behavior ──
  const header = document.getElementById('site-header');
  const hero = document.getElementById('hero');

  if (header && hero) {
    const headerObserver = new IntersectionObserver(
      ([entry]) => {
        header.classList.toggle('header--scrolled', !entry.isIntersecting);
      },
      { threshold: 0.05 }
    );
    headerObserver.observe(hero);
  }

  // ── Scroll-triggered reveal animations ──
  const prefersReducedMotion = window.matchMedia(
    '(prefers-reduced-motion: reduce)'
  ).matches;

  if (!prefersReducedMotion) {
    const revealElements = document.querySelectorAll('.reveal');

    if (revealElements.length > 0) {
      const revealObserver = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              entry.target.classList.add('reveal--visible');
              revealObserver.unobserve(entry.target);
            }
          });
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
      );

      revealElements.forEach((el) => revealObserver.observe(el));
    }
  } else {
    // If reduced motion, make all reveal elements immediately visible
    document.querySelectorAll('.reveal').forEach((el) => {
      el.classList.add('reveal--visible');
    });
  }

  // ── CTA button ripple effect ──
  function addRipple(button) {
    if (!button || prefersReducedMotion) return;

    button.addEventListener('click', (e) => {
      const ripple = document.createElement('span');
      ripple.className = 'ripple';
      const rect = button.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
      ripple.style.top = e.clientY - rect.top - size / 2 + 'px';
      button.appendChild(ripple);

      ripple.addEventListener('animationend', () => {
        ripple.remove();
      });
    });
  }

  addRipple(document.getElementById('cta-login'));
  addRipple(document.getElementById('cta-login-final'));

  // Inject ripple CSS dynamically
  const rippleStyle = document.createElement('style');
  rippleStyle.textContent = `
    .cta-button {
      position: relative;
      overflow: hidden;
    }
    .ripple {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.2);
      transform: scale(0);
      animation: ripple-effect 0.5s ease-out forwards;
      pointer-events: none;
    }
    @keyframes ripple-effect {
      to {
        transform: scale(2.5);
        opacity: 0;
      }
    }
  `;
  document.head.appendChild(rippleStyle);
})();
