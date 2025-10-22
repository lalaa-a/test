<!-- Optional: load the font just once globally -->
<link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&display=swap" rel="stylesheet" />

<section data-trip-card style="--tc-maxw:1000px;" data-trip-id="<?php echo $tripId ?? 'default-' . uniqid(); ?>">
  <div class="tpc-wrapper">

    <article class="tpc-card" aria-label="Trip card: <?php echo htmlspecialchars($name ?? 'Trip'); ?>" data-trip-id="<?php echo $tripId ?? 'default-' . uniqid(); ?>" style="cursor: pointer;" data-clickable>
      <div class="tpc-image-wrap">
        <img
          class="tpc-image"
          src="<?php echo htmlspecialchars($image ?? 'https://images.unsplash.com/photo-1558808047-8934d8794047?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'); ?>"
          alt="<?php echo htmlspecialchars($name ?? 'Trip'); ?>"
        />
      </div>

      <div class="tpc-details" 
           data-trip-name="<?php echo htmlspecialchars($name ?? 'Trip Title'); ?>"
           data-trip-description="<?php echo htmlspecialchars($description ?? 'No description provided.'); ?>"
           data-trip-start-date="<?php echo htmlspecialchars($startDate ?? ''); ?>"
           data-trip-end-date="<?php echo htmlspecialchars($endDate ?? ''); ?>">
        <div class="tpc-header">
          <h3 class="tpc-title"><?php echo htmlspecialchars($name ?? 'Trip Title'); ?></h3>
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

        <p class="tpc-date"><?php echo htmlspecialchars($dateRange ?? 'Date not set'); ?></p>
        <p class="tpc-desc"><?php echo htmlspecialchars($description ?? 'No description provided.'); ?></p>

        <div class="tpc-footer">
          <button class="tpc-status" type="button"><?php echo htmlspecialchars($status ?? 'Scheduled'); ?></button>
        </div>
      </div>
    </article>
  </div>
</section>