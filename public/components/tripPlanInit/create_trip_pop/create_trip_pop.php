<!-- Slide-in drawer (set width here) -->
  <section
    data-trip-drawer="ctf"
    id="tripDrawer"
    aria-hidden="true"
    style="--ctd-width: clamp(360px, 92vw, 560px)"
  >
    <div class="ctd-overlay" data-drawer-close></div>
    <aside class="ctd-panel" role="dialog" aria-modal="true" aria-labelledby="ctf-title">
      <button type="button" class="ctd-close" title="Close" aria-label="Close" data-drawer-close>×</button>

      <!-- Create Trip Form (scoped) -->
      <section data-trip-form="create" data-post-url="">
        <div class="ctf-bar"></div>

        <form class="ctf-form" novalidate>
          <h2 class="ctf-title" id="ctf-title">Create a trip</h2>

          <label class="ctf-label">
            Trip name
            <input class="ctf-input" type="text" name="name" required placeholder="e.g., Summer in Sri Lanka" />
          </label>

          <label class="ctf-label">
            Description
            <textarea class="ctf-textarea" name="description" rows="2" placeholder="Add details (optional)"></textarea>
          </label>

          <div class="ctf-field">
            <div class="ctf-field-title">Select days</div>

            <div class="ctf-calendar" aria-label="Date range picker">
              <div class="ctf-cal-header">
                <button type="button" class="ctf-nav ctf-prev" aria-label="Previous month">‹</button>

                <div class="ctf-cal-controls">
                  <select class="ctf-select ctf-month" aria-label="Select month"></select>
                  <select class="ctf-select ctf-year" aria-label="Select year"></select>
                </div>

                <button type="button" class="ctf-nav ctf-next" aria-label="Next month">›</button>
              </div>

              <div class="ctf-weekdays" aria-hidden="true">
                <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
              </div>

              <div class="ctf-grid" role="grid" aria-label="Calendar grid"></div>
            </div>

            <div class="ctf-actions">
              <button type="button" class="ctf-link ctf-clear">Clear</button>
              <button type="button" class="ctf-btn ctf-apply">Apply</button>
            </div>

            <!-- Hidden inputs populated on Apply (or on submit if not applied yet) -->
            <input type="hidden" name="startDate" class="ctf-start" />
            <input type="hidden" name="endDate" class="ctf-end" />
            </div>

          <div class="ctf-footer">
            <button type="button" class="ctf-cancel" data-drawer-close>Cancel</button>
            <button type="submit" class="ctf-submit">Create Trip</button>
          </div>
        </form>

        <div class="ctf-toast" role="status" aria-live="polite"></div>
      </section>
    </aside>
  </section>