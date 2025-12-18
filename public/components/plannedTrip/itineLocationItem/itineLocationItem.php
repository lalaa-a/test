<?php
// Props (override BEFORE include)
$title   = $title   ?? 'Anuradhapura';
$from    = $from    ?? '9.00 am';
$to      = $to      ?? '9.10 am';
$img     = $img     ??  IMG_ROOT.'/explore/destinations/anuradhapura.png';
$rating  = $rating  ?? '4.6';
$dots    = $dots    ?? 4; // 0..5 filled dots
$desc    = $desc    ?? 'Some details about the place';
$tag     = $tag     ?? 'Destination';
$tagKey  = $tagKey  ?? 'destination'; // checking | destination | map | checkout
$showAddGuide = $showAddGuide ?? false; // set true to show "Add Guide" link

// Optional per-item style overrides (e.g., '--loc-card-w: 560px; --loc-box-h: 140px;')
$locStyle = $locStyle ?? '';
?>

<li data-locitem<?= $locStyle ? ' style="'.htmlspecialchars($locStyle).'"' : '' ?>>
<!-- Location (pin) icon -->
  <div class="li-marker" aria-hidden="true">
    <svg viewBox="0 0 24 24" aria-hidden="true">
      <path fill="currentColor" d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 10a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
    </svg>
  </div>

  <article class="li-card" data-status="<?= htmlspecialchars($tagKey) ?>">
    <span class="li-tag"><?= htmlspecialchars($tag) ?></span>
    <button type="button" class="li-menu" aria-label="More options">
      <svg viewBox="0 0 24 24"><path fill="currentColor" d="M6 12a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm8 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm8 0a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/></svg>
    </button>

    <div class="li-row">
      <div class="li-time">
        <span>From</span>
        <b><?= htmlspecialchars($from) ?></b>
        <span>to</span>
        <b><?= htmlspecialchars($to) ?></b>
      </div>

      <img class="li-thumb" src="<?= htmlspecialchars($img) ?>" alt="">

      <div class="li-info">
        <h3><?= htmlspecialchars($title) ?></h3>
        <div class="li-meta">
          <span class="li-rating">
            <?= htmlspecialchars($rating) ?>
            <span class="li-dots">
              <?php for($i=1;$i<=5;$i++): ?>
                <i class="<?= $i <= (int)$dots ? 'on':'' ?>"></i>
              <?php endfor; ?>
            </span>
          </span>
        </div>
        <p><?= htmlspecialchars($desc) ?></p>

        <?php if (1): ?>
          <button type="button" class="li-action open-guides-btn">
            <svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a7 7 0 0 0-7 7c0 4.08 7 13 7 13s7-8.92 7-13a7 7 0 0 0-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>
            Add Guide
          </button>
        <?php endif; ?>
      </div>
    </div>
  </article>
</li>