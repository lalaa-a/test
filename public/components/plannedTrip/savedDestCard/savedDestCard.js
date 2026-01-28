/* Place Card rating (scoped, vanilla JS) */
(() => {
  const CL = '[data-place-card]';
  const DOTS = 5;

  const clamp = (n, min, max) => Math.min(max, Math.max(min, n));

  function parseRating(card) {
    let r = parseFloat(card.getAttribute('data-rating'));
    if (Number.isNaN(r)) {
      const scoreEl = card.querySelector('.pc-score');
      r = scoreEl ? parseFloat(scoreEl.textContent) : 0;
    }
    return clamp(r || 0, 0, 5);
  }

  function ensureDots(wrap) {
    if (!wrap) return;
    if (wrap.children.length !== DOTS) {
      wrap.innerHTML = '<span class="pc-dot"></span>'.repeat(DOTS);
    }
  }

  function render(card, rating) {
    const r = clamp(Number(rating) || 0, 0, 5);
    const scoreEl = card.querySelector('.pc-score');
    if (scoreEl) scoreEl.textContent = r.toFixed(1).replace(/\.0$/, '');

    const wrap = card.querySelector('.pc-dots');
    if (wrap) {
      ensureDots(wrap);
      [...wrap.children].forEach((dot, i) => {
        const fill = clamp(r - i, 0, 1) * 100; // 0–100
        dot.style.setProperty('--pc-fill', fill.toFixed(0) + '%');
      });
    }

    card.setAttribute('data-rating', String(r));
    const row = card.querySelector('.pc-rating');
    if (row) row.setAttribute('aria-label', `Rating ${r.toFixed(1)} out of 5`);
  }

  function setupEditable(card, step = 0.5) {
    const wrap = card.querySelector('.pc-dots');
    if (!wrap) return;
    wrap.style.cursor = 'pointer';
    wrap.addEventListener('click', (e) => {
      const rect = wrap.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const ratio = x / rect.width;          // 0..1 across the whole dots row
      let r = Math.round((ratio * DOTS) / step) * step;
      r = clamp(r, 0, 5);
      render(card, r);
      card.dispatchEvent(new CustomEvent('placecard:ratingchange', { detail: { rating: r } }));
    });
  }

  function initCard(card) {
    const rating = parseRating(card);
    ensureDots(card.querySelector('.pc-dots'));
    render(card, rating);

    // Listen for manual updates via custom event
    card.addEventListener('placecard:update', (e) => {
      if (e.detail && typeof e.detail.rating !== 'undefined') {
        render(card, e.detail.rating);
      }
    });

    // React to data-rating changes
    const obs = new MutationObserver(muts => {
      for (const m of muts) {
        if (m.type === 'attributes' && m.attributeName === 'data-rating') {
          const r = parseFloat(card.getAttribute('data-rating'));
          if (!Number.isNaN(r)) render(card, r);
        }
      }
    });
    obs.observe(card, { attributes: true });

    // Optional: click the dots to set rating (use data-editable, step defaults to 0.5)
    if (card.hasAttribute('data-editable')) {
      const step = parseFloat(card.getAttribute('data-step')) || 0.5;
      setupEditable(card, step);
    }
  }

  function initAll() {
    document.querySelectorAll(CL).forEach(initCard);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll, { once: true });
  } else {
    initAll();
  }

  // Optional helper (small global for convenience)
  window.PlaceCardRating = {
    update(target, rating) {
      const el = typeof target === 'string' ? document.querySelector(target) : target;
      if (el && el.matches(CL)) render(el, rating);
    },
    get(target) {
      const el = typeof target === 'string' ? document.querySelector(target) : target;
      if (!el || !el.matches(CL)) return null;
      return parseFloat(el.getAttribute('data-rating')) || parseFloat(el.querySelector('.pc-score')?.textContent) || 0;
    }
  };
})();