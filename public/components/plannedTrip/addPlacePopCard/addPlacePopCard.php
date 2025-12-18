<div id="adp" class="adp-root" data-open-btn-id="openAddDestination">
  <!-- Dim background -->
  <div class="adp-overlay" data-adp-close aria-hidden="true"></div>

  <!-- Sliding panel -->
  <aside class="adp-panel" role="dialog" aria-modal="true" aria-labelledby="adp-title">
    <header class="adp-header">
      <h2 id="adp-title" class="adp-title">Add Destination</h2>
      <button type="button" class="adp-icon-btn" data-adp-close aria-label="Close panel">×</button>
    </header>

    <!-- Body (form + scrollable cards) -->
    <div class="adp-body">
      <form id="adp-form" class="adp-form" method="post" action="" novalidate>
        <div class="adp-row-2">
          <div class="adp-field">
            <label for="adp-start">Start time</label>
            <input type="time" id="adp-start" name="start_time" class="adp-input adp-required">
            <div class="adp-error" aria-live="polite"></div>
          </div>

          <div class="adp-field">
            <label for="adp-end">End time</label>
            <input type="time" id="adp-end" name="end_time" class="adp-input adp-required">
            <div class="adp-error" aria-live="polite"></div>
          </div>
        </div>

        <div class="adp-field">
          <label for="adp-notes">Notes</label>
          <textarea id="adp-notes" name="notes" class="adp-input adp-required" rows="3" placeholder="Some details about the place"></textarea>
          <div class="adp-error" aria-live="polite"></div>
        </div>

        <div class="adp-field">
          <label for="adp-search">Search Destination</label>
          <div class="adp-search-wrap">
            <span class="adp-search-icon" aria-hidden="true">🔎</span>
            <input id="adp-search" name="search" class="adp-input adp-input--pill adp-required" placeholder="Search destinations from the site and add">
          </div>
          <div class="adp-error" aria-live="polite"></div>
        </div>
      </form>

      <div class="adp-section-title">Saved Places</div>

      <!-- Only this area scrolls. Replace inner HTML with your PHP cards -->
      <div id="adp-card-list" class="adp-card-list" tabindex="0" aria-label="Saved places list">
        <!-- Replace with PHP: BEGIN -->
        <article class="adp-card">
          <div class="adp-card-img" style="background-image:url('<?php echo IMG_ROOT.'/explore/destinations/sigiriya.png'?>');"></div>
          <div class="adp-card-body">
            <h4 class="adp-card-title">Sigiriya Ancient Rock Fortress</h4>
            <div class="adp-card-sub">4.6 • Some details about the place</div>
          </div>
          <button class="adp-card-menu" aria-label="More options">⋯</button>
        </article>

        <article class="adp-card">
          <div class="adp-card-img" style="background-image:url('<?php echo IMG_ROOT.'/explore/destinations/dambulla.png'?>');"></div>
          <div class="adp-card-body">
            <h4 class="adp-card-title">Another Place</h4>
            <div class="adp-card-sub">4.5 • Some details about the place</div>
          </div>
          <button class="adp-card-menu" aria-label="More options">⋯</button>
        </article>

        <article class="adp-card">
          <div class="adp-card-img" style="background-image:url('<?php echo IMG_ROOT.'/explore/destinations/sigiriya.png'?>');"></div>
          <div class="adp-card-body">
            <h4 class="adp-card-title">Sample Place</h4>
            <div class="adp-card-sub">4.3 • Some details about the place</div>
          </div>
          <button class="adp-card-menu" aria-label="More options">⋯</button>
        </article>
        <!-- Replace with PHP: END -->
      </div>
    </div>

    <footer class="adp-footer">
      <button type="button" class="adp-btn adp-btn--ghost" data-adp-close>Cancel</button>
      <button type="button" id="adp-add-btn" class="adp-btn adp-btn--primary">Add</button>
    </footer>
  </aside>
</div>
