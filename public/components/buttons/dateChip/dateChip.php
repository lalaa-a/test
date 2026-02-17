<!-- Optional font for this component only -->
<link href="https://fonts.googleapis.com/css2?family=Geologica:wght@600;700&display=swap" rel="stylesheet" />

<link rel="stylesheet" href="/css/date-chip.css" />

<!-- Chip 80×40 with 15px text -->
<span data-date-chip style="--dc-w:80px; --dc-h:40px; --dc-fz:15px;">
  <button class="dc-pill" type="button" aria-pressed="false">
    <span class="dc-text"> <?php echo $date?? "65"?> </span>
  </button>
</span>

<!-- Example: different size -->
<!--
<span data-date-chip style="--dc-w:96px; --dc-h:44px; --dc-fz:16px;">
  <button class="dc-pill" type="button"><span class="dc-text">9 Aug</span></button>
</span>
-->