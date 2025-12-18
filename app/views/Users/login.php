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
        }

        .left-section p {
            font-size: 1.2rem;
            line-height: 1.6;
            max-width: 400px;
            opacity: 0.9;
        }

        .right-section {
            flex: 1.5;
            background: #ffffff;
            padding: 60px 80px;
            overflow-y: auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            max-width: 550px;
            width: 100%;
            margin: 0 auto;
            padding: 50px;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h2 {
            font-size: 2.2rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 12px;
        }

        .header p {
            color: #6b7280;
            font-size: 1.1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 25px;
        }

        .form-group label {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .form-group input {
            padding: 14px 18px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1.1rem;
            background: white;
            transition: border-color 0.2s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #35939e;
            box-shadow: 0 0 0 3px rgba(53, 147, 158, 0.1);
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        /* valid input state */
        .form-group input.valid {
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16,185,129,0.06);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
            font-size: 0.95rem;
        }

        .toggle-password:hover {
            color: #35939e;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #6b7280;
            font-size: 0.95rem;
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
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-button {
            background: #35939e;
            color: white;
            border: none;
            padding: 16px 36px;
            border-radius: 22px;
            font-size: 1.15rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            width: 100%;
            margin-top: 10px;
        }

        .login-button:hover {
            background: #236666;
        }

        .signup-link {
            text-align: center;
            margin-top: 25px;
            color: #6b7280;
            font-size: 0.95rem;
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
            font-size: 0.9rem;
            margin-top: 6px;
            display: none;
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
                        <div class="success-message" id="email-success" style="display:none; background:transparent; color:#065f46; padding:0; font-size:0.9rem; margin-top:6px;">Email looks good</div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password *</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                            <span class="toggle-password" id="togglePassword">Show</span>
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
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Show' : 'Hide';
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
                                    redirectUrl = '<?php echo URL_ROOT; ?>/User/account';
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