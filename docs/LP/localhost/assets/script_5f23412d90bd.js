/* ===================================================================
   GeAD Landing Page — Micro-interactions & Enhancements
   =================================================================== */

(function () {
  'use strict';

  // ── Subtle parallax on the orbs (desktop only, respects reduced motion) ──
  const prefersReducedMotion = window.matchMedia(
    '(prefers-reduced-motion: reduce)'
  ).matches;

  if (!prefersReducedMotion && window.innerWidth >= 1024) {
    const orbs = document.querySelectorAll('.orb');
    const speeds = [0.02, 0.015, 0.025];

    let mouseX = 0;
    let mouseY = 0;
    let rafId = null;

    document.addEventListener(
      'mousemove',
      (e) => {
        mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
        mouseY = (e.clientY / window.innerHeight - 0.5) * 2;

        if (!rafId) {
          rafId = requestAnimationFrame(updateOrbs);
        }
      },
      { passive: true }
    );

    function updateOrbs() {
      orbs.forEach((orb, i) => {
        const speed = speeds[i] || 0.02;
        const offsetX = mouseX * 30 * speed * (i + 1);
        const offsetY = mouseY * 20 * speed * (i + 1);
        orb.style.transform = `translate(${offsetX}px, ${offsetY}px)`;
      });
      rafId = null;
    }
  }

  // ── CTA button ripple effect ──
  const ctaButton = document.getElementById('cta-login');
  if (ctaButton) {
    ctaButton.addEventListener('click', (e) => {
      const ripple = document.createElement('span');
      ripple.className = 'ripple';
      const rect = ctaButton.getBoundingClientRect();
      const size = Math.max(rect.width, rect.height);
      ripple.style.width = ripple.style.height = size + 'px';
      ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
      ripple.style.top = e.clientY - rect.top - size / 2 + 'px';
      ctaButton.appendChild(ripple);

      ripple.addEventListener('animationend', () => {
        ripple.remove();
      });
    });
  }

  // Inject ripple CSS dynamically (keeps it self-contained)
  const rippleStyle = document.createElement('style');
  rippleStyle.textContent = `
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
