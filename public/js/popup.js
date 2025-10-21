// DOM elements
const overlay = document.getElementById('popupOverlay');
const closeBtn = document.getElementById('closeBtn');
const addButtons = document.querySelectorAll('.btn-add');
const deleteButtons = document.querySelectorAll('.btn-delete');
const moreButtons = document.querySelectorAll('.more-btn');

// Trip details elements
const tripDetailsOverlay = document.getElementById('tripDetailsOverlay');
const backBtn = document.getElementById('backBtn');
const tripDetailsTitle = document.getElementById('tripDetailsTitle');
const savedItemsContainer = document.getElementById('savedItemsContainer');
const savedItemsCount = document.getElementById('savedItemsCount');

// Store current destination data
let currentDestination = null;

// Initialize the popup functionality
document.addEventListener('DOMContentLoaded', function() {
    // Hide popup when close button is clicked
    if (closeBtn) {
        closeBtn.addEventListener('click', hidePopup);
    }
    
    // Hide popup when clicking outside the popup container
    if (overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                hidePopup();
            }
        });
    }
    
    // Hide popup when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay && overlay.classList.contains('show')) {
            hidePopup();
        }
    });
    
    // Add event listeners for trip actions
    initializeTripActions();
    
    // Add event listeners for trip cards
    initializeTripCards();
    
    // Add event listeners for trip details
    initializeTripDetails();
});

// Show popup function
function showPopup() {
    if (overlay) {
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }
}

// Hide popup function
function hidePopup() {
    if (overlay) {
        overlay.classList.remove('show');
        document.body.style.overflow = 'auto'; // Restore scrolling
    }
}

// Initialize trip action buttons
function initializeTripActions() {
    // Add buttons functionality
    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            handleAddTrip(this);
        });
    });
    
    // Delete buttons functionality
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            handleDeleteTrip(this);
        });
    });
    
    // More options buttons functionality
    moreButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            handleMoreOptions(this);
        });
    });
}

// Handle add trip action
function handleAddTrip(button) {
    const tripCard = button.closest('.trip-card');
    const tripTitle = tripCard.querySelector('.trip-title').textContent;
    
    // Visual feedback
    button.style.backgroundColor = '#28a745';
    button.textContent = 'ADDED';
    button.disabled = true;
    
    // Show success message (you can customize this)
    showNotification(`${tripTitle} added successfully!`, 'success');
    
    // Reset button after 2 seconds
    setTimeout(() => {
        button.style.backgroundColor = '#17a2b8';
        button.textContent = 'ADD';
        button.disabled = false;
    }, 2000);
}

// Handle delete trip action
function handleDeleteTrip(button) {
    const tripCard = button.closest('.trip-card');
    const tripTitle = tripCard.querySelector('.trip-title').textContent;
    
    // Confirmation dialog
    if (confirm(`Are you sure you want to delete "${tripTitle}"?`)) {
        // Visual feedback
        tripCard.style.opacity = '0.5';
        tripCard.style.transform = 'translateX(-10px)';
        
        // Remove trip after animation
        setTimeout(() => {
            tripCard.remove();
            showNotification(`${tripTitle} deleted successfully!`, 'warning');
        }, 300);
    }
}

// Handle more options action
function handleMoreOptions(button) {
    // Create a simple dropdown menu
    const dropdown = createDropdownMenu();
    const rect = button.getBoundingClientRect();
    
    // Position dropdown
    dropdown.style.position = 'absolute';
    dropdown.style.top = `${rect.bottom + 5}px`;
    dropdown.style.right = `${window.innerWidth - rect.right}px`;
    
    // Add to document
    document.body.appendChild(dropdown);
    
    // Remove dropdown when clicking outside
    const removeDropdown = (e) => {
        if (!dropdown.contains(e.target) && !button.contains(e.target)) {
            dropdown.remove();
            document.removeEventListener('click', removeDropdown);
        }
    };
    
    setTimeout(() => {
        document.addEventListener('click', removeDropdown);
    }, 100);
}

// Create dropdown menu for more options
function createDropdownMenu() {
    const dropdown = document.createElement('div');
    dropdown.className = 'dropdown-menu';
    dropdown.innerHTML = `
        <div class="dropdown-item" onclick="handleEditTrip(this)">
            <span>✏️</span> Edit Trip
        </div>
        <div class="dropdown-item" onclick="handleShareTrip(this)">
            <span>🔗</span> Share Trip
        </div>
        <div class="dropdown-item" onclick="handleDuplicateTrip(this)">
            <span>📋</span> Duplicate
        </div>
    `;
    
    // Add styles
    dropdown.style.cssText = `
        background: white;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 8px 0;
        z-index: 1001;
        min-width: 150px;
    `;
    
    return dropdown;
}

// Handle edit trip
function handleEditTrip(element) {
    const dropdown = element.closest('.dropdown-menu');
    dropdown.remove();
    showNotification('Edit functionality would be implemented here', 'info');
}

// Handle share trip
function handleShareTrip(element) {
    const dropdown = element.closest('.dropdown-menu');
    dropdown.remove();
    showNotification('Share functionality would be implemented here', 'info');
}

// Handle duplicate trip
function handleDuplicateTrip(element) {
    const dropdown = element.closest('.dropdown-menu');
    dropdown.remove();
    showNotification('Duplicate functionality would be implemented here', 'info');
}

// Show notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Style the notification
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : type === 'warning' ? '#ffc107' : '#17a2b8'};
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 1002;
        font-size: 14px;
        font-weight: 500;
        animation: slideInRight 0.3s ease-out;
    `;
    
    // Add animation keyframes if not already added
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideInRight 0.3s ease-out reverse';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Utility function to format dates
function formatDate(date) {
    const options = { day: 'numeric', month: 'long', year: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Utility function to create a new trip card
function createTripCard(tripData) {
    const tripCard = document.createElement('div');
    tripCard.className = 'trip-card';
    
    tripCard.innerHTML = `
        <div class="trip-image">
            <img src="${tripData.image || 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=150&h=100&fit=crop&crop=center'}" alt="${tripData.title}">
        </div>
        <div class="trip-details">
            <h4 class="trip-title">${tripData.title}</h4>
            <p class="trip-dates">${tripData.startDate} → ${tripData.endDate}</p>
            <p class="trip-description">${tripData.description}</p>
            <div class="trip-status ${tripData.status}">${tripData.statusText}</div>
        </div>
        <div class="trip-actions">
            <button class="more-btn">⋯</button>
            <div class="action-buttons">
                <button class="btn-add">ADD</button>
                <button class="btn-delete">DELETE</button>
            </div>
        </div>
    `;
    
    // Add event listeners to the new card
    const addBtn = tripCard.querySelector('.btn-add');
    const deleteBtn = tripCard.querySelector('.btn-delete');
    const moreBtn = tripCard.querySelector('.more-btn');
    
    addBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        handleAddTrip(this);
    });
    
    deleteBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        handleDeleteTrip(this);
    });
    
    moreBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        handleMoreOptions(this);
    });
    
    return tripCard;
}

// Initialize trip cards functionality
function initializeTripCards() {
    const tripCards = document.querySelectorAll('.trip-card[data-trip-id]');
    tripCards.forEach(card => {
        card.addEventListener('click', function() {
            const tripId = this.getAttribute('data-trip-id');
            const tripName = this.getAttribute('data-trip-name');
            selectTrip(tripId, tripName);
        });
    });
}

// Initialize trip details functionality
function initializeTripDetails() {
    if (backBtn) {
        backBtn.addEventListener('click', function() {
            hideTripDetails();
        });
    }
    
    if (tripDetailsOverlay) {
        tripDetailsOverlay.addEventListener('click', function(e) {
            if (e.target === tripDetailsOverlay) {
                hideTripDetails();
            }
        });
    }
}

// Select trip and show trip details
function selectTrip(tripId, tripName) {
    // Store trip info
    window.currentTrip = { id: tripId, name: tripName };
    
    // Auto-save current destination to this trip
    if (currentDestination) {
        const wasAdded = autoSaveToTrip(tripId, tripName, currentDestination);
        
        // If item was already in trip, still show trip details but don't add duplicate
        if (!wasAdded) {
            // Still show the trip details to let user see what's already there
            hidePopup();
            showTripDetails(tripName);
            return;
        }
    }
    
    // Hide main popup and show trip details
    hidePopup();
    showTripDetails(tripName);
}

// Auto-save destination to trip
function autoSaveToTrip(tripId, tripName, destination) {
    // This would typically make an API call to save the destination
    // For now, we'll just store it locally
    if (!window.tripData) {
        window.tripData = {};
    }
    
    if (!window.tripData[tripId]) {
        window.tripData[tripId] = {
            name: tripName,
            items: []
        };
    }
    
    // Check if destination already exists in this trip
    const existingItem = window.tripData[tripId].items.find(item => 
        item.id === destination.id || item.name === destination.name
    );
    
    if (existingItem) {
        console.log(`${destination.name} is already in trip: ${tripName}`);
        showNotification(`${destination.name} is already saved in this trip!`, 'info');
        return false; // Don't add duplicate
    }
    
    // Add destination to trip
    window.tripData[tripId].items.push(destination);
    
    console.log(`Auto-saved ${destination.name} to trip: ${tripName}`);
    showNotification(`${destination.name} saved to ${tripName}!`, 'success');
    return true; // Successfully added
}

// Show trip details popup
function showTripDetails(tripName) {
    if (tripDetailsOverlay) {
        tripDetailsTitle.textContent = tripName;
        updateSavedItems();
        tripDetailsOverlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

// Hide trip details popup
function hideTripDetails() {
    if (tripDetailsOverlay) {
        tripDetailsOverlay.classList.remove('show');
        document.body.style.overflow = 'auto';
    }
}

// Update saved items display
function updateSavedItems() {
    if (!window.currentTrip || !window.tripData) return;
    
    const tripId = window.currentTrip.id;
    const trip = window.tripData[tripId];
    
    if (!trip || !trip.items) return;
    
    // Update count
    savedItemsCount.textContent = `${trip.items.length} item${trip.items.length !== 1 ? 's' : ''} saved`;
    
    // Clear existing items
    savedItemsContainer.innerHTML = '';
    
    // Add each saved item
    trip.items.forEach((item, index) => {
        const savedItem = createSavedItem(item, index);
        savedItemsContainer.appendChild(savedItem);
    });
}

// Create saved item element
function createSavedItem(item, index) {
    const savedItem = document.createElement('div');
    savedItem.className = 'saved-item';
    savedItem.innerHTML = `
        <div class="saved-item-image">
            <img src="${item.image}" alt="${item.name}">
        </div>
        <div class="saved-item-details">
            <div class="saved-item-title">${item.name}</div>
            <div class="saved-item-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>${item.location}</span>
            </div>
        </div>
        <button class="remove-btn" onclick="removeItem(${index})">Remove</button>
    `;
    
    return savedItem;
}

// Remove item from trip
function removeItem(index) {
    if (!window.currentTrip || !window.tripData) return;
    
    const tripId = window.currentTrip.id;
    const trip = window.tripData[tripId];
    
    if (trip && trip.items) {
        trip.items.splice(index, 1);
        updateSavedItems();
        
        // If no items left, go back to main popup
        if (trip.items.length === 0) {
            hideTripDetails();
            showPopup();
        }
    }
}

// Set current destination (called from main script)
function setCurrentDestination(destination) {
    currentDestination = destination;
}

// Export functions for external use
window.TripPopup = {
    show: showPopup,
    hide: hidePopup,
    createTripCard: createTripCard,
    setCurrentDestination: setCurrentDestination
};

