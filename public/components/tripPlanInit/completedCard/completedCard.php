<article data-trip-preview style="margin-top:20px;margin-bottom:20px;" data-trip-id="<?php echo isset($tripId) ? 'trip-' . $tripId : 'default-' . uniqid(); ?>">
  <div class="tpp-media">
    <img
      src="<?php echo htmlspecialchars($image ?? 'https://images.unsplash.com/photo-1558808047-8934d8794047?q=80&w=1200&auto=format&fit=crop'); ?>"
      alt="<?php echo htmlspecialchars($name ?? 'Trip'); ?>"
      loading="lazy"
    />
  </div>

  <div class="tpp-body">
    <!-- CSS-only dropdown -->
    <details class="tpp-menuwrap">
      <summary class="tpp-kebab" aria-label="More options" role="button" aria-haspopup="menu">•••</summary>
      <div class="tpp-menu" role="menu">
        <a class="tpp-menu-item" role="menuitem" href="#">See more</a>
        <button class="tpp-menu-item is-danger" role="menuitem" type="button" onclick="deleteCompletedTrip('<?php echo isset($tripId) ? $tripId : ''; ?>')">Delete</button>
      </div>
    </details>

    <h3 class="tpp-title"><?php echo htmlspecialchars($name ?? 'Trip Name'); ?></h3>
    <p class="tpp-date"><?php echo htmlspecialchars($dateRange ?? 'Date not set'); ?></p>
    <p class="tpp-desc"><?php echo htmlspecialchars($description ?? 'No description provided.'); ?></p>
  </div>
</article>

<script>
function deleteCompletedTrip(tripId) {
  if (!tripId) return;
  
  if (confirm('Are you sure you want to delete this completed trip? This action cannot be undone.')) {
    const urlRoot = window.URL_ROOT || '';
    const deleteUrl = `${urlRoot}/Trips/delete/${tripId}`;
    
    fetch(deleteUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Find and remove the card
        const card = document.querySelector(`[data-trip-id="trip-${tripId}"]`);
        if (card) {
          card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
          card.style.opacity = '0';
          card.style.transform = 'scale(0.95)';
          setTimeout(() => card.remove(), 300);
        }
        // Show success message
        alert('Trip deleted successfully');
      } else {
        alert(data.message || 'Failed to delete trip');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to delete trip');
    });
  }
}
</script>