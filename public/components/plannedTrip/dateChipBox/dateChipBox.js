// Initializes ALL uninitialized [data-chipsbox] components on the page.
    (function initChipsBox(){
      const roots = document.querySelectorAll('[data-chipsbox]:not([data-cb-ready])');
      roots.forEach(root => {
        root.setAttribute('data-cb-ready','');

        const viewport = root.querySelector('.cb-viewport');
        const rightBtn = root.querySelector('.cb-arrow-right');
        const leftBtn  = root.querySelector('.cb-arrow-left');

        const css = () => getComputedStyle(root);
        const numVar = (name, fallback=0) => {
          const v = parseFloat(css().getPropertyValue(name));
          return Number.isFinite(v) ? v : fallback;
        };

        const maxScroll = () => Math.max(0, viewport.scrollWidth - viewport.clientWidth);
        const stepValue = () => {
          const custom = numVar('--cb-step', 0);
          if (custom > 0) return custom;
          // Auto page: most of visible area
          return Math.max(24, Math.floor(viewport.clientWidth * 0.9));
        };

        const clamp = (v, min, max) => Math.max(min, Math.min(max, v));
        const scrollToX = (x) => viewport.scrollTo({left: clamp(x, 0, maxScroll()), behavior: 'smooth'});

        const update = () => {
          const s = viewport.scrollLeft, m = maxScroll();
          rightBtn.disabled = s >= m - 1;
          leftBtn.disabled  = s <= 0;
          // Show left arrow/fade only after moving right
          root.toggleAttribute('data-show-left', s > 0);
        };

        rightBtn.addEventListener('click', () => scrollToX(viewport.scrollLeft + stepValue()));
        leftBtn.addEventListener('click',  () => scrollToX(viewport.scrollLeft - stepValue()));

        // Arrow-only: block wheel & swipe
        const block = e => e.preventDefault();
        viewport.addEventListener('wheel', block, {passive:false});
        viewport.addEventListener('touchmove', block, {passive:false});

        // Optional keyboard support when arrows focused
        rightBtn.addEventListener('keydown', e => { if(e.key==='ArrowRight') rightBtn.click(); });
        leftBtn .addEventListener('keydown', e => { if(e.key==='ArrowLeft')  leftBtn.click(); });

        viewport.addEventListener('scroll', update, {passive:true});
        window.addEventListener('resize', update, {passive:true});
        update();
      });
    })();