/* Trip Card Actions - Dropdown, Edit, Delete */
(() => {
  
  // Initialize all trip card actions when page loads
  function initTripCards() {
    // Set up dropdown toggles
    document.querySelectorAll('[data-trip-card] [data-toggle-dropdown]').forEach(button => {
      button.addEventListener('click', handleDropdownToggle);
    });

    // Set up action buttons (edit/delete)
    document.querySelectorAll('[data-trip-card] [data-action]').forEach(button => {
      button.addEventListener('click', handleAction);
    });

    // Set up card navigation
    document.querySelectorAll('[data-trip-card] .tpc-card[data-clickable]').forEach(card => {
      card.addEventListener('click', handleCardClick);
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', handleOutsideClick);
  }

  function handleDropdownToggle(event) {
    event.stopPropagation();
    
    const button = event.currentTarget;
    const dropdown = button.nextElementSibling;
    const isOpen = dropdown.classList.contains('show');
    
    // Close all other dropdowns
    document.querySelectorAll('[data-trip-card] .tpc-dropdown.show').forEach(dd => {
      dd.classList.remove('show');
    });
    
    // Toggle current dropdown
    if (!isOpen) {
      dropdown.classList.add('show');
    }
  }

  function handleOutsideClick(event) {
    if (!event.target.closest('[data-trip-card] .tpc-options-wrapper')) {
      document.querySelectorAll('[data-trip-card] .tpc-dropdown.show').forEach(dropdown => {
        dropdown.classList.remove('show');
      });
    }
  }

  function handleCardClick(event) {
    // Don't navigate if clicking on options menu or its dropdown
    if (event.target.closest('.tpc-options-wrapper')) {
      return;
    }
    
    // Don't navigate if clicking on buttons or interactive elements
    if (event.target.closest('button, a, input, select, textarea')) {
      return;
    }
    
    // Get the current URL root (assuming it's available)
    const urlRoot = window.URL_ROOT || '';
    
    // Navigate to the planned trip itinerary
    window.location.href = urlRoot + '/user/plannedTripItinerary';
  }

  function handleAction(event) {
    event.stopPropagation();
    
    const action = event.currentTarget.dataset.action;
    const tripCard = event.currentTarget.closest('[data-trip-card]');
    const tripDetails = tripCard.querySelector('.tpc-details');
    
    // Close dropdown
    const dropdown = event.currentTarget.closest('.tpc-dropdown');
    dropdown.classList.remove('show');
    
    if (action === 'edit') {
      handleEdit(tripCard, tripDetails);
    } else if (action === 'delete') {
      handleDelete(tripCard);
    }
  }

  function handleEdit(tripCard, tripDetails) {
    // Get current trip data
    const tripData = {
      name: tripDetails.dataset.tripName || '',
      description: tripDetails.dataset.tripDescription || '',
      startDate: tripDetails.dataset.tripStartDate || '',
      endDate: tripDetails.dataset.tripEndDate || ''
    };

    // Open the create trip form with existing data
    const drawer = document.getElementById('tripDrawer');
    const form = drawer ? drawer.querySelector('.ctf-form') : null;
    
    if (drawer && form) {
      // Pre-fill the form
      const nameInput = form.querySelector('[name="name"]');
      const descInput = form.querySelector('[name="description"]');
      const startInput = form.querySelector('.ctf-start');
      const endInput = form.querySelector('.ctf-end');
      
      if (nameInput) nameInput.value = tripData.name;
      if (descInput) descInput.value = tripData.description;
      if (startInput) startInput.value = tripData.startDate;
      if (endInput) endInput.value = tripData.endDate;
      
      // Mark as edit mode
      form.dataset.editMode = 'true';
      form.dataset.editTarget = getCardId(tripCard);
      
      // Update form title
      const title = form.querySelector('.ctf-title');
      if (title) title.textContent = 'Edit Trip';
      
      // Update submit button text
      const submitBtn = form.querySelector('.ctf-submit');
      if (submitBtn) submitBtn.textContent = 'Update Trip';
      
      // Pre-fill calendar dates if available
      if (window.prefillFormDates && tripData.startDate) {
        setTimeout(() => {
          window.prefillFormDates(tripData.startDate, tripData.endDate);
        }, 100);
      }
      
      // Open drawer
      drawer.classList.add('is-open');
      drawer.setAttribute('aria-hidden', 'false');
      lockBodyScroll();
      
      // Focus first input
      setTimeout(() => {
        if (nameInput) nameInput.focus();
      }, 300);
    }
  }

  function handleDelete(tripCard) {
    const tripName = tripCard.querySelector('.tpc-title').textContent;
    
    if (confirm(`Are you sure you want to delete "${tripName}"? This action cannot be undone.`)) {
      // Add fade out animation
      tripCard.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
      tripCard.style.opacity = '0';
      tripCard.style.transform = 'scale(0.95)';
      
      // Remove after animation
      setTimeout(() => {
        tripCard.remove();
        showToast('Trip deleted successfully');
      }, 300);
    }
  }

  // Generate unique ID for trip cards
  function getCardId(card) {
    if (!card.dataset.tripId) {
      card.dataset.tripId = 'trip-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    }
    return card.dataset.tripId;
  }

  // Toast notification
  function showToast(message) {
    // Check if toast already exists
    let toast = document.querySelector('.trip-toast');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'trip-toast';
      document.body.appendChild(toast);
    }
    
    toast.textContent = message;
    toast.classList.add('show');
    
    // Hide after 3 seconds
    setTimeout(() => {
      toast.classList.remove('show');
    }, 3000);
  }

  // Body scroll lock functions (for drawer)
  function lockBodyScroll() {
    const scrollY = window.scrollY;
    document.body.style.position = 'fixed';
    document.body.style.top = `-${scrollY}px`;
    document.body.style.width = '100%';
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTripCards);
  } else {
    initTripCards();
  }

  // Re-initialize when new cards are added dynamically
  window.initTripCardActions = initTripCards;

})();