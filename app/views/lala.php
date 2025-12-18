<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">

    <link href="<?php echo URL_ROOT; ?>/public/css/trips/usertrip.css" rel="stylesheet">



    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Geologica';
        }
        :root {
            --primary: #006A71;
            --primary-hover: #9ACBD0;
            --secondary: #f5f7fb;
            --text-color: #212529;
            --sidebar-width: 222px;
            --header-height: 70px;
        }
        body {
            background-color: var(--secondary);
            display: flex;
            min-height: 100vh;
            color: var(--primary);
        }
        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            color: var(--text-color);
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            z-index: 1000;
            border-right: 1px solid #e9ecef;
        }
        .sidebar-header {
            display: flex;
            justify-content: center;
        }
        .sidebar-logo {
            width: 90px;
            height: 50px;
            display: flex;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0 10px;
        }
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        .sidebar-menu a {
            color: var(--text-color);
            text-decoration: none;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 5px 5px;
        }
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--primary-hover);
            color: var(--primary);
        }
        .sidebar-menu a i {
            font-size: 1rem;
        }
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: calc(var(--header-height) + 20px) 20px 20px;
        }
        /* Header */
        .header {
            padding: 10px;
            background: white;
            height: var(--header-height);
            width: calc(100% - var(--sidebar-width));
            left: var(--sidebar-width);
            display: flex;
            top: 0;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            margin-bottom: 30px;
            position: fixed;
            z-index: 999;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .header h1 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* Auth Buttons (pre-login) */
        .auth-buttons {
            display: flex;
            gap: 10px;
        }
        .auth-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.95rem;
        }
        .login-btn {
            background: var(--primary);
            color: white;
        }
        .signup-btn {
            background: #6c757d;
            color: white;
        }
        .auth-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* User Dropdown (post-login) — ENHANCED UX */
        .user-info {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 8px;
            transition: background-color 0.25s ease;
        }
        .user-info:hover {
            background-color: #f8f9fa;
        }
        .user-info:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            user-select: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .user-name {
            font-weight: 500;
            color: var(--primary);
            transition: color 0.2s ease;
        }
        .user-info:hover .user-avatar,
        .user-info:focus .user-avatar {
            transform: scale(1.05);
            box-shadow: 0 0 0 3px rgba(0, 106, 113, 0.2);
        }
        .user-info:hover .user-name,
        .user-info:focus .user-name {
            color: var(--primary);
            text-decoration: underline;
        }

        /* Dropdown Menu — Smooth Animation */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            min-width: 200px;
            overflow: hidden;
            z-index: 1001;
            margin-top: 8px;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        .dropdown-menu.show {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        .dropdown-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary);
            text-decoration: none;
            transition: background 0.2s, padding-left 0.2s;
        }
        .dropdown-item:hover {
            background: #f8f9fa;
            padding-left: 24px;
        }
        .dropdown-item i {
            width: 20px;
            text-align: center;
        }
        .dropdown-divider {
            height: 1px;
            background: #e9ecef;
            margin: 4px 0;
        }

        /* Dashboard Content */
        .dashboard-content {
            display: none;
        }
        .dashboard-content.active {
            display: block;
        }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 25px;
            margin-bottom: 25px;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .card-header h2 {
            color: var(--primary);
            font-size: 1.3rem;
            font-weight: 600;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 25px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
            color: white;
        }
        .stat-icon.trips {
            background: linear-gradient(135deg, #006A71, #005a5f);
        }
        .stat-icon.earnings {
            background: linear-gradient(135deg, #4CAF50, #45a049);
        }
        .stat-icon.users {
            background: linear-gradient(135deg, #2196F3, #1976D2);
        }
        .stat-icon.bookings {
            background: linear-gradient(135deg, #FF9800, #F57C00);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        /* Recent Notifications */
        .notifications-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .notification-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 20px;
            border-left: 4px solid var(--primary);
        }
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .notification-title {
            font-weight: 600;
            color: var(--primary);
        }
        .notification-time {
            font-size: 0.8rem;
            color: #888;
        }
        .notification-content {
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .notification-actions {
            display: flex;
            gap: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-menu a span {
                display: none;
            }
            .sidebar-menu a {
                justify-content: center;
                padding: 15px;
            }
            .main-content {
                margin-left: 70px;
            }
            .header {
                left: 70px;
                width: calc(100% - 70px);
            }
            .stats-grid,
            .notifications-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div>
                <img src="<?php echo IMG_ROOT.'/logo/logo design 1(2).png'?>" alt="Logo" class="sidebar-logo">
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active" data-tab="home"><i class="fa-solid fa-house"></i> <span>Home</span></a></li>
            <li><a href="#" data-tab="destinations"><i class="fa-solid fa-location-dot"></i> <span>Destinations</span></a></li>
            <li><a href="#" data-tab="drivers"><i class="fa-solid fa-car"></i> <span>Drivers</span></a></li>
            <li><a href="#" data-tab="guides"><i class="fa-solid fa-compass"></i> <span>Guides</span></a></li>
            <li><a href="#" data-tab="packages"><i class="fa-solid fa-box-open"></i> <span>Packages</span></a></li>
            <li><a href="#" data-tab="trips"><i class="fa-solid fa-suitcase-rolling"></i> <span>Trips</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            
            <h1>Admin Dashboard</h1>
            <!-- Pre-login: buttons -->
            <div id="authContainer" class="auth-buttons">
                <button class="auth-btn login-btn" id="loginBtn">Login</button>
                <button class="auth-btn signup-btn" id="signupBtn">Sign Up</button>
            </div>

            <!-- Post-login: user info + dropdown -->
            <div id="userContainer" class="user-info" tabindex="0" style="display: none;" role="button" aria-haspopup="true" aria-expanded="false">
                <div class="user-avatar" id="userAvatar">A</div>
                <span class="user-name" id="userName">Admin</span>
                <div class="dropdown-menu" id="userDropdown">
                    <a href="#" class="dropdown-item" id="profileSettingsBtn">
                        <i class="fas fa-cog"></i> Profile Settings
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user-circle"></i> My Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item logout-item" id="logoutBtn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content active" id="dashboard"></div>



    <script>
        // Mock user
        const mockUser = {
            fullname: "Admin User",
            email: "admin@travel.com"
        };

        let isLoggedIn = false;

        const authContainer = document.getElementById('authContainer');
        const userContainer = document.getElementById('userContainer');
        const userAvatar = document.getElementById('userAvatar');
        const userName = document.getElementById('userName');
        const userDropdown = document.getElementById('userDropdown');
        const loginBtn = document.getElementById('loginBtn');
        const signupBtn = document.getElementById('signupBtn');
        const logoutBtn = document.getElementById('logoutBtn');
        const profileSettingsBtn = document.getElementById('profileSettingsBtn');

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded and parsed');
            
            // Check if data exists
            let encodedData = null;
            
            <?php 
            // Debug: Show what variables are available
            error_log('Available variables in common view: ' . print_r(get_defined_vars(), true));
            ?>
            
            try {
                <?php if (isset($unEncodedResponse)): ?>
                    encodedData = <?php echo json_encode($unEncodedResponse); ?>;
                    console.log('Data loaded:', encodedData);
                    
                    updateUI();
                    setActiveTab(encodedData.tabId);
                    loadTabContent(encodedData.tabId);
                <?php elseif (isset($tabId) && isset($loadingContent)): ?>
                    // Try alternative variable names
                    encodedData = {
                        tabId: '<?php echo $tabId; ?>',
                        loadingContent: <?php echo json_encode($loadingContent); ?>
                    };
                    console.log('Data loaded (alternative):', encodedData);
                    
                    updateUI();
                    setActiveTab(encodedData.tabId);
                    loadTabContent(encodedData.tabId);
                <?php else: ?>
                    console.error('No data available - variables not set');
                    document.getElementById('dashboard').innerHTML = '<p>No data available</p>';
                <?php endif; ?>
            } catch (error) {
                console.error('Error initializing:', error);
                document.getElementById('dashboard').innerHTML = '<p>Error loading data</p>';
            }
        });
     

               

        // Function to set active tab
        function setActiveTab(activeTabId) {
            // Remove active class from all tabs
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to clicked tab
            const activeLink = document.querySelector(`[data-tab="${activeTabId}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }

        // AJAX function to load tab content
        async function loadTabContent(tabId) {
            console.log('Loading tab content for:', tabId);
            console.log('Available encodedData:', encodedData);

            const tabElement = document.getElementById('dashboard'); //<---injecting to this div element
            
            if (!tabElement) {
                console.error('Dashboard element not found!');
                return;
            }
            
            // Show loading state
            tabElement.innerHTML = `
                <div style="text-align: center; padding: 40px; color: var(--primary);">
                    <i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom: 15px;"></i>
                    <p>Loading ${tabId} content...</p>
                </div>
            `;

            try {
                if (!encodedData || !encodedData.loadingContent) {
                    throw new Error('No data available for this tab');
                }

                const data = loadingContent;
                console.log('Content data:', data);
                
                cleanupPreviousAssets(tabId);
                
                // Inject HTML
                if (data.html) {
                    tabElement.innerHTML = data.html;
                    console.log('HTML injected successfully');
                } else {
                    console.warn('No HTML content found');
                }
                
                if(data.css){
                    appendCSS(data.css, tabId);
                    console.log('CSS applied successfully');
                }

                if(data.js){
                    executeJS(data.js);
                    console.log('JS executed successfully');
                }
                
            } catch (error) {
                console.error('Error loading tab:', error);
                tabElement.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle fa-2x" style="margin-bottom: 15px;"></i>
                        <h3>Failed to load content</h3>
                        <p>${error.message}</p>
                        <button class="btn btn-primary" onclick="loadTabContent('${tabId}')">
                            Try Again
                        </button>
                    </div>
                `;
            }
        }

        
        // Helper function to clean up previous assets
        function cleanupPreviousAssets(currentTabId) {
            // Remove previous tab CSS
            const existingStyles = document.querySelectorAll('style[data-tab]');
            existingStyles.forEach(style => {
                if (style.dataset.tab !== currentTabId) {
                    style.remove();
                }
            });
        }

        // Helper function to append CSS
        function appendCSS(cssContent, tabId) {
            // Check if CSS for this tab already exists
            let existingStyle = document.querySelector(`style[data-tab="${tabId}"]`);
            
            if (existingStyle) {
                existingStyle.textContent = cssContent;
            } else {
                const styleElement = document.createElement('style');
                styleElement.setAttribute('data-tab', tabId);
                styleElement.textContent = cssContent;
                document.head.appendChild(styleElement);
            }
        }

        // Helper function to safely execute JavaScript
        function executeJS(jsContent) {
            try {
                // Create a new function and execute it
                new Function(jsContent)();
            } catch (error) {
                console.error('Error executing JavaScript:', error);
            }
        }

        // Toggle dropdown
        function toggleDropdown() {
            const isShown = userDropdown.classList.contains('show');
            userDropdown.classList.toggle('show', !isShown);
            userContainer.setAttribute('aria-expanded', !isShown);
        }

        // Close dropdown
        function closeDropdown() {
            userDropdown.classList.remove('show');
            userContainer.setAttribute('aria-expanded', 'false');
        }

        // Bind click to user container (avatar + name)
        userContainer.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleDropdown();
        });

        // Keyboard: Enter/Space to open dropdown
        userContainer.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleDropdown();
            }
        });

        // Close on outside click or Escape
        document.addEventListener('click', closeDropdown);
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDropdown();
        });

        // Prevent closing when clicking inside dropdown
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Login action
        loginBtn.addEventListener('click', function() {
            alert('✅ Logging in as Admin User...');
            login();
        });

        signupBtn.addEventListener('click', function() {
            alert('📝 Opening Sign Up...\n(For demo, logging in as Admin.)');
            login();
        });

        // Logout
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('🔒 Are you sure you want to log out?')) {
                logout();
            }
        });

        profileSettingsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('⚙️ Opening Profile Settings...');
        });

        function login() {
            isLoggedIn = true;
            updateUI();
        }

        function logout() {
            isLoggedIn = false;
            updateUI();
        }

        function updateUI() {
            if (isLoggedIn) {
                authContainer.style.display = 'none';
                userContainer.style.display = 'flex';

                const initial = mockUser.fullname.charAt(0).toUpperCase();
                userAvatar.textContent = initial;
                userName.textContent = mockUser.fullname;
            } else {
                authContainer.style.display = 'flex';
                userContainer.style.display = 'none';
                closeDropdown();
            }
        }
    
    </script>
</body>
</html>

