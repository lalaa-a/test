// Driver Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeDestinationCards();
    initializeDriverCards();
    initializeScrollBehavior();
});

// Destination cards interaction
function initializeDestinationCards() {
    const destinationCards = document.querySelectorAll('.destination-card');
    
    destinationCards.forEach(card => {
        card.addEventListener('click', function() {
            const title = this.querySelector('.destination-title').textContent;
            console.log(`Destination clicked: ${title}`);
            // Add navigation logic here
            // Example: window.location.href = `destination.php?location=${title.toLowerCase()}`;
        });
        
        // Add keyboard navigation
        card.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });
}

// Driver cards interaction
function initializeDriverCards() {
    const driverCards = document.querySelectorAll('.driver-card');
    const selectButtons = document.querySelectorAll('.select-driver-btn');
    
    // Add hover effects
    driverCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Handle driver selection
    selectButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const driverCard = this.closest('.driver-card');
            const driverName = driverCard.querySelector('.driver-name').textContent;
            const driverRating = driverCard.querySelector('.rating').textContent;
            
           
            
            console.log(`Driver selected: ${driverName} (Rating: ${driverRating})`);
            
            // Here you can add logic to store the selected driver
            // Example: localStorage.setItem('selectedDriver', JSON.stringify({name: driverName, rating: driverRating}));
        });
    });
}

// Scroll behavior for driver containers
function initializeScrollBehavior() {
    const driversContainers = document.querySelectorAll('.drivers-container:not(.no-scroll)');
    
    driversContainers.forEach(container => {
        // Add smooth scrolling
        container.style.scrollBehavior = 'smooth';
        
        // Optional: Add scroll indicators
        if (container.scrollWidth > container.clientWidth) {
            addScrollIndicators(container);
        }
    });
}

// Add event listeners to all explore destination buttons
document.querySelectorAll('.select-driver-btn').forEach(button => {
    button.addEventListener('click', function() {
        window.location.href = 'DriverController/driverDetail'
    });
});

// Add scroll indicators for horizontally scrollable containers
function addScrollIndicators(container) {
    const wrapper = container.parentElement;
    
    // Create left and right scroll buttons
    const leftBtn = document.createElement('button');
    leftBtn.innerHTML = '‹';
    leftBtn.className = 'scroll-btn scroll-btn-left';
    leftBtn.style.cssText = `
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.9);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 20px;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 10;
        display: none;
    `;
    
    const rightBtn = document.createElement('button');
    rightBtn.innerHTML = '›';
    rightBtn.className = 'scroll-btn scroll-btn-right';
    rightBtn.style.cssText = leftBtn.style.cssText.replace('left: 10px', 'right: 10px');
    
    wrapper.style.position = 'relative';
    wrapper.appendChild(leftBtn);
    wrapper.appendChild(rightBtn);
    
    // Scroll functionality
    leftBtn.addEventListener('click', () => {
        container.scrollBy({ left: -300, behavior: 'smooth' });
    });
    
    rightBtn.addEventListener('click', () => {
        container.scrollBy({ left: 300, behavior: 'smooth' });
    });
    
    // Show/hide scroll buttons based on scroll position
    function updateScrollButtons() {
        leftBtn.style.display = container.scrollLeft > 0 ? 'block' : 'none';
        rightBtn.style.display = 
            container.scrollLeft < (container.scrollWidth - container.clientWidth) ? 'block' : 'none';
    }
    
    container.addEventListener('scroll', updateScrollButtons);
    updateScrollButtons();
}

// Utility functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 5px;
        color: white;
        font-weight: 600;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s ease;
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
    `;
    
    document.body.appendChild(notification);
    
    // Fade in
    setTimeout(() => notification.style.opacity = '1', 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}

// Export functions for external use
window.DriverPage = {
    showNotification,
    selectDriver: function(driverName) {
        console.log(`Programmatically selected driver: ${driverName}`);
        showNotification(`Selected driver: ${driverName}`, 'success');
    }
};