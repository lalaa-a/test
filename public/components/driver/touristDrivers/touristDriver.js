// Tourist Drivers JavaScript functionality
/*
document.addEventListener('DOMContentLoaded', function() {
    console.log('Tourist Drivers page loaded');
    
    // Initialize filter functionality
    initializeFilters();
    
    // Initialize driver card interactions
    initializeDriverCards();
    
    // Initialize select driver buttons
    initializeSelectButtons();
});

function initializeFilters() {
    // Handle checkbox filters
    const checkboxes = document.querySelectorAll('.checkbox input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            console.log('Filter changed:', this.parentElement.textContent.trim());
            applyFilters();
        });
    });
    
    // Handle select filters
    const selects = document.querySelectorAll('.filter-select');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            console.log('Select filter changed:', this.value);
            applyFilters();
        });
    });
    
    // Handle rating filter
    const ratingFilter = document.querySelector('.rating-line');
    if (ratingFilter) {
        ratingFilter.addEventListener('click', function() {
            console.log('Rating filter clicked');
            // Add rating filter logic here
        });
    }
    
    // Handle price range filter
    const priceRange = document.querySelector('.range-bar');
    if (priceRange) {
        priceRange.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const percentage = clickX / rect.width;
            console.log('Price range adjusted:', Math.round(percentage * 100) + '%');
            // Add price range logic here
        });
    }
}

function initializeDriverCards() {
    const driverCards = document.querySelectorAll('.profile-card');
    
    driverCards.forEach(card => {
        // Add hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
        
        // Add click handler for card details
        card.addEventListener('click', function(e) {
            // Don't trigger if clicking the select button
            if (!e.target.classList.contains('select-driver-btn')) {
                const driverName = this.querySelector('.profile-name').textContent;
                console.log('Driver card clicked:', driverName);
                showDriverDetails(driverName);
            }
        });
    });
}

function initializeSelectButtons() {
    const selectButtons = document.querySelectorAll('.select-driver-btn');
    
    selectButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent card click
            
            const driverCard = this.closest('.profile-card');
            const driverName = driverCard.querySelector('.profile-name').textContent;
            const driverRating = driverCard.querySelector('.profile-rating').textContent;
            
            console.log('Select driver clicked:', driverName);
            selectDriver(driverName, driverRating);
        });
        
        // Add button animation
        button.addEventListener('mousedown', function() {
            this.style.transform = 'translateY(2px)';
        });
        
        button.addEventListener('mouseup', function() {
            this.style.transform = 'translateY(-2px)';
        });
    });
}

function applyFilters() {
    const driverCards = document.querySelectorAll('.profile-card');
    const selectedVehicleTypes = getSelectedCheckboxValues('Vehicle type');
    const selectedLanguage = getSelectValue('Language spoken');
    const selectedSpecializations = getSelectedCheckboxValues('Tourist specialization');
    const selectedRegion = getSelectValue('Region coverage');
    
    console.log('Applying filters:', {
        vehicleTypes: selectedVehicleTypes,
        language: selectedLanguage,
        specializations: selectedSpecializations,
        region: selectedRegion
    });
    
    driverCards.forEach(card => {
        let showCard = true;
        
        // Filter by specialization
        if (selectedSpecializations.length > 0) {
            const driverBadges = card.querySelectorAll('.badge');
            const driverSpecializations = Array.from(driverBadges).map(badge => 
                badge.textContent.toLowerCase().trim()
            );
            
            const hasMatchingSpecialization = selectedSpecializations.some(spec => 
                driverSpecializations.some(driverSpec => 
                    driverSpec.includes(spec.toLowerCase())
                )
            );
            
            if (!hasMatchingSpecialization) {
                showCard = false;
            }
        }
        
        // Show/hide card with animation
        if (showCard) {
            card.style.display = 'block';
            card.style.opacity = '0';
            setTimeout(() => {
                card.style.opacity = '1';
            }, 100);
        } else {
            card.style.opacity = '0';
            setTimeout(() => {
                card.style.display = 'none';
            }, 300);
        }
    });
}

function getSelectedCheckboxValues(filterGroupName) {
    const filterGroups = document.querySelectorAll('.filter-group');
    let selectedValues = [];
    
    filterGroups.forEach(group => {
        const label = group.querySelector('.filter-label');
        if (label && label.textContent.trim() === filterGroupName) {
            const checkboxes = group.querySelectorAll('input[type="checkbox"]:checked');
            selectedValues = Array.from(checkboxes).map(cb => 
                cb.parentElement.textContent.trim()
            );
        }
    });
    
    return selectedValues;
}

function getSelectValue(filterGroupName) {
    const filterGroups = document.querySelectorAll('.filter-group');
    let selectedValue = '';
    
    filterGroups.forEach(group => {
        const label = group.querySelector('.filter-label');
        if (label && label.textContent.trim() === filterGroupName) {
            const select = group.querySelector('.filter-select');
            if (select) {
                selectedValue = select.value;
            }
        }
    });
    
    return selectedValue;
}

function showDriverDetails(driverName) {
    // Create a modal or expand card to show more details
    console.log('Showing details for:', driverName);
    
    // For now, just alert - you can implement a proper modal later
    alert(`Driver Details for ${driverName}\n\nClick "Select Driver" to book this driver.`);
}

function selectDriver(driverName, driverRating) {
    console.log('Selecting driver:', driverName, driverRating);
    
    // Show confirmation
    const confirmation = confirm(`Do you want to select ${driverName} as your tourist driver?\n\nRating: ${driverRating}`);
    
    if (confirmation) {
        // Here you would typically send the selection to your backend
        console.log('Driver selected successfully:', driverName);
        
        // Show success message
        showSuccessMessage(`${driverName} has been selected as your tourist driver!`);
        
        // You could redirect to booking page or show next steps
    // window.location.href = '/abbb/booking?driver=' + encodeURIComponent(driverName);
    }
}

function showSuccessMessage(message) {
    // Create and show a success notification
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        z-index: 1000;
        font-family: 'Roboto', sans-serif;
        font-weight: 500;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Utility function to clear all filters
function clearAllFilters() {
    const checkboxes = document.querySelectorAll('.checkbox input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    const selects = document.querySelectorAll('.filter-select');
    selects.forEach(select => {
        select.selectedIndex = 0;
    });
    
    applyFilters();
    console.log('All filters cleared');
}

// Export functions for potential external use
window.touristDrivers = {
    clearFilters: clearAllFilters,
    applyFilters: applyFilters,
    selectDriver: selectDriver
};

*/
