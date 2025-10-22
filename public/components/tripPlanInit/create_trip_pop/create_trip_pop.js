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
      const isEditMode = form.dataset.editMode === 'true';
      const editTargetId = form.dataset.editTarget;

      try {
        if (url) {
          const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
          });
          if (!res.ok) throw new Error('Network error');
          showToast(isEditMode ? 'Trip updated' : 'Trip created');
        } else {
          if (isEditMode && editTargetId) {
            // Update existing card
            updateTripCard(editTargetId, payload);
            showToast('Trip updated successfully!');
          } else {
            // Create new card
            createTripCard(payload);
            showToast('Trip created successfully!');
          }
        }

        // Clear inputs and selection AFTER successful submit
        clearFields();
        resetFormToCreateMode();
        // Close the drawer
        closeDrawer();

      } catch (err) {
        console.error(err);
        showToast(isEditMode ? 'Failed to update trip' : 'Failed to create trip');
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

    // Reset form to create mode
    function resetFormToCreateMode() {
      form.dataset.editMode = 'false';
      delete form.dataset.editTarget;
      
      const title = form.querySelector('.ctf-title');
      if (title) title.textContent = 'Create a trip';
      
      const submitBtn = form.querySelector('.ctf-submit');
      if (submitBtn) submitBtn.textContent = 'Create Trip';
    }

    // Close drawer helper
    function closeDrawer() {
      const drawer = document.getElementById('tripDrawer');
      if (drawer) {
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.left = '';
        document.body.style.right = '';
        document.body.style.width = '';
        const scrollY = parseInt(document.body.dataset.ctdScrollY || '0', 10);
        delete document.body.dataset.ctdScrollY;
        window.scrollTo(0, scrollY);
      }
    }

    // Pre-fill form with existing dates (for edit mode)
    window.prefillFormDates = function(startDate, endDate) {
      if (startDate) {
        selStart = new Date(startDate);
        startInput.value = startDate;
      }
      if (endDate && endDate !== startDate) {
        selEnd = new Date(endDate);
        endInput.value = endDate;
      } else if (startDate) {
        selEnd = new Date(startDate);
        endInput.value = startDate;
      }
      
      // Update calendar view to show the selected dates
      if (selStart) {
        viewYear = selStart.getFullYear();
        viewMonth = selStart.getMonth();
        monthSel.value = String(viewMonth);
        yearSel.value = String(viewYear);
      }
      
      render();
    };

    let toastTimer = null;
    function showToast(msg) {
      toast.textContent = msg || 'Dates applied';
      toast.classList.add('is-visible');
      if (toastTimer) clearTimeout(toastTimer);
      toastTimer = setTimeout(() => toast.classList.remove('is-visible'), 2200);
    }

    render();
  }

  // Function to create a new trip card dynamically
  function createTripCard(tripData) {
    const { name, description, startDate, endDate } = tripData;

    // Format the date range
    const formatDate = (dateStr) => {
      const date = new Date(dateStr);
      return date.toLocaleDateString('en-US', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    };

    const startFormatted = formatDate(startDate);
    const endFormatted = formatDate(endDate);
    const dateRange = startDate === endDate ? startFormatted : `${startFormatted} → ${endFormatted}`;

    // Generate unique ID for the new card
    const cardId = 'trip-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

    // Create the card HTML with proper dropdown structure
    const cardHtml = `
      <section data-trip-card style="--tc-maxw:1000px;" data-trip-id="${cardId}">
        <div class="tpc-wrapper">
          <article class="tpc-card" aria-label="Trip card: ${name}" style="cursor: pointer;" data-clickable>
            <div class="tpc-image-wrap">
              <img
                class="tpc-image"
                src="https://images.unsplash.com/photo-1558808047-8934d8794047?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                alt="${name}"
              />
            </div>
            <div class="tpc-details" 
                 data-trip-name="${name}"
                 data-trip-description="${description || ''}"
                 data-trip-start-date="${startDate}"
                 data-trip-end-date="${endDate}">
              <div class="tpc-header">
                <h3 class="tpc-title">${name}</h3>
                <div class="tpc-options-wrapper">
                  <button class="tpc-options" type="button" aria-label="More options" data-toggle-dropdown>...</button>
                  <div class="tpc-dropdown" data-dropdown-menu>
                    <button class="tpc-dropdown-item" data-action="edit" type="button">
                      <span class="tpc-dropdown-icon">✏️</span>
                      Edit Trip
                    </button>
                    <button class="tpc-dropdown-item" data-action="delete" type="button">
                      <span class="tpc-dropdown-icon">🗑️</span>
                      Delete Trip
                    </button>
                  </div>
                </div>
              </div>
              <p class="tpc-date">${dateRange}</p>
              <p class="tpc-desc">${description || 'No description provided.'}</p>
              <div class="tpc-footer">
                <button class="tpc-status" type="button">Scheduled</button>
              </div>
            </div>
          </article>
        </div>
      </section>
    `;

    // Find the ongoing trips section and add the new card
    const ongoingSection = document.querySelector('h2.tpc-section-title');
    if (ongoingSection && ongoingSection.textContent.includes('Ongoing')) {
      // Find all existing trip cards in the ongoing section
      let insertPoint = ongoingSection.nextElementSibling;
      while (insertPoint && !insertPoint.querySelector('[data-trip-card]')) {
        insertPoint = insertPoint.nextElementSibling;
      }

      if (insertPoint) {
        // Insert before the first existing card
        insertPoint.insertAdjacentHTML('beforebegin', cardHtml);
      } else {
        // Insert after the heading if no cards exist
        ongoingSection.insertAdjacentHTML('afterend', cardHtml);
      }
    } else {
      // Fallback: find a good place to insert
      const body = document.body;
      const lastCard = body.querySelector('[data-trip-card]:last-of-type');
      if (lastCard) {
        lastCard.insertAdjacentHTML('afterend', cardHtml);
      } else {
        body.insertAdjacentHTML('beforeend', cardHtml);
      }
    }

    // Re-initialize card actions for the new card
    if (window.initTripCardActions) {
      window.initTripCardActions();
    }
  }

  // Function to update an existing trip card
  function updateTripCard(cardId, tripData) {
    const { name, description, startDate, endDate } = tripData;
    
    // Format the date range
    const formatDate = (dateStr) => {
      const date = new Date(dateStr);
      return date.toLocaleDateString('en-US', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    };

    const startFormatted = formatDate(startDate);
    const endFormatted = formatDate(endDate);
    const dateRange = startDate === endDate ? startFormatted : `${startFormatted} → ${endFormatted}`;

    // Find the card to update
    const card = document.querySelector(`[data-trip-id="${cardId}"]`);
    if (!card) return;

    // Update the card content
    const titleElement = card.querySelector('.tpc-title');
    const dateElement = card.querySelector('.tpc-date');
    const descElement = card.querySelector('.tpc-desc');
    const detailsElement = card.querySelector('.tpc-details');
    const cardElement = card.querySelector('.tpc-card');

    if (titleElement) titleElement.textContent = name;
    if (dateElement) dateElement.textContent = dateRange;
    if (descElement) descElement.textContent = description || 'No description provided.';
    if (cardElement) cardElement.setAttribute('aria-label', `Trip card: ${name}`);
    
    // Update data attributes
    if (detailsElement) {
      detailsElement.setAttribute('data-trip-name', name);
      detailsElement.setAttribute('data-trip-description', description || '');
      detailsElement.setAttribute('data-trip-start-date', startDate);
      detailsElement.setAttribute('data-trip-end-date', endDate);
    }

    // Add a subtle update animation
    card.style.transform = 'scale(1.02)';
    card.style.transition = 'transform 0.2s ease';
    setTimeout(() => {
      card.style.transform = 'scale(1)';
      setTimeout(() => {
        card.style.transition = '';
      }, 200);
    }, 150);
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