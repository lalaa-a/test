<article
  data-place-card
  data-rating="4.0"; /*to change the rating*/
  style="
    --pc-card-w: 544px;
    --pc-card-h: 188px;
    /* Optional: move three dots closer/farther from top/right */
    /* --pc-kebab-gap-top: 14px; */
    /* --pc-kebab-gap-right: 10px; */ /* smaller value = closer to right edge */
  "
  aria-label="Sigiriya Ancient Rock Fortress card"
>
  <!-- Left square photo (auto equals card height) -->
  <div class="pc-media">
    <img
      src="https://images.unsplash.com/photo-1558808047-8934d8794047?q=80&w=1200&auto=format&fit=crop"
      alt="Sigiriya Ancient Rock Fortress, Sri Lanka"
      loading="lazy"
    />
  </div>

  <!-- Right content -->
  <div class="pc-body">
    <!-- Three dots menu button -->
    <button class="pc-kebab" type="button" aria-label="More options"></button>

    <h3 class="pc-title">Sigiriya Ancient Rock Fortress</h3>

    <!-- Rating (CSS-only dots; set fills per dot) -->
    <div class="pc-rating" aria-label="Rating 4.6 out of 5">
      <span class="pc-score">4.6</span>
      <div class="pc-dots" aria-hidden="true">
        <span class="pc-dot" style="--pc-fill:100%"></span>
        <span class="pc-dot" style="--pc-fill:100%"></span>
        <span class="pc-dot" style="--pc-fill:100%"></span>
        <span class="pc-dot" style="--pc-fill:100%"></span>
        <span class="pc-dot" style="--pc-fill:60%"></span>
      </div>
    </div>

    <p class="pc-desc">Some details about the place</p>
  </div>
</article>