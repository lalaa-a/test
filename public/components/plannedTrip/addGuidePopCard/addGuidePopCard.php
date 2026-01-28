<!-- Add Guides Drawer (scoped) -->
<div id="ag-guides" aria-hidden="true">
  <div class="ag-backdrop" data-ag-close></div>
  <aside class="ag-panel" role="dialog" aria-modal="true" aria-labelledby="ag-title" tabindex="-1">
    <form id="ag-form" class="ag-form" method="post" action="">
      <header class="ag-header">
        <h2 id="ag-title">Add Guides</h2>
        <div class="ag-field">
          <label class="ag-label" for="ag-search">Add Guide</label>
          <div class="ag-search">
            <span class="ag-search-icon" aria-hidden="true">🔎</span>
            <input id="ag-search" type="search" placeholder="Search from the site" autocomplete="off" />
          </div>
        </div>
      </header>

      <section class="ag-content" aria-labelledby="ag-saved-title">
        <h3 id="ag-saved-title" class="ag-section-title">Saved Guides</h3>
        <ul class="ag-list" role="listbox" aria-multiselectable="true">
          <!-- Card 1 -->
          <li class="ag-card" tabindex="0" role="option" aria-selected="false" data-id="1">
            <img class="ag-thumb" src="<?php echo IMG_ROOT; ?>/explore/drivers/sample2.png" alt="Guide photo" />
            <div class="ag-card-body">
              <div class="ag-card-top">
                <p class="ag-name">Akila Prabhashawara</p>
                <button type="button" class="ag-more" aria-label="More options">…</button>
              </div>
              <div class="ag-rating">
                <span class="ag-score">4.6</span>
                <span class="ag-dots"><i></i><i></i><i></i><i></i><i class="ag-off"></i></span>
              </div>
              <p class="ag-sub">Some details about the guide</p>
            </div>
          </li>

          <!-- Card 2 -->
          <li class="ag-card" tabindex="0" role="option" aria-selected="false" data-id="2">
            <img class="ag-thumb" src="<?php echo IMG_ROOT; ?>/explore/drivers/sample1.png" alt="Guide photo" />
            <div class="ag-card-body">
              <div class="ag-card-top">
                <p class="ag-name">Akila Prabhashawara</p>
                <button type="button" class="ag-more" aria-label="More options">…</button>
              </div>
              <div class="ag-rating">
                <span class="ag-score">4.6</span>
                <span class="ag-dots"><i></i><i></i><i></i><i></i><i class="ag-off"></i></span>
              </div>
              <p class="ag-sub">Some details about the guide</p>
            </div>
          </li>
          
           <!-- Card 2 -->
          <li class="ag-card" tabindex="0" role="option" aria-selected="false" data-id="2">
            <img class="ag-thumb" src="<?php echo IMG_ROOT; ?>/explore/drivers/sample3.png" alt="Guide photo" />
            <div class="ag-card-body">
              <div class="ag-card-top">
                <p class="ag-name">Akila Prabhashawara</p>
                <button type="button" class="ag-more" aria-label="More options">…</button>
              </div>
              <div class="ag-rating">
                <span class="ag-score">4.6</span>
                <span class="ag-dots"><i></i><i></i><i></i><i></i><i class="ag-off"></i></span>
              </div>
              <p class="ag-sub">Some details about the guide</p>
            </div>
          </li>
          
           <!-- Card 2 -->
          <li class="ag-card" tabindex="0" role="option" aria-selected="false" data-id="2">
            <img class="ag-thumb" src="<?php echo IMG_ROOT; ?>/explore/drivers/sample4.png" alt="Guide photo" />
            <div class="ag-card-body">
              <div class="ag-card-top">
                <p class="ag-name">Akila Prabhashawara</p>
                <button type="button" class="ag-more" aria-label="More options">…</button>
              </div>
              <div class="ag-rating">
                <span class="ag-score">4.6</span>
                <span class="ag-dots"><i></i><i></i><i></i><i></i><i class="ag-off"></i></span>
              </div>
              <p class="ag-sub">Some details about the guide</p>
            </div>
          </li>
          
        </ul>
      </section>

      <footer class="ag-footer">
        <button type="button" class="ag-cancel" data-ag-close>Cancel</button>
        <button type="submit" class="ag-add" disabled>ADD</button>
      </footer>
    </form>
  </aside>
</div>