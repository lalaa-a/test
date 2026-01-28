// Scoped JS for the component
  (function () {
    const root = document.getElementById('ag-guides');
    const panel = root.querySelector('.ag-panel');
    const form = root.querySelector('#ag-form');
    const addBtn = root.querySelector('.ag-add');
    const list = root.querySelector('.ag-list');
    const closeEls = root.querySelectorAll('[data-ag-close]');

    let lastFocusedBeforeOpen = null;
    let keydownHandlerBound = null;
    let openedFromCard = null; // 👈 store which card button opened drawer

    function getFocusable() {
      return Array.from(
        panel.querySelectorAll(
          'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])'
        )
      ).filter(el => el.offsetParent !== null);
    }

    function onKeydown(e) {
      if (e.key === 'Escape') {
        e.preventDefault();
        close();
        return;
      }
      if (e.key === 'Tab') {
        const focusable = getFocusable();
        if (focusable.length === 0) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
      }
      if ((e.key === 'Enter' || e.key === ' ') && document.activeElement?.classList.contains('ag-card')) {
        e.preventDefault();
        toggleCard(document.activeElement);
      }
    }

    function open(triggerBtn = null) {
      if (root.classList.contains('ag-open')) return;
      openedFromCard = triggerBtn?.closest('[data-locitem]') || null;
      lastFocusedBeforeOpen = document.activeElement;
      root.classList.add('ag-open');
      root.removeAttribute('aria-hidden');
      setTimeout(() => panel.focus(), 0);
      keydownHandlerBound = onKeydown.bind(this);
      document.addEventListener('keydown', keydownHandlerBound);
    }

    function close() {
      root.classList.remove('ag-open');
      root.setAttribute('aria-hidden', 'true');
      document.removeEventListener('keydown', keydownHandlerBound || onKeydown);
      keydownHandlerBound = null;
      if (lastFocusedBeforeOpen && typeof lastFocusedBeforeOpen.focus === 'function') {
        lastFocusedBeforeOpen.focus();
      }
      openedFromCard = null; // reset
    }

    function selectedCards() {
      return Array.from(list.querySelectorAll('.ag-card[aria-selected="true"]'));
    }

    function updateAddButton() {
      addBtn.disabled = selectedCards().length === 0;
    }

    function toggleCard(cardEl) {
      const isSelected = cardEl.getAttribute('aria-selected') === 'true';
      cardEl.setAttribute('aria-selected', String(!isSelected));
      cardEl.classList.toggle('is-selected', !isSelected);
      updateAddButton();
    }

    // Click handler on list (delegation)
    list.addEventListener('click', (e) => {
      const card = e.target.closest('.ag-card');
      if (!card) return;

      // Handle "..." button clicks
      if (e.target.closest('.ag-more')) {
        console.log('More button clicked on card:', card.dataset.id);
        // TODO: open a context menu or take some action per card
        return;
      }

      // Otherwise toggle selection
      toggleCard(card);
    });

    // Close actions
    closeEls.forEach(el => el.addEventListener('click', close));
    root.querySelector('.ag-backdrop').addEventListener('click', close);

    // ✅ Hook all external open buttons (multiple per card)
    document.querySelectorAll('.open-guides-btn').forEach(btn => {
      btn.addEventListener('click', () => open(btn));
    });

    // Form submit (fires when "ADD" is clicked)
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const ids = selectedCards().map(li => li.dataset.id);
      const sourceCardId = openedFromCard?.dataset?.locitemId || null;

      // Emit a custom event with selection + source card
      root.dispatchEvent(new CustomEvent('ag:submit', { 
        detail: { selectedIds: ids, sourceCard: sourceCardId } 
      }));

      console.log('Submitted guides:', ids, 'for card:', sourceCardId);

      // Clear selection after submit
      selectedCards().forEach(card => {
        card.setAttribute('aria-selected', 'false');
        card.classList.remove('is-selected');
      });
      updateAddButton();

      close();
    });

    // Expose API
    window.AgGuidesDrawer = { open, close, el: root };
  })();