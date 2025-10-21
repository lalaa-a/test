// Google Maps Integration
function initMap() {
    // Sigiriya coordinates (Central Province, Sri Lanka)
    const sigiriya = { lat: 7.9569, lng: 80.7597 };
    
    // Create the map centered on Sigiriya
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: sigiriya,
        mapTypeId: google.maps.MapTypeId.TERRAIN,
        styles: [
            {
                featureType: "poi",
                elementType: "labels",
                stylers: [{ visibility: "off" }]
            }
        ]
    });

    // Add a marker for Sigiriya Rock Fortress
    const marker = new google.maps.Marker({
        position: sigiriya,
        map: map,
        title: "Sigiriya Rock Fortress",
        icon: {
            url: "https://maps.google.com/mapfiles/ms/icons/red-dot.png",
            scaledSize: new google.maps.Size(32, 32)
        }
    });

    // Add info window for Sigiriya
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div style="padding: 10px; max-width: 200px;">
                <h3 style="margin: 0 0 10px 0; color: #006a71;">Sigiriya Rock Fortress</h3>
                <p style="margin: 0; font-size: 14px;">
                    Ancient palace and fortress complex built by King Kashyapa in the 5th century AD. 
                    UNESCO World Heritage Site.
                </p>
            </div>
        `
    });

    marker.addListener("click", () => {
        infoWindow.open(map, marker);
    });

    // Add nearby attractions
    const nearbyAttractions = [
        {
            name: "Pidurangala Rock",
            position: { lat: 7.9667, lng: 80.7500 },
            description: "Alternative viewpoint of Sigiriya"
        },
        {
            name: "Dambulla Cave Temple",
            position: { lat: 7.8567, lng: 80.6492 },
            description: "Ancient Buddhist temple complex"
        },
        {
            name: "Minneriya National Park",
            position: { lat: 7.9833, lng: 80.9000 },
            description: "Wildlife sanctuary famous for elephants"
        }
    ];

    nearbyAttractions.forEach(attraction => {
        const attractionMarker = new google.maps.Marker({
            position: attraction.position,
            map: map,
            title: attraction.name,
            icon: {
                url: "https://maps.google.com/mapfiles/ms/icons/blue-dot.png",
                scaledSize: new google.maps.Size(24, 24)
            }
        });

        const attractionInfo = new google.maps.InfoWindow({
            content: `
                <div style="padding: 8px; max-width: 180px;">
                    <h4 style="margin: 0 0 5px 0; color: #006a71;">${attraction.name}</h4>
                    <p style="margin: 0; font-size: 12px;">${attraction.description}</p>
                </div>
            `
        });

        attractionMarker.addListener("click", () => {
            attractionInfo.open(map, attractionMarker);
        });
    });
}

// Smooth Scrolling Navigation
document.addEventListener('DOMContentLoaded', function() {
    // Add save to trip button functionality
    const saveToTripBtn = document.getElementById('saveToTripBtn');
    if (saveToTripBtn) {
        saveToTripBtn.addEventListener('click', function() {
            // Get destination data from the page
            const destinationName = document.querySelector('.hero-text h1')?.textContent || 'Destination';
            const destinationImage = document.querySelector('.main-image img')?.src || 'assets/sigiriya.jpg';
            const destinationLocation = document.querySelector('.detail-value')?.textContent || 'Sri Lanka';
            
            // Create destination object
            const destination = {
                id: window.location.pathname.split('/').pop() || '1',
                name: destinationName,
                location: destinationLocation,
                image: destinationImage
            };
            
            // Set current destination and show popup directly
            if (window.TripPopup) {
                window.TripPopup.setCurrentDestination(destination);
                window.TripPopup.show();
            }
        });
    }
    // Get all navigation links
    const navLinks = document.querySelectorAll('.nav a, .tab');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetSection = document.querySelector(targetId);
            
            if (targetSection) {
                // Calculate offset for fixed header
                const headerHeight = document.querySelector('.header').offsetHeight;
                const navTabsHeight = document.querySelector('.nav-tabs').offsetHeight;
                const totalOffset = headerHeight + navTabsHeight;
                
                const targetPosition = targetSection.offsetTop - totalOffset;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                // Update active tab
                updateActiveTab(targetId);
            }
        });
    });

    // Update active tab based on scroll position
    function updateActiveTab(targetId) {
        // Remove active class from all tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Add active class to current tab
        const currentTab = document.querySelector(`[href="${targetId}"]`);
        if (currentTab) {
            currentTab.classList.add('active');
        }
    }

    // Scroll spy for navigation tabs
    window.addEventListener('scroll', function() {
        const sections = document.querySelectorAll('.section');
        const navTabs = document.querySelectorAll('.tab');
        const headerHeight = document.querySelector('.header').offsetHeight;
        const navTabsHeight = document.querySelector('.nav-tabs').offsetHeight;
        const totalOffset = headerHeight + navTabsHeight;

        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop - totalOffset - 100;
            const sectionHeight = section.clientHeight;
            
            if (window.pageYOffset >= sectionTop && window.pageYOffset < sectionTop + sectionHeight) {
                current = section.getAttribute('id');
            }
        });

        navTabs.forEach(tab => {
            tab.classList.remove('active');
            if (tab.getAttribute('href') === `#${current}`) {
                tab.classList.add('active');
            }
        });
    });

    // Guide card interactions
    const guideCards = document.querySelectorAll('.guide-card');
    
    guideCards.forEach(card => {
        const selectButton = card.querySelector('.btn-primary');
        
        selectButton.addEventListener('click', function() {
            const guideName = card.querySelector('h3').textContent;
            const guideRole = this.textContent.includes('Driver') ? 'Driver' : 'Guide';
            
            // Show selection confirmation
            showNotification(`You have selected ${guideName} as your ${guideRole}!`, 'success');
            
            // Add visual feedback
            this.textContent = 'Selected!';
            this.style.background = '#28a745';
            this.disabled = true;
        });
    });

    // Rating bars animation
    const ratingBars = document.querySelectorAll('.bar-fill');
    
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const width = bar.style.width;
                bar.style.width = '0%';
                
                setTimeout(() => {
                    bar.style.width = width;
                }, 200);
                
                observer.unobserve(bar);
            }
        });
    }, observerOptions);

    ratingBars.forEach(bar => {
        observer.observe(bar);
    });

    // Smooth scroll for guide cards
    const guidesSection = document.querySelector('#guides');
    if (guidesSection) {
        const guidesCards = guidesSection.querySelector('.guides-cards');
        
        // Add scroll indicators
        const scrollLeft = document.createElement('button');
        scrollLeft.className = 'scroll-indicator scroll-left';
        scrollLeft.innerHTML = '<i class="fas fa-chevron-left"></i>';
        scrollLeft.style.cssText = `
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 106, 113, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
        `;

        const scrollRight = document.createElement('button');
        scrollRight.className = 'scroll-indicator scroll-right';
        scrollRight.innerHTML = '<i class="fas fa-chevron-right"></i>';
        scrollRight.style.cssText = `
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 106, 113, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
        `;

        guidesSection.style.position = 'relative';
        guidesSection.appendChild(scrollLeft);
        guidesSection.appendChild(scrollRight);

        // Scroll functionality
        scrollLeft.addEventListener('click', () => {
            guidesCards.scrollBy({
                left: -350,
                behavior: 'smooth'
            });
        });

        scrollRight.addEventListener('click', () => {
            guidesCards.scrollBy({
                left: 350,
                behavior: 'smooth'
            });
        });

        // Show/hide scroll indicators based on scroll position
        guidesCards.addEventListener('scroll', () => {
            scrollLeft.style.display = guidesCards.scrollLeft > 0 ? 'block' : 'none';
            scrollRight.style.display = 
                guidesCards.scrollLeft < (guidesCards.scrollWidth - guidesCards.clientWidth) ? 'block' : 'none';
        });

        // Initial state
        scrollLeft.style.display = 'none';
    }
});

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Style the notification
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'success' ? '#28a745' : '#006a71'};
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        z-index: 10000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        max-width: 300px;
        font-weight: 500;
    `;

    document.body.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add loading state for buttons
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-primary') || e.target.classList.contains('btn-secondary')) {
        const button = e.target;
        const originalText = button.textContent;
        
        // Add loading state
        button.textContent = 'Loading...';
        button.disabled = true;
        
        // Simulate loading (remove this in production)
        setTimeout(() => {
            button.textContent = originalText;
            button.disabled = false;
        }, 1000);
    }
});

// Add parallax effect to hero section
window.addEventListener('scroll', function() {
    const scrolled = window.pageYOffset;
    const hero = document.querySelector('.hero');
    
    if (hero) {
        const rate = scrolled * -0.5;
        hero.style.transform = `translateY(${rate}px)`;
    }
});

// Add intersection observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe sections for animation
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.section');
    
    sections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(30px)';
        section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(section);
    });
});

// Add keyboard navigation support
document.addEventListener('keydown', function(e) {
    if (e.key === 'Tab') {
        // Add focus styles for keyboard navigation
        document.body.classList.add('keyboard-navigation');
    }
});

document.addEventListener('mousedown', function() {
    // Remove keyboard navigation styles on mouse use
    document.body.classList.remove('keyboard-navigation');
});

// Add CSS for keyboard navigation
const style = document.createElement('style');
style.textContent = `
    .keyboard-navigation .nav a:focus,
    .keyboard-navigation .tab:focus,
    .keyboard-navigation .btn-primary:focus,
    .keyboard-navigation .btn-secondary:focus {
        outline: 3px solid #006a71;
        outline-offset: 2px;
    }
`;
document.head.appendChild(style);