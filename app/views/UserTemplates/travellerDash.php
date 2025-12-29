<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARS40V0wUMA2Y3wKorMNNof1eD6wixViE&loading=async" defer></script>


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

            /* Modern additions */
            --sidebar-bg: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            --sidebar-shadow: 4px 0 20px rgba(0, 106, 113, 0.08);
            --menu-item-shadow: 0 8px 25px rgba(0, 106, 113, 0.3);
            --glass-bg: rgba(255, 255, 255, 0.95);
            --glass-border: rgba(0, 106, 113, 0.1);
            --text-muted: #64748b;
            --text-accent: #94a3b8;
        }
        body {
            background-color: var(--secondary);
            display: flex;
            min-height: 100vh;
            color: var(--primary);
        }
        /* Sidebar Styles - Modern Design */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            color: var(--text-color);
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            z-index: 1000;
            border-right: 1px solid #e9ecef;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            overflow-y: auto;
            overflow-x: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 2px;
        }

        .sidebar-header {
            display: flex;
            justify-content: center;
            position: relative;
            z-index: 1;
            padding: 0 20px;
            margin-bottom: 30px;
        }

        .sidebar-logo {
            width: 100px;
            height: 55px;
            display: flex;
            object-fit: contain;
            margin-bottom: 15px;
            filter: drop-shadow(0 2px 8px rgba(0, 106, 113, 0.15));
            transition: transform 0.3s ease;
        }

        .sidebar-logo:hover {
            transform: scale(1.05);
        }

        .sidebar-menu {
            margin-top: 20px;
            list-style: none;
            padding: 0 15px;
            position: relative;
            z-index: 1;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
            position: relative;
        }

        .sidebar-menu a {
            color: black;
            text-decoration: none;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 4px 0;
            position: relative;
            font-weight: 500;
            letter-spacing: 0.025em;
            background: white;
            border: 1px solid #e9ecef;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--primary);
            color: white;
            transform: translateX(8px);
            box-shadow: var(--menu-item-shadow);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .sidebar-menu a:hover i,
        .sidebar-menu a.active i {
            transform: scale(1.1) rotate(5deg);
            color: white;
        }

        .sidebar-menu a i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            transition: all 0.3s ease;
            color: black;
        }

        .sidebar-menu a:hover i,
        .sidebar-menu a.active i {
            color: white;
        }

        .sidebar-menu a span {
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        /* Modern indicator for active state */
        .sidebar-menu a.active::after {
            content: '';
            position: absolute;
            right: -8px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: white;
            border-radius: 2px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
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
            border-bottom: 1px solid #e9ecef;
            z-index: 999;
        }
        .header h1 {
            color: var(--primary);
            font-size: 1.4rem;
            font-weight: 300;
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

        /* Responsive - Modern Mobile Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                background: white;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
            }

            .sidebar-menu a span {
                display: none;
                opacity: 0;
                transform: translateX(-10px);
                transition: all 0.3s ease;
            }

            .sidebar-menu a {
                justify-content: center;
                padding: 16px 12px;
                margin: 6px 8px;
                position: relative;
                background: white;
                border: 1px solid #e9ecef;
            }

            .sidebar-menu a:hover {
                width: calc(100% - 16px);
                justify-content: flex-start;
                padding-left: 20px;
                background: var(--primary);
                border-color: var(--primary);
                box-shadow: var(--menu-item-shadow);
            }

            .sidebar-menu a:hover span {
                display: inline;
                opacity: 1;
                transform: translateX(0);
            }

            .sidebar-menu a.active::after {
                display: none;
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
            <li><a href="<?php echo URL_ROOT.'/RegUser/home'?>" class="active" data-tab="home"><i class="fa-solid fa-house"></i> <span>Home</span></a></li>
            <li><a href="<?php echo URL_ROOT.'/RegUser/destinations'?>" data-tab="destinations"><i class="fa-solid fa-location-dot"></i> <span>Destinations</span></a></li>
            <li><a href="<?php echo URL_ROOT.'/RegUser/drivers'?>" data-tab="drivers"><i class="fa-solid fa-car"></i> <span>Drivers</span></a></li>
            <li><a href="<?php echo URL_ROOT.'/RegUser/guides'?>" data-tab="guides"><i class="fa-solid fa-compass"></i> <span>Guides</span></a></li>
            <li><a href="<?php echo URL_ROOT.'/RegUser/packages'?>" data-tab="packages"><i class="fa-solid fa-box-open"></i> <span>Packages</span></a></li>
            <li><a href="<?php echo URL_ROOT.'/RegUser/trips'?>" data-tab="trips"><i class="fa-solid fa-suitcase-rolling"></i> <span>Trips</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            
            <h1>Hello <?php echo getLoggedInUser()['fullname'].' Welcome Back!' ?></h1>
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
        <div class="dashboard-content active" id="dashboard">lalinda</div>

    <script>

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
        
        let encodedData;

        document.addEventListener('DOMContentLoaded', function() {
            
            encodedData = <?php echo json_encode($loadingContent)?>;
            const tabId = <?php echo json_encode($tabId)?>;

            updateUI();
            setActiveTab(tabId);
            loadTabContent(tabId);

            console.log('DOM fully loaded and parsed');
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
            console.log(activeTabId);
        }

        // function to load tab content
        async function loadTabContent(tabId) {

            const tabElement = document.getElementById('dashboard'); //<---injecting to this div element
            
            // Show loading state
            tabElement.innerHTML = `
                <div style="text-align: center; padding: 40px; color: var(--primary);">
                    <i class="fas fa-spinner fa-spin fa-2x" style="margin-bottom: 15px;"></i>
                    <p>Loading ${tabId} content...</p>
                </div>
            `;

            try {

                const data = encodedData;

                cleanupPreviousAssets(tabId);
                
                // Inject HTML
                tabElement.innerHTML =  data.html;
                
                if(data.css){
                    appendCSS(data.css, tabId)
                }

                if(data.js){
                    appendJS(data.js,tabId);
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

        function cleanupPreviousAssets(currentTabId) {
            
            // Remove previous tab CSS
            const existingAssets = document.querySelectorAll('link[data-tab], style[data-tab],script[data-tab]');

            if(currentTabId.startsWith("subtab")){
                existingAssets.forEach(asset => {
                    if (asset.dataset.tab.startsWith("subtab")) {
                        asset.remove();
                        console.log("cleaning ", asset);
                    }
                });
            } else {
                existingAssets.forEach(asset => {
                    asset.remove();
                });
            }
        }

        function appendCSS(url, tabId) {

            // Check if CSS for this tab already exists
            const existingLink = document.querySelector(`link[data-tab="${tabId}"]`);
            console.log('this exists ',existingLink);

            console.log("adding id " + tabId);
            
            if (existingLink) {
                existingScript.remove();
                console.log("existing css ",existingLink);
            } 

            // Create new link element
            const linkElement = document.createElement('link');
            linkElement.rel = 'stylesheet';
            linkElement.type = 'text/css';
            linkElement.href = url + (url.includes('?') ? '&' : '?') + 'v=' + Date.now();
            linkElement.setAttribute('data-tab', tabId);
            document.head.appendChild(linkElement);
            
        }  

        function appendJS(url, tabId){
            
            // Remove existing script for this tab
            const existingScript = document.querySelector(`script[data-tab="${tabId}"]`);

            if (existingScript) {
                console.log(`Removing existing script for tab ${tabId}`);
                existingScript.remove();
            }
                
            // Create new script element with cache busting
            const script = document.createElement('script');
            script.src = url + '?t=' + Date.now(); // Cache busting
            script.setAttribute('data-tab', tabId);
            
            script.onload = function() {
                console.log(`Successfully loaded and executed script for ${tabId}`);
            };
            
            script.onerror = function() {
                console.error(`Failed to load script for ${tabId}:`, url);
            };
            
            console.log("adding ", script);
            document.head.appendChild(script);
            console.log('append js is working');
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
            window.location.href = '<?php echo URL_ROOT.'/User/login'?>';
        });

        //Register button
        signupBtn.addEventListener('click', function() {
           window.location.href = '<?php echo URL_ROOT.'/User/register'?>';
        });

        // Logout
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('🔒 Are you sure you want to log out?')) {
                window.location.href = '<?php echo URL_ROOT.'/User/logout'?>';
            }
        });

        profileSettingsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('⚙️ Opening Profile Settings...');
        });


        //To update the username and profile displaying
        function updateUI() {

            const userNameValue = '<?php echo getLoggedInUser()['fullname']?>';
            const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'?>;

            if (isLoggedIn) {
                authContainer.style.display = 'none';
                userContainer.style.display = 'flex';

                const initial = userNameValue.charAt(0).toUpperCase();
                userAvatar.textContent = initial;
                userName.textContent = userNameValue;
            } else {
                authContainer.style.display = 'flex';
                userContainer.style.display = 'none';
                closeDropdown();
            }
        }


    </script>
</body>
</html>

