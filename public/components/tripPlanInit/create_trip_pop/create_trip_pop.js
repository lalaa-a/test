/* Create Trip Form (scoped) */
(() => {
  const monthNames = ["January","February","March","April","May","June","July","August","September","October","November","December"];
  const pad = n => String(n).padStart(2, '0');
  const toISO = d => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
  const sameDate = (a,b) => a && b && a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();
  const dayStart = d => new Date(d.getFullYear(), d.getMonth(), d.getDate());
  const inRange = (d, a, b) => {
    if (a && !b) return sameDate(d, a);
    if (!a || !b) return false;
    const s = a < b ? a : b, e = a < b ? b : a;
    const dn = dayStart(d).getTime();
    return dn >= dayStart(s).getTime() && dn <= dayStart(e).getTime();
  };

  function setup(root) {
    const form = root.querySelector('.ctf-form');
    const toast = root.querySelector('.ctf-toast');
    const grid = root.querySelector('.ctf-grid');
    const monthSel = root.querySelector('.ctf-month');
    const yearSel  = root.querySelector('.ctf-year');
    const prevBtn  = root.querySelector('.ctf-prev');
    const nextBtn  = root.querySelector('.ctf-next');
    const clearBtn = root.querySelector('.ctf-clear');
    const applyBtn = root.querySelector('.ctf-apply');
    const startInput = root.querySelector('.ctf-start');
    const endInput   = root.querySelector('.ctf-end');

    const today = new Date();
    let viewYear = today.getFullYear();
    let viewMonth = today.getMonth();
    let selStart = null, selEnd = null;

    // Fill month/year
    monthNames.forEach((name, idx) => {
      const opt = document.createElement('option');
      opt.value = idx; opt.textContent = name;
      monthSel.appendChild(opt);
    });
    const spanPast = 80, spanFuture = 20;
    for (let y = today.getFullYear() - spanPast; y <= today.getFullYear() + spanFuture; y++) {
      const opt = document.createElement('option');
      opt.value = y; opt.textContent = y;
      yearSel.appendChild(opt);
    }
    monthSel.value = String(viewMonth);
    yearSel.value = String(viewYear);

    // Events
    monthSel.addEventListener('change', () => { viewMonth = parseInt(monthSel.value,10); render(); });
    yearSel.addEventListener('change', () => { viewYear = parseInt(yearSel.value,10); render(); });
    prevBtn.addEventListener('click', () => {
      viewMonth--;
      if (viewMonth < 0) { viewMonth = 11; viewYear--; }
      monthSel.value = String(viewMonth); yearSel.value = String(viewYear);
      render();
    });
    nextBtn.addEventListener('click', () => {
      viewMonth++;
      if (viewMonth > 11) { viewMonth = 0; viewYear++; }
      monthSel.value = String(viewMonth); yearSel.value = String(viewYear);
      render();
    });

    clearBtn.addEventListener('click', () => {
      selStart = null; selEnd = null;
      startInput.value = ''; endInput.value = '';
      render();
    });

    applyBtn.addEventListener('click', () => {
      if (!selStart && !selEnd) { showToast('Select at least one day'); return; }
      syncHidden();
      const s = startInput.value, e = endInput.value;
      showToast(`Dates applied: ${s===e ? s : `${s} – ${e}`}`);
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (!startInput.value && selStart) syncHidden(); // auto-apply last selection

      const name = form.elements['name'].value.trim();
      const description = form.elements['description'].value.trim();
      const startDate = startInput.value;
      const endDate = endInput.value || startInput.value;

      if (!name) { showToast('Please enter a trip name'); return; }
      if (!startDate) { showToast('Please select your dates'); return; }

      const payload = { name, description, startDate, endDate };
      const url = root.dataset.postUrl || '';

      try {
        if (url) {
          const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          if (!res.ok) throw new Error('Network error');
          showToast('Trip created');
        } else {
          console.log('Create Trip payload:', payload);
          showToast('Trip created (logged to console)');
        }

        // Clear inputs and selection AFTER successful submit
        clearFields();

      } catch (err) {
        console.error(err);
        showToast('Failed to create trip');
      }
    });

    function onDayClick(d) {
      if (d.getFullYear() !== viewYear || d.getMonth() !== viewMonth) {
        viewYear = d.getFullYear(); viewMonth = d.getMonth();
        monthSel.value = String(viewMonth); yearSel.value = String(viewYear);
      }
      if (!selStart || (selStart && selEnd)) {
        selStart = new Date(d); selEnd = null;
      } else {
        if (d < selStart) { selEnd = selStart; selStart = new Date(d); }
        else { selEnd = new Date(d); }
      }
      render();
    }

    function render() {
      grid.innerHTML = '';
      const firstOfMonth = new Date(viewYear, viewMonth, 1);
      const startOffset = (firstOfMonth.getDay() + 6) % 7; // Monday-first
      const gridStart = new Date(viewYear, viewMonth, 1 - startOffset);

      for (let i = 0; i < 42; i++) {
        const d = new Date(gridStart); d.setDate(gridStart.getDate() + i);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'ctf-day';
        btn.textContent = d.getDate();
        btn.dataset.date = toISO(d);
        if (d.getMonth() !== viewMonth) btn.classList.add('is-outside');
        if (inRange(d, selStart, selEnd)) btn.classList.add('is-inrange');
        if (selStart && sameDate(d, selStart)) btn.classList.add('is-start');
        if (selEnd && sameDate(d, selEnd)) btn.classList.add('is-end');
        btn.addEventListener('click', () => onDayClick(d));
        grid.appendChild(btn);
      }
    }

    function syncHidden() {
      const s = selStart ? selStart : selEnd;
      const e = selEnd ? selEnd : selStart;
      if (!s) { startInput.value=''; endInput.value=''; return; }
      startInput.value = toISO(s);
      endInput.value   = toISO(e || s);
    }

    // Clear text fields, hidden date inputs, and selection; keep current month/year view
    function clearFields() {
      if (form.elements['name']) form.elements['name'].value = '';
      if (form.elements['description']) form.elements['description'].value = '';
      selStart = null;
      selEnd = null;
      startInput.value = '';
      endInput.value = '';
      render();
      // If you prefer to reset month/year to today too, uncomment:
      // const t = new Date(); viewYear = t.getFullYear(); viewMonth = t.getMonth();
      // monthSel.value = String(viewMonth); yearSel.value = String(viewYear); render();
    }

    let toastTimer = null;
    function showToast(msg) {
      toast.textContent = msg || 'Dates applied';
      toast.classList.add('is-visible');
      if (toastTimer) clearTimeout(toastTimer);
      toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 2200);
    }

    render();
  }

  function initAll() {
    document.querySelectorAll('[data-trip-form="create"]').forEach(setup);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();


/* Drawer open/close with body scroll lock (scoped) */
(() => {
  function getFocusable(container) {
    return [...container.querySelectorAll(
      'a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])'
    )];
  }

  function lockBodyScroll() {
    const y = window.scrollY || document.documentElement.scrollTop || 0;
    document.body.dataset.ctdScrollY = String(y);
    document.body.style.position = 'fixed';
    document.body.style.top = `-${y}px`;
    document.body.style.left = '0';
    document.body.style.right = '0';
    document.body.style.width = '100%';
  }
  function unlockBodyScroll() {
    const y = parseInt(document.body.dataset.ctdScrollY || '0', 10);
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.left = '';
    document.body.style.right = '';
    document.body.style.width = '';
    delete document.body.dataset.ctdScrollY;
    window.scrollTo(0, y);
  }

  function openDrawer(drawer, trigger) {
    drawer.classList.add('is-open');
    drawer.setAttribute('aria-hidden', 'false');
    drawer._trigger = trigger || null;
    lockBodyScroll();

    const panel = drawer.querySelector('.ctd-panel');
    const focusables = getFocusable(panel);
    const first = focusables[0];
    if (first) setTimeout(() => first.focus(), 340);

    function onKey(e) {
      if (e.key === 'Escape') closeDrawer(drawer);
      if (e.key === 'Tab') {
        const fs = getFocusable(panel);
        if (!fs.length) return;
        const i = fs.indexOf(document.activeElement);
        if (e.shiftKey && (i <= 0)) { e.preventDefault(); fs[fs.length - 1].focus(); }
        else if (!e.shiftKey && (i === fs.length - 1)) { e.preventDefault(); fs[0].focus(); }
      }
    }
    drawer._keyHandler = onKey;
    document.addEventListener('keydown', onKey);
  }

  function closeDrawer(drawer) {
    drawer.classList.remove('is-open');
    drawer.setAttribute('aria-hidden', 'true');
    document.removeEventListener('keydown', drawer._keyHandler);
    unlockBodyScroll();
    if (drawer._trigger) try { drawer._trigger.focus({ preventScroll: true }); } catch {}
  }

  function initDrawer(drawer) {
    drawer.addEventListener('click', (e) => {
      if (e.target.matches('[data-drawer-close], [data-drawer-close] *')) {
        closeDrawer(drawer);
      }
    });
  }

  function initOpenButtons() {
    document.querySelectorAll('[data-open-drawer]').forEach(btn => {
      const sel = btn.getAttribute('data-open-drawer');
      const drawer = document.querySelector(sel);
      if (!drawer) return;
      if (!drawer.dataset.initialized) {
        initDrawer(drawer);
        drawer.dataset.initialized = 'true';
      }
      btn.addEventListener('click', () => openDrawer(drawer, btn));
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initOpenButtons);
  } else {
    initOpenButtons();
  }
})();