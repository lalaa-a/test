(() => {
  const root = document.getElementById('adp');
  if (!root) return;

  const panel = root.querySelector('.adp-panel');
  const overlay = root.querySelector('.adp-overlay');
  const addBtn = root.querySelector('#adp-add-btn');
  const form = root.querySelector('#adp-form');
  const startInput = root.querySelector('#adp-start');
  const endInput = root.querySelector('#adp-end');

  // Attach open button
  const openBtnId = root.getAttribute('data-open-btn-id') || '';
  const externalOpenBtn = openBtnId ? document.getElementById(openBtnId) : null;
  if (externalOpenBtn) {
    externalOpenBtn.addEventListener('click', openPanel);
  }

  root.querySelectorAll('[data-adp-close]').forEach(btn => btn.addEventListener('click', closePanel));
  document.addEventListener('keydown', (e) => {
    if (!root.classList.contains('adp-open')) return;
    if (e.key === 'Escape') closePanel();
  });

  function openPanel() {
    root.classList.add('adp-open');
    panel.setAttribute('aria-hidden', 'false');
    setTimeout(() => startInput?.focus(), 50);
  }
  function closePanel() {
    root.classList.remove('adp-open');
    panel.setAttribute('aria-hidden', 'true');
  }

  // Validation
  const requiredFields = [
    { el: startInput, name: 'Start time' },
    { el: endInput, name: 'End time' },
    { el: root.querySelector('#adp-notes'), name: 'Notes' },
    { el: root.querySelector('#adp-search'), name: 'Search Destination' },
  ];

  function setError(fieldEl, message) {
    const fieldWrap = fieldEl.closest('.adp-field');
    if (!fieldWrap) return;
    fieldWrap.classList.add('adp-invalid');
    const err = fieldWrap.querySelector('.adp-error');
    if (err) err.textContent = message || 'Required';
  }
  function clearError(fieldEl) {
    const fieldWrap = fieldEl.closest('.adp-field');
    if (!fieldWrap) return;
    fieldWrap.classList.remove('adp-invalid');
    const err = fieldWrap.querySelector('.adp-error');
    if (err) err.textContent = '';
  }

  requiredFields.forEach(({el}) => {
    ['input','change','blur'].forEach(evt => el?.addEventListener(evt, () => clearError(el)));
  });

  function timeToMinutes(value) {
    if (!value) return null;
    const [h, m] = value.split(':').map(Number);
    return h * 60 + m;
  }

  function validateForm() {
    let ok = true;
    requiredFields.forEach(({el, name}) => {
      if (!el) return;
      const val = (el.value || '').toString().trim();
      if (val === '') { setError(el, `${name} is required`); ok = false; }
      else { clearError(el); }
    });

    const s = timeToMinutes(startInput.value);
    const e = timeToMinutes(endInput.value);
    if (s !== null && e !== null && e <= s) {
      setError(endInput, 'End time must be after start time');
      ok = false;
    }
    return ok;
  }

  addBtn.addEventListener('click', () => {
    if (!validateForm()) return;
    const payload = {
      start_time: startInput.value,
      end_time: endInput.value,
      notes: root.querySelector('#adp-notes').value.trim(),
      search: root.querySelector('#adp-search').value.trim()
    };
    root.dispatchEvent(new CustomEvent('adp:submit', { detail: payload }));

    if (form.getAttribute('action')) {
      form.requestSubmit();
    } else {
      console.log('ADP submit payload:', payload);
      closePanel();
    }
  });

  overlay.addEventListener('click', closePanel);
})();