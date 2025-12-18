<?php
$dateLabel = $dateLabel ?? 'Friday, 8 Aug';
$trId = $trId ?? ('tr-' . uniqid()); // unique id for the toggle
?>

<div data-tlinerail
     style="--tr-rail: 46px; --tr-gap: 24px; --tr-color: #e5e7eb;
            --tr-marker: 36px; --tr-marker-offset: 6px; --ab-h: 44px;">

    <!-- Hidden checkbox controls collapse (checked = collapsed) -->
  <input id="<?= $trId ?>" class="tr-toggle" type="checkbox">

  <!-- Header: date pill + chevron toggle (date is not clickable) -->
  <div class="tr-header">
    <div class="it-head">
      <h2><?= htmlspecialchars($dateLabel) ?></h2>
    </div>
    <label class="tr-btn" for="<?= $trId ?>" aria-label="Collapse / expand">
      <svg class="chev" viewBox="0 0 24 24" aria-hidden="true">
        <!-- chevron-up (rotates when collapsed) -->
        <path fill="currentColor" d="M7.41 15.41 12 10.83l4.59 4.58L18 14l-6-6-6 6z"/>
      </svg>
    </label>
  </div>

  <!-- Body: your items and Add bar -->
  <div class="tr-body">
    <?php
      // 1) Place item (Checking)
      $title='Bandaranaike International Airport'; $tag='Checking'; $tagKey='checking';
      $from='9.00 am'; $to='9.10 am';
      $img=IMG_ROOT.'/explore/destinations/colombo.png';
      $rating='4.6'; $dots=4; $desc='Some details about the place';
      $showAddGuide=false;
      renderComponent("plannedTrip","itineLocationItem");

      // 2) Location item (Destination) + Add Guide action, taller
      $title='Sigiriya'; $tag='Destination'; $tagKey='destination';
      $img=IMG_ROOT.'/explore/destinations/sigiriya.png';
      $showAddGuide=true;
      $locStyle='--loc-box-h: 140px;';
      renderComponent("plannedTrip","itineLocationItem");

      // 3) Location item (Map Location) default
      $title='Bandaranaike International Airport'; $tag='Map Location'; $tagKey='map';
      $img=IMG_ROOT.'/explore/destinations/negombo.png';
      $showAddGuide=false;
      $locStyle='';
      renderComponent("plannedTrip","itineLocationItem");
    ?>

    <?php renderComponent("plannedTrip","itineAddBar"); ?>
  </div>
</div>