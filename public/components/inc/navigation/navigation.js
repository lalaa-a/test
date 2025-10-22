
document.getElementById('tripsButton').addEventListener('click', function() {
    window.location.href = '/test/User/trips';
});

// User dropdown functionality
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdown');
    const userInfo = document.querySelector('.user-info');
    
    if (dropdown && userInfo) {
        const isVisible = dropdown.classList.contains('show');
        
        if (isVisible) {
            dropdown.classList.remove('show');
            userInfo.classList.remove('active');
        } else {
            dropdown.classList.add('show');
            userInfo.classList.add('active');
        }
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const userSection = document.querySelector('.user-section');
    const dropdown = document.getElementById('userDropdown');
    const userInfo = document.querySelector('.user-info');
    
    if (userSection && dropdown && userInfo && !userSection.contains(event.target)) {
        dropdown.classList.remove('show');
        userInfo.classList.remove('active');
    }
});

// Close dropdown when pressing escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const dropdown = document.getElementById('userDropdown');
        const userInfo = document.querySelector('.user-info');
        
        if (dropdown && userInfo) {
            dropdown.classList.remove('show');
            userInfo.classList.remove('active');
        }
    }
});
