<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Travel Application</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Geologica:wght@100..900&family=Outfit&display=swap">
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
            background-color: #f8f9fa;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .left-section {
            flex: 1.5;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url(<?php echo IMG_ROOT.'/homepage/planning-trekking-trip_53876-51125.jpg'?>);
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

        .location-icon {
            display: inline-block;
            width: 80px;
            height: 80px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>') no-repeat center center;
            background-size: contain;
            vertical-align: middle;
            margin-right: 15px;
        }

        .left-section h1 {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 30px;
            font-family: 'Geologica', sans-serif;
        }

        .left-section p {
            font-size: 1.2rem;
            line-height: 1.6;
            max-width: 400px;
            opacity: 0.9;
            font-family: 'Geologica', sans-serif;
        }

        .right-section {
            flex: 1.5;
            background: #ffffff;
            padding: 40px 60px;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            max-width: 450px;
            width: 100%;
            margin: 0 auto;
            padding: 30px;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 8px;
            font-family: 'Geologica', sans-serif;
        }

        .header p {
            color: #6b7280;
            font-size: 0.95rem;
            font-family: 'Geologica', sans-serif;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
        }

        .form-group label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 6px;
            font-family: 'Geologica', sans-serif;
        }

        .form-group input {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.9rem;
            background: white;
            transition: border-color 0.2s;
            font-family: 'Geologica', sans-serif;
        }

        .form-group input:focus {
            outline: none;
            border-color: #35939e;
            box-shadow: 0 0 0 3px rgba(53, 147, 158, 0.1);
        }

        .form-group input::placeholder {
            color: #9ca3af;
            font-family: 'Geologica', sans-serif;
        }

        /* valid input state */
        .form-group input.valid {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16,185,129,0.06);
        }

        .password-container {
            position: relative;
            width: 100%;
        }

        .password-container input {
            width: 100%;
            padding-right: 45px; /* Make room for the eye icon */
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
            font-size: 1.2rem;
            user-select: none;
            transition: color 0.2s;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            color: #35939e;
        }

        /* Closed (hidden) eye icon by default */
        .toggle-password::before {
            content: '';
            width: 20px;
            height: 20px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24'/%3E%3Cline x1='1' y1='1' x2='23' y2='23'/%3E%3C/svg%3E");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            display: block;
        }

        /* Open (visible) eye when the .visible class is present */
        .toggle-password.visible::before {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z'/%3E%3Ccircle cx='12' cy='12' r='3'/%3E%3C/svg%3E");
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #6b7280;
            font-size: 0.95rem;
            font-family: 'Geologica', sans-serif;
        }

        .remember-me input {
            width: 18px;
            height: 18px;
        }

        .forgot-password {
            color: #35939e;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: 'Geologica', sans-serif;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-button {
            background: #006a71;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
            margin-top: 10px;
            font-family: 'Geologica', sans-serif;
        }

        .login-button:hover {
            background: #005a61;
            transform: translateY(-1px);
        }

        .signup-link {
            text-align: center;
            margin-top: 16px;
            color: #6b7280;
            font-size: 0.9rem;
            font-family: 'Geologica', sans-serif;
        }

        .signup-link a {
            color: #35939e;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.85rem;
            margin-top: 4px;
            display: none;
            font-family: 'Geologica', sans-serif;
        }

        .form-group input.error {
            border-color: #ef4444;
        }

        .success-message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: none;
            text-align: center;
            font-size: 1.1rem;
            font-family: 'Geologica', sans-serif;
        }

        .error-message-global {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: none;
            text-align: center;
            font-size: 1.1rem;
            font-family: 'Geologica', sans-serif;
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .left-section {
                min-height: 40vh;
                padding: 20px;
            }

            .left-section h1 {
                font-size: 2.5rem;
            }

            .right-section {
                padding: 40px 20px;
            }

            .form-container {
                padding: 40px 30px;
                max-width: 450px;
            }

            .header h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="location-icon"></div>
            <h1>Welcome Back!</h1>
            <p>Continue your journey with us and explore amazing destinations or share your local expertise.</p>
        </div>

        <div class="right-section">
            <div class="form-container">
                <div class="success-message" id="success-message"></div>
                <div class="error-message-global" id="error-message-global"></div>
                
                <div class="header">
                    <h2>Sign In to Your Account</h2>
                    <p>Enter your credentials to access your account</p>
                </div>

                <form id="login-form">
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" placeholder="youremail@gmail.com" required>
                        <div class="error-message" id="email-error">Valid email is required</div>
                        <div class="success-message" id="email-success" style="display:none; background:transparent; color:#065f46; padding:0; font-size:0.9rem; margin:0;">Email looks good</div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                            <span class="toggle-password" id="togglePassword"></span>
                        </div>
                        <div class="error-message" id="password-error">Password is required</div>
                    </div>

                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>

                    <button type="submit" class="login-button" id="login-button">
                        <span id="button-text">Sign In</span>
                    </button>
                </form>

                <div class="signup-link">
                    Don't have an account? <a href="<?php echo URL_ROOT; ?>/user/register">Create Account</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const isHidden = passwordInput.getAttribute('type') === 'password';
            // Toggle input type
            passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
            // Add/remove visible class to reflect open-eye (visible) state
            this.classList.toggle('visible', isHidden);
        });

        // Clear error messages
        function clearError(fieldId) {
            const errorElement = document.getElementById(`${fieldId}-error`);
            const fieldElement = document.getElementById(fieldId);
            
            if (errorElement) {
                errorElement.style.display = 'none';
            }
            
            if (fieldElement) {
                fieldElement.classList.remove('error');
            }
        }

        // Show error message
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

        // Validate form
        function validateForm() {
            let isValid = true;
            
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const password = document.getElementById('password').value;
            
            if (!email) {
                showError('email', 'Email is required');
                isValid = false;
            } else if (!emailRegex.test(email)) {
                showError('email', 'Valid email is required');
                isValid = false;
            }
            
            if (!password) {
                showError('password', 'Password is required');
                isValid = false;
            } else if (password.length < 8) {
                showError('password', 'Password must be at least 8 characters long');
                isValid = false;
            }
            
            return isValid;
        }

        // Show loading state
        function showLoading() {
            const loginButton = document.getElementById('login-button');
            const buttonText = document.getElementById('button-text');
            loginButton.disabled = true;
            buttonText.innerHTML = '<span class="spinner"></span>Signing In...';
        }

        // Hide loading state
        function hideLoading() {
            const loginButton = document.getElementById('login-button');
            const buttonText = document.getElementById('button-text');
            loginButton.disabled = false;
            buttonText.textContent = 'Sign In';
        }

        // Handle form submission
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                return;
            }
            
            // Get form data
            const formData = new FormData();
            formData.append('email', document.getElementById('email').value.trim());
            formData.append('password', document.getElementById('password').value);
            formData.append('remember', document.getElementById('remember').checked ? 'on' : 'off');
            
            // Show loading state
            showLoading();
            
            // Send to PHP backend
            fetch('<?php echo URL_ROOT; ?>/user/login', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const successDiv = document.getElementById('success-message');
                    successDiv.textContent = data.message;
                    successDiv.style.display = 'block';
                    document.getElementById('error-message-global').style.display = 'none';
                    
                    // Redirect based on account type after 2 seconds
                    setTimeout(() => {
                        // Use server-provided redirect URL if available, otherwise fallback to hardcoded
                        let redirectUrl = data.redirect_url;
                        
                        if (!redirectUrl) {
                            // Fallback to hardcoded redirects
                            console.log(data.user.account_type);
                            switch(data.user.account_type) {
                                case 'admin':
                                    redirectUrl = '<?php echo URL_ROOT; ?>/dashboard/Admin';
                                    break;
                                case 'site_moderator':
                                    redirectUrl = '<?php echo URL_ROOT; ?>/dashboard/siteModerator';
                                    break;
                                case 'business_manager':
                                    redirectUrl = '<?php echo URL_ROOT; ?>/dashboard/businessManager';
                                    break;
                                case 'driver':
                                    redirectUrl = '<?php echo URL_ROOT; ?>/dashboard/driver';
                                    break;
                                case 'guide':
                                    redirectUrl = '<?php echo URL_ROOT; ?>/dashboard/guide';
                                    break;
                                case 'tourist':
                                    redirectUrl = '<?php echo URL_ROOT; ?>/RegUser/home';
                                    break;
                                default:
                                    redirectUrl = '<?php echo URL_ROOT; ?>/dashboard';
                            }
                        }
                        window.location.href = redirectUrl;
                    }, 2000);
                } else {
                    // Show error message
                    const errorDiv = document.getElementById('error-message-global');
                    errorDiv.textContent = data.message;
                    errorDiv.style.display = 'block';
                    document.getElementById('success-message').style.display = 'none';
                    
                    hideLoading();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorDiv = document.getElementById('error-message-global');
                errorDiv.textContent = 'An error occurred during login. Please try again.';
                errorDiv.style.display = 'block';
                document.getElementById('success-message').style.display = 'none';
                
                hideLoading();
            });
        });

        // Add input event listeners for real-time validation
        const emailInput = document.getElementById('email');
        const loginButton = document.getElementById('login-button');
        const emailSuccess = document.getElementById('email-success');

        function validateEmailField(showFeedback = true) {
            const value = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // reset
            emailInput.classList.remove('valid');
            clearError('email');
            emailSuccess.style.display = 'none';

            if (!value) {
                // empty - keep button disabled
                loginButton.disabled = true;
                return false;
            }

            if (emailRegex.test(value)) {
                emailInput.classList.add('valid');
                if (showFeedback) emailSuccess.style.display = 'block';
                loginButton.disabled = false;
                return true;
            } else {
                // invalid
                if (showFeedback) showError('email', 'Please enter a valid email address');
                loginButton.disabled = true;
                return false;
            }
        }

        emailInput.addEventListener('input', function() {
            validateEmailField(false); // live validate, but don't show error until blur/submission
        });

        emailInput.addEventListener('blur', function() {
            validateEmailField(true); // on blur show feedback/errors
        });
        
        document.getElementById('password').addEventListener('input', function() {
            clearError('password');
        });

        // Handle "Forgot Password" link
        document.querySelector('.forgot-password').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Password reset functionality would be implemented here.');
        });

        // Check if user is already logged in (optional - for better UX)
        // This would require a separate endpoint to check session status
        // For now, we'll rely on server-side redirects
    </script>
</body>
</html>