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
      $img='https://images.unsplash.com/photo-1542556398-95f0d4d09f33?q=80&w=600&auto=format&fit=crop';
      $rating='4.6'; $dots=4; $desc='Some details about the place';
      renderComponent("plannedTrip","itineLocationItem");

      // 2) Location item (Destination) + Add Guide action, taller
      $title='Sigiriya'; $tag='Destination'; $tagKey='destination';
      $img='https://images.unsplash.com/photo-1573455494057-2d243b526f1a?q=80&w=600&auto=format&fit=crop';
      $showAddGuide=true;
      $locStyle='--loc-box-h: 140px;';
      renderComponent("plannedTrip","itineLocationItem");

      // 3) Location item (Map Location) default
      $title='Bandaranaike International Airport'; $tag='Map Location'; $tagKey='map';
      $img='https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=600&auto=format&fit=crop';
      $showAddGuide=false;
      $locStyle='';
      renderComponent("plannedTrip","itineLocationItem");

      // 4) Place item (Checkout) optional
      // $title='Bandaranaike International Airport'; $tag='Checkout'; $tagKey='checkout';
      // $img='https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=600&auto=format&fit=crop';
      // $placeStyle='--place-box-h: 120px;';
      // renderComponent("plannedTrip","itinePlaceItem");
    ?>

    <?php renderComponent("plannedTrip","itineAddBar"); ?>
  </div>
</div>