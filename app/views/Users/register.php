<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Registration</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geologica:wght@100..900&family=Outfit&display=swap">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/libphonenumber-js/1.10.40/libphonenumber-js.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Geologica', sans-serif;
            height: 100vh;
            overflow: hidden;
        }
        .container {
            display: flex;
            height: 100vh;
        }
        .left-section1 {
            flex: 1.5;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url(<?php echo IMG_ROOT.'/explore/drivers/portrait-young-asian-handsome-man-with-backpack-trekking-hat-pretty-girlfriend-standing-checking-direction-paper-map-while-walking-forest-trail-backpack-travel-concept_1150-48388.jpg'?>);
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 40px;
            position: relative;
        }
        .left-section2 {
            flex: 1.5;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://placehold.co/800x600/50C878/FFFFFF?text=Guide');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 40px;
            position: relative;
        }
        .left-section3 {
            flex: 1.5;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://placehold.co/800x600/FF6B6B/FFFFFF?text=Tourist');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 40px;
            position: relative;
        }
        .location-icon1 {
            display: inline-block;
            width: 80px;
            height: 80px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.22.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/></svg>') no-repeat center center;
            background-size: contain;
            vertical-align: middle;
            margin-right: 15px;
        }
        .location-icon2 {
            display: inline-block;
            width: 70px;
            height: 70px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>') no-repeat center center;
            background-size: contain;
            vertical-align: middle;
            margin-right: 15px;
        }
        .location-icon3 {
            display: inline-block;
            width: 80px;
            height: 80px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>') no-repeat center center;
            background-size: contain;
            vertical-align: middle;
            margin-right: 15px;
        }
        .left-section1 h1, 
        .left-section2 h1, 
        .left-section3 h1 {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 30px;
        }
        .left-section1 p, 
        .left-section2 p, 
        .left-section3 p {
            font-size: 1.2rem;
            line-height: 1.6;
            max-width: 400px;
            opacity: 2;
        }
        .right-section {
            flex: 1.5;
            background: #f8f9fa;
            padding: 60px 80px;
            overflow-y: auto;
        }
        .form-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 40px;
            border: 0px solid #49a6afbc;
            border-radius: 12px;
            background: #ffffffb8;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.241);
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h2 {
            font-size: 2rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
        }
        .header p {
            color: #6b7280;
            font-size: 1rem;
        }
        .progress-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .progress-steps {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
        }
        .step.active {
            background: #006A71;
            color: white;
        }
        .step.inactive {
            background: #a7d4d4;
            color: white;
        }
        .step-connector {
            width: 130px;
            height: 2px;
            background: #a7d4d4;
            margin: 0 0px;
        }
        .step-connector.active {
            background: #006A71;
        }
        .step-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
            color: #6b7280;
            margin-left: 15px;
        }
        .step-labels .step-label {
            color: #6b7280;
            font-size: 0.875rem;
        }
        .step-labels .active-label {
            color: #2d7a7a;
            font-weight: 600;
        }
        .account-selection {
            margin-bottom: 30px;
        }
        .account-selection h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
        }
        .account-option {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: white;
        }
        .account-option:hover {
            border-color: #2d7a7a;
        }
        .account-option.selected {
            border-color: #2d7a7a;
            background: #f0f9f9;
        }
        .account-option input[type="radio"] {
            margin-right: 16px;
            width: 20px;
            height: 20px;
        }
        .account-icon {
            width: 24px;
            height: 24px;
            margin-right: 16px;
            opacity: 1.5;
        }
        .account-details h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 2px;
        }
        .account-details p {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .form-grid3 {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 30px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        .form-group label {
            font-size: 1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            opacity: 1;
        }
        .form-group input,
        .form-group select,
        .form-group textarea,
        .form-group input[type="file"] {
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
            background: white;
            transition: border-color 0.2s;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 16px;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2d7a7a;
            box-shadow: 0 0 0 3px rgba(45, 122, 122, 0.1);
        }
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #9ca3af;
        }
        .img-contain {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            align-items: center;
        }
        .photo-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            background: white;
            margin-bottom: 10px;
            cursor: pointer;
            transition: border-color 0.2s;
            width: 100px;
            height: 100px;
            position: relative;
            overflow: hidden;
        }
        .photo-upload img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            display: none;
        }
        .photo-upload.show-image img {
            display: block;
        }
        .photo-upload-icon {
            width: 40px;
            height: 40px;
            background: #f3f4f6;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .photo-upload-icon::before {
            content: '📷';
            font-size: 25px;
            margin-top: -10px;
        }
        .file-info {
            font-size: 1rem;
            color: #6b7280;
        }
        .file-info1 {
            margin-left: 140px;
            padding: 10px;
        }
        .form-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .previous-button,
        .next-button,
        .next-button1 {
            background: #35939e;
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 20px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .next-button1 {
            margin-left: 460px;
        }
        .previous-button:hover,
        .next-button:hover,
        .next-button1:hover {
            background: #236666;
        }
        .form-page {
            display: none;
        }
        .form-page.active {
            display: block;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 4px;
            display: none;
        }
        .form-group input.error,
        .form-group select.error,
        .form-group textarea.error {
            border-color: #ef4444;
        }
        .success-message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 12px;
            border-radius: 6px;
            margin-top: 20px;
            display: none;
        }
        .error-message-global {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 6px;
            margin-top: 20px;
            display: none;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            .left-section2 {
                min-height: 40vh;
                padding: 20px;
            }
            .left-section2 h1 {
                font-size: 2.5rem;
            }
            .right-section {
                padding: 40px 20px;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .next-button1 {
                margin-left: 0;
                width: 100%;
            }
            .photo-upload {
                margin-left: 0;
            }
            .file-info1 {
                margin-left: 0;
                text-align: center;
            }
            .form-grid3 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section1">
            <div class=""></div>
            <h1>Register to your account</h1>
            <p>Get the ultimate experience with us</p>
        </div>
        <div class="right-section">
            <div class="form-container">
                <div class="success-message" id="success-message">
                    Account created successfully! Redirecting to login...
                </div>
                <div class="error-message-global" id="error-message-global"></div>
                <!-- Page 1: Personal Information -->
                <div class="form-page active" id="page1">
                    <div class="header">
                        <h2>Create Your Account</h2>
                        <p>Join our community and start your journey today</p>
                    </div>
                    <div class="progress-container">
                        <div class="progress-steps">
                            <div class="step active">1</div>
                            <div class="step-connector"></div>
                            <div class="step inactive">2</div>
                            <div class="step-connector"></div>
                            <div class="step inactive">3</div>
                        </div>
                        <div class="step-labels">
                            <span class="step-label active-label">Personal Info</span>
                            <span class="step-label">Contact Details</span>
                            <span class="step-label">Verification</span>
                        </div>
                    </div>
                    <div class="account-selection">
                        <h3>Choose Your Account</h3>
                        <div class="account-option" onclick="selectAccount('tourist')">
                            <input type="radio" name="account-type" id="tourist">
                            <div class="account-icon">👤</div>
                            <div class="account-details">
                                <h4>Tourist</h4>
                                <p>Explore destination</p>
                            </div>
                        </div>
                        <div class="account-option" onclick="selectAccount('guide')">
                            <input type="radio" name="account-type" id="guide">
                            <div class="account-icon">📍</div>
                            <div class="account-details">
                                <h4>Guide</h4>
                                <p>Share local expertise</p>
                            </div>
                        </div>
                        <div class="account-option" onclick="selectAccount('driver')">
                            <input type="radio" name="account-type" id="driver">
                            <div class="account-icon">🚗</div>
                            <div class="account-details">
                                <h4>Driver</h4>
                                <p>Provide transportation</p>
                            </div>
                        </div>
                    </div>
                    <div class="form-grid" style="display:none">
                        <div class="form-group">
                            <label for="fullname">Full Name *</label>
                            <input type="text" id="fullname" placeholder="Enter your full name" required>
                            <div class="error-message" id="fullname-error">Full name is required</div>
                        </div>
                        <div class="form-group">
                            <label for="language">Preferred Language *</label>
                            <select id="language" required>
                                <option value="">Select Language</option>
                                <option value="Sinhala">Sinhala</option>
                                <option value="English">English</option>
                                <option value="Spanish">Spanish</option>
                                <option value="French">French</option>
                            </select>
                            <div class="error-message" id="language-error">Please select a language</div>
                        </div>
                        <div class="form-group">
                            <label for="dob">Date of Birth *</label>
                            <input type="date" id="dob" placeholder="mm/dd/yyyy" required>
                            <div class="error-message" id="dob-error">Date of birth is required</div>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender *</label>
                            <select id="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                            <div class="error-message" id="gender-error">Please select gender</div>
                        </div>
                        <div class="form-group full-width">
                            <label>Profile Photo *</label>
                            <div class="photo-upload" id="profile-upload">
                                <div class="photo-upload-icon"></div>
                                <img id="profile-preview" src="" alt="Profile Preview">
                            </div>
                            <div class="file-info1" id="profile-file-info">
                                <strong>Choose File</strong> No file chosen
                            </div>
                            <div class="error-message" id="profile-error">Profile photo is required</div>
                        </div>
                    </div>
                    <div class="form-actions" style="display:none">
                        <button class="next-button1" onclick="navigateTo(2)">Next</button>
                    </div>
                </div>
                <!-- Page 2: Contact Details -->
                <div class="form-page" id="page2">
                    <div class="header">
                        <h2>Create Your Account</h2>
                        <p>Join our community and start your journey today</p>
                    </div>
                    <div class="progress-container">
                        <div class="progress-steps">
                            <div class="step active">1</div>
                            <div class="step-connector active"></div>
                            <div class="step active">2</div>
                            <div class="step-connector"></div>
                            <div class="step inactive">3</div>
                        </div>
                        <div class="step-labels">
                            <span class="step-label">Personal Info</span>
                            <span class="step-label active-label">Contact Details</span>
                            <span class="step-label">Verification</span>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="pnumber">Phone Number *</label>
                            <input type="tel" id="pnumber" placeholder="Enter phone number" required>
                            <div class="error-message" id="pnumber-error">Valid phone number is required</div>
                        </div>
                        <div class="form-group">
                            <label for="spnumber">Secondary Phone (Optional)</label>
                            <input type="tel" id="spnumber" placeholder="Enter phone number">
                        </div>
                        <div class="form-group full-width">
                            <label for="address">Complete Address *</label>
                            <textarea id="address" rows="4" placeholder="Enter your full address including city, state and postal code" required></textarea>
                            <div class="error-message" id="address-error">Address is required</div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" placeholder="youremail@gmail.com" required>
                            <div class="error-message" id="email-error">Valid email is required</div>
                        </div>
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input type="password" id="password" placeholder="Enter your password (min 8 characters)" required>
                            <div class="error-message" id="password-error">Password must be at least 8 characters long</div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password *</label>
                            <input type="password" id="confirm_password" placeholder="Confirm your password" required>
                            <div class="error-message" id="confirm_password-error">Passwords do not match</div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="previous-button" onclick="navigateTo(1)">Previous</button>
                        <button class="next-button" onclick="navigateTo(3)">Next</button>
                    </div>
                </div>
                <!-- Page 3: Verification (content switches by account type) -->
                <div class="form-page" id="page3">
                    <div class="header">
                        <h2>Create Your Account</h2>
                        <p>Join our community and start your journey today</p>
                    </div>
                    <div class="progress-container">
                        <div class="progress-steps">
                            <div class="step active">1</div>
                            <div class="step-connector active"></div>
                            <div class="step active">2</div>
                            <div class="step-connector active"></div>
                            <div class="step active">3</div>
                        </div>
                        <div class="step-labels">
                            <span class="step-label">Personal Info</span>
                            <span class="step-label">Contact Details</span>
                            <span class="step-label active-label">Verification</span>
                        </div>
                    </div>
                    <div class="account-selection">
                        <h3>Driver Documents & Information</h3>
                    </div>
                    <div class="form-grid3">
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
                            <input type="file" id="licenseFront" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="licenseBack">Driving License (Back)</label>
                            <input type="file" id="licenseBack" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="vehicleDoc">Vehicle Registration Documents</label>
                            <input type="file" id="vehicleDoc" accept="application/pdf,image/*">
                        </div>
                        <div class="form-group">
                            <label for="insurance">Vehicle Insurance</label>
                            <input type="file" id="insurance" accept="application/pdf,image/*">
                        </div>
                        <div class="form-group">
                            <label for="idFront">National ID Card (Front)</label>
                            <input type="file" id="idFront" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="idBack">National ID Card (Back)</label>
                            <input type="file" id="idBack" accept="image/*">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button class="previous-button" onclick="navigateTo(2)">Previous</button>
                        <button class="next-button" onclick="submitForm()">Create Account</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Global variables
        let currentPage = 1;
        let selectedAccountType = null;
        let profileFile = null;
        let uploadedFiles = {};
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
            leftSection = document.querySelector('.left-section1');
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
            // Add input event listeners for real-time validation
            const inputs = document.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    clearError(this);
                });
            });
            
            // Add specific event listeners for password fields
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');
            
            if (passwordField) {
                passwordField.addEventListener('input', function() {
                    clearError(this);
                    if (confirmPasswordField.value && this.value !== confirmPasswordField.value) {
                        showError('confirm_password', 'Passwords do not match');
                    } else {
                        clearError(confirmPasswordField);
                    }
                });
            }
            
            if (confirmPasswordField) {
                confirmPasswordField.addEventListener('input', function() {
                    clearError(this);
                    if (passwordField.value !== this.value) {
                        showError('confirm_password', 'Passwords do not match');
                    }
                });
            }
        }
        // Navigate between pages
        function navigateTo(pageNumber) {
            if (pageNumber < 1 || pageNumber > 3) return;
            // Block navigation if no account type chosen yet
            if (pageNumber > 1 && !selectedAccountType) {
                alert('Please select Driver, Guide, or Tourist first.');
                return;
            }
            // Validate current page before navigating
            if (pageNumber === 2 && !validatePage1()) {
                return;
            }
            if (pageNumber === 3 && !validatePage2()) {
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
                    leftSection.style.background = `linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('http://localhost/test/public/img/explore/destinations/hero2.jpg')`;
                    leftSection.style.backgroundSize = 'cover';
                    leftSection.style.backgroundPosition = 'center';
                    // Update icon
                    const driverIcon = leftSection.querySelector('.location-icon1, .location-icon2, .location-icon3');
                    if (driverIcon) {
                        driverIcon.className = 'location-icon1';
                    }
                    // Update text content
                    const driverTitle = leftSection.querySelector('h1');
                    if (driverTitle) driverTitle.textContent = 'Provide Safe Transportation';
                    const driverDesc = leftSection.querySelector('p');
                    if (driverDesc) driverDesc.textContent = 'Help tourists and locals get around safely while earning from your driving skills.';
                    break;
                case 'guide':
                    leftSection.classList.add('left-section2');
                    leftSection.style.background = `linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('http://localhost/test/public/img/explore/destinations/hero3.jpg')`;
                    leftSection.style.backgroundSize = 'cover';
                    leftSection.style.backgroundPosition = 'center';
                    // Update icon
                    const guideIcon = leftSection.querySelector('.location-icon1, .location-icon2, .location-icon3');
                    if (guideIcon) {
                        guideIcon.className = 'location-icon2';
                    }
                    // Update text content
                    const guideTitle = leftSection.querySelector('h1');
                    if (guideTitle) guideTitle.textContent = 'Share Your Local Expertise';
                    const guideDesc = leftSection.querySelector('p');
                    if (guideDesc) guideDesc.textContent = 'Connect with travelers and showcase the best of your local area with authentic experiences.';
                    break;
                case 'tourist':
                    leftSection.classList.add('left-section3');
                    leftSection.style.background = `linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('http://localhost/test/public/img/explore/destinations/hero1.jpg')`;
                    leftSection.style.backgroundSize = 'cover';
                    leftSection.style.backgroundPosition = 'center';
                    // Update icon
                    const touristIcon = leftSection.querySelector('.location-icon1, .location-icon2, .location-icon3');
                    if (touristIcon) {
                        touristIcon.className = 'location-icon3';
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
            if (file.size > 5 * 1024 * 1024) { // 5MB limit
                alert('File size should be less than 5MB');
                return;
            }
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
            const fileType = file.type;
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            if (!allowedTypes.includes(fileType) || !allowedExtensions.includes(fileExtension)) {
                alert('Invalid file type. Only JPG, PNG, and PDF files are allowed.');
                return;
            }
            if (type === 'profile') {
                profileFile = file;
                const fileInfo = document.getElementById('profile-file-info');
                if (fileInfo) {
                    fileInfo.innerHTML = `<strong>Selected:</strong> ${file.name}`;
                }
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const photoUpload = document.getElementById('profile-upload');
                        const preview = document.getElementById('profile-preview');
                        if (photoUpload && preview) {
                            preview.src = e.target.result;
                            photoUpload.classList.add('show-image');
                        }
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                uploadedFiles[type] = file;
                // Handle preview for other file uploads
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewElement = document.getElementById(`${type}-preview`);
                        if (previewElement) {
                            previewElement.src = e.target.result;
                            previewElement.parentElement.classList.add('show-image');
                        }
                    };
                    reader.readAsDataURL(file);
                }
            }
        }
        // Validation functions
        function validatePage1() {
            let isValid = true;
            // Full name validation
            const fullname = document.getElementById('fullname');
            if (!fullname.value.trim()) {
                showError('fullname', 'Full name is required');
                isValid = false;
            }
            // Language validation
            const language = document.getElementById('language');
            if (!language.value) {
                showError('language', 'Please select a language');
                isValid = false;
            }
            // Date of birth validation
            const dob = document.getElementById('dob');
            if (!dob.value) {
                showError('dob', 'Date of birth is required');
                isValid = false;
            }
            // Gender validation
            const gender = document.getElementById('gender');
            if (!gender.value) {
                showError('gender', 'Please select gender');
                isValid = false;
            }
            // Profile photo validation
            if (!profileFile) {
                showError('profile', 'Profile photo is required');
                isValid = false;
            }
            return isValid;
        }
        function validatePage2() {
            let isValid = true;
            // Phone number validation with libphonenumber
            const pnumber = document.getElementById('pnumber');
            const pnumberValue = pnumber.value.trim();
            if (!pnumberValue) {
                showError('pnumber', 'Phone number is required');
                isValid = false;
            } else {
                try {
                    const phoneNumber = libphonenumber.parsePhoneNumber(pnumberValue);
                    if (!phoneNumber || !phoneNumber.isValid()) {
                        showError('pnumber', 'Please enter a valid phone number');
                        isValid = false;
                    }
                } catch (error) {
                    showError('pnumber', 'Please enter a valid phone number');
                    isValid = false;
                }
            }
            // Secondary phone validation (optional)
            const spnumber = document.getElementById('spnumber');
            const spnumberValue = spnumber.value.trim();
            if (spnumberValue) {
                try {
                    const phoneNumber = libphonenumber.parsePhoneNumber(spnumberValue);
                    if (!phoneNumber || !phoneNumber.isValid()) {
                        showError('spnumber', 'Please enter a valid secondary phone number');
                        isValid = false;
                    }
                } catch (error) {
                    showError('spnumber', 'Please enter a valid secondary phone number');
                    isValid = false;
                }
            }
            // Address validation
            const address = document.getElementById('address');
            if (!address.value.trim()) {
                showError('address', 'Address is required');
                isValid = false;
            }
            // Email validation
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim() || !emailRegex.test(email.value)) {
                showError('email', 'Valid email is required');
                isValid = false;
            }
            
            // Password validation
            const password = document.getElementById('password');
            if (!password.value) {
                showError('password', 'Password is required');
                isValid = false;
            } else if (password.value.length < 8) {
                showError('password', 'Password must be at least 8 characters long');
                isValid = false;
            }
            
            // Confirm password validation
            const confirmPassword = document.getElementById('confirm_password');
            if (!confirmPassword.value) {
                showError('confirm_password', 'Please confirm your password');
                isValid = false;
            } else if (password.value !== confirmPassword.value) {
                showError('confirm_password', 'Passwords do not match');
                isValid = false;
            }
            
            return isValid;
        }
        function validatePage3() {
            let isValid = true;
            if (selectedAccountType === 'driver') {
                const vehicleNo = document.getElementById('VehicleNo');
                const license = document.getElementById('license');
                const expireDate = document.getElementById('expireDate');
                if (!vehicleNo.value.trim()) {
                    showError('VehicleNo', 'Vehicle number is required');
                    isValid = false;
                }
                if (!license.value.trim()) {
                    showError('license', 'License number is required');
                    isValid = false;
                }
                if (!expireDate.value) {
                    showError('expireDate', 'License expire date is required');
                    isValid = false;
                }
                // Check required files for driver
                const requiredFiles = ['licenseFront', 'licenseBack', 'vehicleDoc', 'insurance', 'idFront', 'idBack'];
                requiredFiles.forEach(fileId => {
                    if (!uploadedFiles[fileId]) {
                        showError(fileId, `${fileId.replace(/([A-Z])/g, ' $1')} is required`);
                        isValid = false;
                    }
                });
            } else if (selectedAccountType === 'guide' || selectedAccountType === 'tourist') {
                const nic = document.getElementById('nic');
                if (!nic.value.trim()) {
                    showError('nic', 'NIC/Passport is required');
                    isValid = false;
                }
                // Check required files for guide/tourist
                if (!uploadedFiles['nicFront']) {
                    showError('nicFront', 'NIC/Passport front image is required');
                    isValid = false;
                }
                if (!uploadedFiles['nicBack']) {
                    showError('nicBack', 'NIC/Passport back image is required');
                    isValid = false;
                }
            }
            return isValid;
        }
        function showError(fieldId, message) {
            const errorElement = document.getElementById(`${fieldId}-error`);
            const fieldElement = document.getElementById(fieldId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
            if (fieldElement) {
                fieldElement.classList.add('error');
            }
        }
        function clearError(element) {
            const errorElement = document.getElementById(`${element.id}-error`);
            if (errorElement) {
                errorElement.style.display = 'none';
            }
            element.classList.remove('error');
        }
        // Submit form to backend
        function submitForm() {
            if (!validatePage3()) {
                return;
            }
            // Create FormData object
            const formData = new FormData();
            formData.append('account_type', selectedAccountType);
            // Page 1 data
            formData.append('fullname', document.getElementById('fullname').value);
            formData.append('language', document.getElementById('language').value);
            formData.append('dob', document.getElementById('dob').value);
            formData.append('gender', document.getElementById('gender').value);
            if (profileFile) {
                formData.append('profile_photo', profileFile);
            }
            // Page 2 data
            formData.append('phone', document.getElementById('pnumber').value);
            formData.append('secondary_phone', document.getElementById('spnumber').value);
            formData.append('address', document.getElementById('address').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('password', document.getElementById('password').value);
            formData.append('confirm_password', document.getElementById('confirm_password').value);
            // Page 3 data based on account type
            if (selectedAccountType === 'driver') {
                formData.append('vehicle_number', document.getElementById('VehicleNo').value);
                formData.append('license_number', document.getElementById('license').value);
                formData.append('license_expire_date', document.getElementById('expireDate').value);
                // Add driver files
                const driverFiles = ['licenseFront', 'licenseBack', 'vehicleDoc', 'insurance', 'idFront', 'idBack'];
                driverFiles.forEach(fileId => {
                    if (uploadedFiles[fileId]) {
                        formData.append(fileId, uploadedFiles[fileId]);
                    }
                });
            } else if (selectedAccountType === 'guide' || selectedAccountType === 'tourist') {
                formData.append('nic_passport', document.getElementById('nic').value);
                // Add guide/tourist files
                if (uploadedFiles['nicFront']) {
                    formData.append('nic_front', uploadedFiles['nicFront']);
                }
                if (uploadedFiles['nicBack']) {
                    formData.append('nic_back', uploadedFiles['nicBack']);
                }
            }
            // Show loading message
            const submitButton = document.querySelector('#page3 .next-button');
            const originalText = submitButton.textContent;
            submitButton.textContent = 'Creating Account...';
            submitButton.disabled = true;
            // Send to PHP backend
            fetch('/test/User/register', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    document.getElementById('success-message').style.display = 'block';
                    document.getElementById('error-message-global').style.display = 'none';
                    // Redirect after 3 seconds
                    setTimeout(() => {
                        window.location.href = '/test/User/login';     //redirect to the login page if it a success
                    }, 3000);
                } else {
                    // Show error message
                    const errorDiv = document.getElementById('error-message-global');
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorDiv = document.getElementById('error-message-global');
                errorDiv.textContent = 'An error occurred during registration. Please try again.';
                errorDiv.style.display = 'block';
                submitButton.textContent = originalText;
                submitButton.disabled = false;
            });
        }
        // Update 3rd step content based on account type
        function updateThirdStepContent(accountType) {
            const page3 = document.getElementById('page3');
            if (!page3) return;
            const accountSelection = page3.querySelector('.account-selection');
            const formGrid = page3.querySelector('.form-grid3');
            if (!accountSelection || !formGrid) return;
            switch (accountType) {
                case 'driver':
                    accountSelection.innerHTML = '<h3>Driver Documents & Information</h3>';
                    formGrid.innerHTML = `
                        <div class="form-group">
                            <label for="VehicleNo">Vehicle Number *</label>
                            <input type="text" id="VehicleNo" placeholder="Enter vehicle number" required>
                            <div class="error-message" id="VehicleNo-error">Vehicle number is required</div>
                        </div>
                        <div class="form-group">
                            <label for="license">Driving License Number *</label>
                            <input type="text" id="license" placeholder="License number" required>
                            <div class="error-message" id="license-error">License number is required</div>
                        </div>
                        <div class="form-group">
                            <label for="expireDate">License Expire Date *</label>
                            <input type="date" id="expireDate" placeholder="mm/dd/yyyy" required>
                            <div class="error-message" id="expireDate-error">License expire date is required</div>
                        </div>
                        <div class="form-group">
                            <label for="licenseFront">Driving License (Front) *</label>
                            <input type="file" id="licenseFront" accept="image/*" required onchange="handleFileUpload(this.files[0], 'licenseFront')">
                            <div class="error-message" id="licenseFront-error">License front image is required</div>
                        </div>
                        <div class="form-group">
                            <label for="licenseBack">Driving License (Back) *</label>
                            <input type="file" id="licenseBack" accept="image/*" required onchange="handleFileUpload(this.files[0], 'licenseBack')">
                            <div class="error-message" id="licenseBack-error">License back image is required</div>
                        </div>
                        <div class="form-group">
                            <label for="vehicleDoc">Vehicle Registration Documents *</label>
                            <input type="file" id="vehicleDoc" accept="application/pdf,image/*" required onchange="handleFileUpload(this.files[0], 'vehicleDoc')">
                            <div class="error-message" id="vehicleDoc-error">Vehicle registration documents are required</div>
                        </div>
                        <div class="form-group">
                            <label for="insurance">Vehicle Insurance *</label>
                            <input type="file" id="insurance" accept="application/pdf,image/*" required onchange="handleFileUpload(this.files[0], 'insurance')">
                            <div class="error-message" id="insurance-error">Vehicle insurance is required</div>
                        </div>
                        <div class="form-group">
                            <label for="idFront">National ID Card (Front) *</label>
                            <input type="file" id="idFront" accept="image/*" required onchange="handleFileUpload(this.files[0], 'idFront')">
                            <div class="error-message" id="idFront-error">ID card front image is required</div>
                        </div>
                        <div class="form-group">
                            <label for="idBack">National ID Card (Back) *</label>
                            <input type="file" id="idBack" accept="image/*" required onchange="handleFileUpload(this.files[0], 'idBack')">
                            <div class="error-message" id="idBack-error">ID card back image is required</div>
                        </div>
                    `;
                    break;
                case 'guide':
                    accountSelection.innerHTML = '<h3>Guide Documents & Information</h3>';
                    formGrid.innerHTML = `
                        <div class="form-group">
                            <label for="nic">NIC/Passport *</label>
                            <input type="text" id="nic" placeholder="Enter your nic/passport" required>
                            <div class="error-message" id="nic-error">NIC/Passport is required</div>
                        </div>
                        <div class="form-group full-width">
                            <div class="img-contain">
                                <div class="file-info">
                                    <strong>Front img: </strong>NIC/Passport *
                                </div>
                                <div class="photo-upload" id="nicFront-upload" onclick="document.getElementById('nicFront').click()">
                                    <div class="photo-upload-icon"></div>
                                    <img id="nicFront-preview" src="" alt="NIC Front Preview">
                                </div>
                                <input type="file" id="nicFront" accept="image/*" style="display:none" required onchange="handleFileUpload(this.files[0], 'nicFront')">
                            </div>
                            <div class="error-message" id="nicFront-error">NIC/Passport front image is required</div>
                        </div>
                        <div class="form-group full-width">
                            <div class="img-contain">
                                <div class="file-info">
                                    <strong>Back img: </strong>NIC/Passport *
                                </div>
                                <div class="photo-upload" id="nicBack-upload" onclick="document.getElementById('nicBack').click()">
                                    <div class="photo-upload-icon"></div>
                                    <img id="nicBack-preview" src="" alt="NIC Back Preview">
                                </div> 
                                <input type="file" id="nicBack" accept="image/*" style="display:none" required onchange="handleFileUpload(this.files[0], 'nicBack')">
                            </div>
                            <div class="error-message" id="nicBack-error">NIC/Passport back image is required</div>
                        </div>
                    `;
                    break;
                case 'tourist':
                    accountSelection.innerHTML = '<h3>Tourist Documents & Information</h3>';
                    formGrid.innerHTML = `
                        <div class="form-group">
                            <label for="nic">NIC/Passport *</label>
                            <input type="text" id="nic" placeholder="Enter your nic/passport" required>
                            <div class="error-message" id="nic-error">NIC/Passport is required</div>
                        </div>
                        <div class="form-group full-width">
                            <div class="img-contain">
                                <div class="file-info">
                                    <strong>Front img: </strong>NIC/Passport *
                                </div>
                                <div class="photo-upload" id="nicFront-upload" onclick="document.getElementById('nicFront').click()">
                                    <div class="photo-upload-icon"></div>
                                    <img id="nicFront-preview" src="" alt="NIC Front Preview">
                                </div>
                                <input type="file" id="nicFront" accept="image/*" style="display:none" required onchange="handleFileUpload(this.files[0], 'nicFront')">
                            </div>
                            <div class="error-message" id="nicFront-error">NIC/Passport front image is required</div>
                        </div>
                        <div class="form-group full-width">
                            <div class="img-contain">
                                <div class="file-info">
                                    <strong>Back img: </strong>NIC/Passport *
                                </div>
                                <div class="photo-upload" id="nicBack-upload" onclick="document.getElementById('nicBack').click()">
                                    <div class="photo-upload-icon"></div>
                                    <img id="nicBack-preview" src="" alt="NIC Back Preview">
                                </div> 
                                <input type="file" id="nicBack" accept="image/*" style="display:none" required onchange="handleFileUpload(this.files[0], 'nicBack')">
                            </div>
                            <div class="error-message" id="nicBack-error">NIC/Passport back image is required</div>
                        </div>
                    `;
                    break;
            }
            // Reattach event listeners for new file inputs
            const newFileInputs = formGrid.querySelectorAll('input[type="file"]');
            newFileInputs.forEach(input => {
                input.addEventListener('change', function(e) {
                    if (e.target.files[0]) {
                        const fieldId = e.target.id;
                        handleFileUpload(e.target.files[0], fieldId);
                        clearError(e.target);
                    }
                });
            });
        }
    </script>
</body>
</html>