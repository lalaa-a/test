<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Registration</title>
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/css/signup/login.css">
</head>
<body>
    <div class="container">
        <div class="left-section1">
            <div class="location-icon1"></div>
            <h1>Provide Safe Transportation</h1>
            <p>Help tourists and locals get around safely while earning from your driving skills.</p>
        </div>

        <div class="right-section">
            <div class="form-container">
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
                            <label for="fullname">Full Name</label>
                            <input type="text" id="fullname" placeholder="Enter your full name">
                        </div>

                        <div class="form-group">
                            <label for="language">Preferred Language</label>
                            <select id="language">
                                <option>Sinhala</option>
                                <option>English</option>
                                <option>Spanish</option>
                                <option>French</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" placeholder="mm/dd/yyyy">
                        </div>

                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender">
                                <option>Select</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label>Profile Photo</label>
                            <div class="photo-upload" id="profile-upload">
                                <div class="photo-upload-icon"></div>
                            </div>
                            <div class="file-info1">
                                <strong>Choose File</strong> No file chosen
                            </div>
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
                            <label for="pnumber">Phone Number</label>
                            <input type="text" id="pnumber" placeholder="+94 12-3456789">
                        </div>

                        <div class="form-group">
                            <label for="spnumber">Secondary Phone (Optional)</label>
                            <input type="text" id="spnumber" placeholder="+94 12-3456789">
                        </div>

                        <div class="form-group full-width">
                            <label for="address">Complete Address</label>
                            <textarea id="address" rows="4" placeholder="Enter your full address including city, state and postal code"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" placeholder="youremail@gmail.com">
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
                    
                    <!-- This grid is replaced dynamically by script.js based on selection -->
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
                    </div>
                    
                    <div class="form-actions">
                        <button class="previous-button" onclick="navigateTo(2)">Previous</button>
                        <button class="next-button" onclick="submitForm()">Create Account</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo URL_ROOT; ?>/js/signup/script.js"></script>
</body>
</html>
