// Global variables
let currentPage = 1;
let selectedAccountType = null; // No default selection at load

// DOM elements
let leftSection;
let progressSteps;
let stepConnectors;
let stepLabels;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeElements();
    setupEventListeners();
    updateProgressIndicator();
    updatePage1Visibility();
});

// Initialize DOM elements
function initializeElements() {
    leftSection = document.querySelector('.left-section1, .left-section2, .left-section3');
    progressSteps = document.querySelectorAll('.step');
    stepConnectors = document.querySelectorAll('.step-connector');
    stepLabels = document.querySelectorAll('.step-label');

    // Ensure no account is selected on load
    const accountOptions = document.querySelectorAll('.account-option');
    accountOptions.forEach(option => {
        option.classList.remove('selected');
        const radio = option.querySelector('input[type="radio"]');
        if (radio) radio.checked = false;
    });

    // Do not infer selection from left section; wait for explicit user choice
    selectedAccountType = null;
}

// Helper: toggle visibility of step-1 content until a selection is made
function updatePage1Visibility() {
    const page1 = document.getElementById('page1');
    if (!page1) return;

    const grids = page1.querySelectorAll('.form-grid');
    const actions = page1.querySelectorAll('.form-actions');
    const nextButtons = page1.querySelectorAll('.next-button, .next-button1');

    const shouldShow = !!selectedAccountType;

    grids.forEach(el => { el.style.display = shouldShow ? '' : 'none'; });
    actions.forEach(el => { el.style.display = shouldShow ? '' : 'none'; });

    nextButtons.forEach(btn => { if (btn) btn.disabled = !shouldShow; });
}

// Setup event listeners
function setupEventListeners() {
    // Account selection radio buttons
    const accountOptions = document.querySelectorAll('.account-option');
    accountOptions.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (!radio) return;
            const accountType = radio.id;
            selectAccount(accountType);
            updatePage1Visibility();
        });
    });
    
    // File upload functionality
    const profileUpload = document.getElementById('profile-upload');
    if (profileUpload) {
        profileUpload.addEventListener('click', function() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = function(e) {
                handleFileUpload(e.target.files[0], 'profile');
            };
            input.click();
        });
    }
}

// Navigate between pages
function navigateTo(pageNumber) {
    if (pageNumber < 1 || pageNumber > 3) return;

    // Block navigation if no account type chosen yet
    if (!selectedAccountType) {
        alert('Please select Driver, Guide, or Tourist first.');
        return;
    }
    
    // Hide current page
    const currentPageElement = document.getElementById(`page${currentPage}`);
    if (currentPageElement) {
        currentPageElement.classList.remove('active');
    }
    
    // Show target page
    const targetPageElement = document.getElementById(`page${pageNumber}`);
    if (targetPageElement) {
        targetPageElement.classList.add('active');
    }
    
    // Update current page
    currentPage = pageNumber;
    
    // Update progress indicator
    updateProgressIndicator();
}

// Update progress indicator
function updateProgressIndicator() {
    // Work only within the currently visible page
    const activePage = document.querySelector('.form-page.active');
    if (!activePage) return;

    const localSteps = activePage.querySelectorAll('.step');
    const localConnectors = activePage.querySelectorAll('.step-connector');
    const localLabels = activePage.querySelectorAll('.step-label');

    // Update step circles
    localSteps.forEach((step, index) => {
        if (index + 1 <= currentPage) {
            step.classList.add('active');
            step.classList.remove('inactive');
        } else {
            step.classList.remove('active');
            step.classList.add('inactive');
        }
    });
    
    // Update step connectors
    localConnectors.forEach((connector, index) => {
        if (index + 1 < currentPage) {
            connector.classList.add('active');
        } else {
            connector.classList.remove('active');
        }
    });
    
    // Update step labels
    localLabels.forEach((label, index) => {
        if (index + 1 === currentPage) {
            label.classList.add('active-label');
        } else {
            label.classList.remove('active-label');
        }
    });
}

// Select account type and update left section
function selectAccount(accountType) {
    selectedAccountType = accountType;
    
    // Update radio button selection
    const accountOptions = document.querySelectorAll('.account-option');
    accountOptions.forEach(option => {
        option.classList.remove('selected');
        const radio = option.querySelector('input[type="radio"]');
        if (radio.id === accountType) {
            option.classList.add('selected');
            radio.checked = true;
        } else {
            radio.checked = false;
        }
    });
    
    // Update left section based on account type
    updateLeftSection(accountType);
    
    // Update 3rd step content based on account type
    updateThirdStepContent(accountType);
}

// Update left section image and content
function updateLeftSection(accountType) {
    if (!leftSection) return;
    
    // Remove existing classes
    leftSection.classList.remove('left-section1', 'left-section2', 'left-section3');
    
    // Add appropriate class and update background
    switch (accountType) {
        case 'driver':
            leftSection.classList.add('left-section1');
            leftSection.style.background = `linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('img/signup/driver.jpg')`;
            leftSection.style.backgroundSize = 'cover';
            leftSection.style.backgroundPosition = 'center';
            
            // Update icon
            const driverIcon = leftSection.querySelector('.location-icon1, .location-icon2, .location-icon3');
            if (driverIcon) {
                driverIcon.className = 'location-icon1';
                driverIcon.style.background = `url('img/signup/car.png') no-repeat center center`;
                driverIcon.style.backgroundSize = 'contain';
            }
            
            // Update text content
            const driverTitle = leftSection.querySelector('h1');
            if (driverTitle) driverTitle.textContent = 'Provide Safe Transportation';
            
            const driverDesc = leftSection.querySelector('p');
            if (driverDesc) driverDesc.textContent = 'Help tourists and locals get around safely while earning from your driving skills.';
            break;
            
        case 'guide':
            leftSection.classList.add('left-section2');
            leftSection.style.background = `linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('img/signup/guide.jpg')`;
            leftSection.style.backgroundSize = 'cover';
            leftSection.style.backgroundPosition = 'center';
            
            // Update icon
            const guideIcon = leftSection.querySelector('.location-icon1, .location-icon2, .location-icon3');
            if (guideIcon) {
                guideIcon.className = 'location-icon2';
                guideIcon.style.background = `url('img/signup/location.png') no-repeat center center`;
                guideIcon.style.backgroundSize = 'contain';
            }
            
            // Update text content
            const guideTitle = leftSection.querySelector('h1');
            if (guideTitle) guideTitle.textContent = 'Share Your Local Expertise';
            
            const guideDesc = leftSection.querySelector('p');
            if (guideDesc) guideDesc.textContent = 'Connect with travelers and showcase the best of your local area with authentic experiences.';
            break;
            
        case 'tourist':
            leftSection.classList.add('left-section3');
            leftSection.style.background = `linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('img/signup/tourist.jpg')`;
            leftSection.style.backgroundSize = 'cover';
            leftSection.style.backgroundPosition = 'center';
            
            // Update icon
            const touristIcon = leftSection.querySelector('.location-icon1, .location-icon2, .location-icon3');
            if (touristIcon) {
                touristIcon.className = 'location-icon3';
                touristIcon.style.background = `url('img/signup/people.png') no-repeat center center`;
                touristIcon.style.backgroundSize = 'contain';
            }
            
            // Update text content
            const touristTitle = leftSection.querySelector('h1');
            if (touristTitle) touristTitle.textContent = 'Explore Amazing Destinations';
            
            const touristDesc = leftSection.querySelector('p');
            if (touristDesc) touristDesc.textContent = 'Discover new places, connect with local guides, and create unforgettable travel memories.';
            break;
    }
}

// Handle file upload
function handleFileUpload(file, type) {
    if (!file) return;
    
    const fileInfo = document.querySelector('.file-info1');
    if (fileInfo) {
        fileInfo.innerHTML = `<strong>Selected:</strong> ${file.name}`;
    }
    
    // You can add additional file validation here
    if (file.size > 5 * 1024 * 1024) { // 5MB limit
        alert('File size should be less than 5MB');
        return;
    }
    
    // You can add file preview functionality here
    if (type === 'profile' && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const photoUpload = document.getElementById('profile-upload');
            if (photoUpload) {
                photoUpload.style.backgroundImage = `url(${e.target.result})`;
                photoUpload.style.backgroundSize = 'cover';
                photoUpload.style.backgroundPosition = 'center';
            }
        };
        reader.readAsDataURL(file);
    }
}

// Submit form (placeholder function)
function submitForm() {
    // Validate form data
    if (!validateForm()) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Show success message
    alert('Account created successfully!');
    
    // You can add form submission logic here
    console.log('Form submitted with account type:', selectedAccountType);
}

// Validate form (basic validation)
function validateForm() {
    const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.style.borderColor = '#ef4444';
        } else {
            field.style.borderColor = '#d1d5db';
        }
    });
    
    return isValid;
}

// Reset form validation styling
function resetValidation() {
    const fields = document.querySelectorAll('input, select, textarea');
    fields.forEach(field => {
        field.style.borderColor = '#d1d5db';
    });
}

// Add input event listeners for real-time validation
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.style.borderColor === 'rgb(239, 68, 68)') {
                this.style.borderColor = '#d1d5db';
            }
        });
    });
});

// Update 3rd step content based on account type
function updateThirdStepContent(accountType) {
    const page3 = document.getElementById('page3');
    if (!page3) return;
    
    const accountSelection = page3.querySelector('.account-selection');
    const formGrid = page3.querySelector('.form-grid, .form-grid3');
    
    if (!accountSelection || !formGrid) return;
    
    switch (accountType) {
        case 'driver':
            accountSelection.innerHTML = '<h3>Driver Documents & Information</h3>';
            formGrid.className = 'form-grid3';
            formGrid.innerHTML = `
                <div class="form-group">
                    <label for="VehicleNo">Vehicle Number</label>
                    <input type="text" id="VehicleNo" placeholder="Enter vehicle number">
                </div>
                <div class="form-group">
                    <label for="license">Driving License Number</label>
                    <input type="text" id="license" placeholder="License number">
                </div>
                
                <div class="form-group">
                    <label for="expireDate">License Expire Date</label>
                    <input type="date" id="expireDate" placeholder="mm/dd/yyyy">
                </div>
                
                <div class="form-group">
                    <label for="licenseFront">Driving License (Front)</label>
                    <input type="file" id="licenseFront">
                </div>
                
                <div class="form-group">
                    <label for="licenseBack">Driving License (Back)</label>
                    <input type="file" id="licenseBack">
                </div>
                
                <div class="form-group">
                    <label for="vehicleDoc">Vehicle Registration Documents</label>
                    <input type="file" id="vehicleDoc">
                </div>
                
                <div class="form-group">
                    <label for="insurance">Vehicle Insurance</label>
                    <input type="file" id="insurance">
                </div>
                
                <div class="form-group">
                    <label for="idFront">National ID Card (Front)</label>
                    <input type="file" id="idFront">
                </div>
                
                <div class="form-group">
                    <label for="idBack">National ID Card (Back)</label>
                    <input type="file" id="idBack">
                </div>
            `;
            break;
            
        case 'guide':
            accountSelection.innerHTML = '<h3>Guide Documents & Information</h3>';
            formGrid.className = 'form-grid';
            formGrid.innerHTML = `
                <div class="form-group">
                    <label for="nic">NIC/Passport</label>
                    <input type="text" id="nic" placeholder="Enter your nic/passport">
                </div>

                <div class="form-group full-width">
                    <div class="img-contain">
                        <div class="file-info">
                            <strong>Front img: </strong>NIC/Passport
                        </div>
                        <div class="photo-upload">
                            <div class="photo-upload-icon"></div>
                        </div>
                    </div>
                </div>
                <div class="form-group full-width">
                    <div class="img-contain">
                        <div class="file-info">
                            <strong>Back img: </strong>NIC/Passport
                        </div>
                        <div class="photo-upload">
                            <div class="photo-upload-icon"></div>
                        </div> 
                    </div>
                </div>
            `;
            break;
            
        case 'tourist':
            accountSelection.innerHTML = '<h3>Tourist Documents & Information</h3>';
            formGrid.className = 'form-grid';
            formGrid.innerHTML = `
                
                <div class="form-group">
                    <label for="nic">NIC/Passport</label>
                    <input type="text" id="nic" placeholder="Enter your nic/passport">
                </div>

                <div class="form-group full-width">
                    <div class="img-contain">
                        <div class="file-info">
                            <strong>Front img: </strong>NIC/Passport
                        </div>
                        <div class="photo-upload">
                            <div class="photo-upload-icon"></div>
                        </div>
                    </div>
                </div>
                <div class="form-group full-width">
                    <div class="img-contain">
                        <div class="file-info">
                            <strong>Back img: </strong>NIC/Passport
                        </div>
                        <div class="photo-upload">
                            <div class="photo-upload-icon"></div>
                        </div> 
                    </div>
                </div>
            `;
            break;
    }
}
