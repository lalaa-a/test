<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARS40V0wUMA2Y3wKorMNNof1eD6wixViE&loading=async" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


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

        /* Header Actions */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-action-btn {
            position: relative;
            background: none;
            border: none;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            color: #006A71;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-action-btn:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.05);
        }

        .header-action-btn:active {
            transform: scale(0.95);
        }

        .notification-badge,
        .message-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            font-size: 0.65rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .message-badge {
            background: #28a745;
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

        /* Sidebar User Section at Bottom */
        .sidebar-user-section {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding-top: 10px;
            padding-left: 3px;
            padding-right: 3px;
            border-top: 1px solid #e9ecef;
            background: white;
            margin-bottom: 10px;
        }

        .sidebar-auth-buttons {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-auth-btn {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Geologica';
        }

        .sidebar-login-btn {
            background: white;
            color: var(--primary);
            border: 1.5px solid var(--primary);
        }

        .sidebar-login-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 106, 113, 0.2);
        }

        .sidebar-signup-btn {
            background: var(--primary);
            color: white;
        }

        .sidebar-signup-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 106, 113, 0.3);
        }

        .sidebar-user-info {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 10px;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .sidebar-user-info:hover {
            background: var(--primary);
        }

        .sidebar-user-info:hover .sidebar-user-name,
        .sidebar-user-info:hover .sidebar-user-role,
        .sidebar-user-info:hover .sidebar-dropdown-icon {
            color: white;
        }

        .sidebar-user-info:hover .sidebar-user-avatar {
            transform: scale(1.05);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
        }

        .sidebar-user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #008891 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 106, 113, 0.2);
        }

        .sidebar-user-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
            overflow: hidden;
        }

        .sidebar-user-name {
            font-weight: 600;
            color: var(--primary);
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color 0.3s ease;
        }

        .sidebar-user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
            transition: color 0.3s ease;
        }

        .sidebar-dropdown-icon {
            color: var(--primary);
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .sidebar-user-info[aria-expanded="true"] .sidebar-dropdown-icon {
            transform: rotate(180deg);
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

        /* Sidebar Dropdown Menu */
        .sidebar-dropdown-menu {
            display: none;
            position: absolute;
            bottom: 100%;
            left: 0;
            right: 0;
            margin-bottom: 8px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 -4px 20px rgba(0, 106, 113, 0.15);
            border: 1px solid #e9ecef;
            overflow: hidden;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .sidebar-dropdown-menu.show {
            display: block;
        }

        .sidebar-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            color: var(--text-color);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .sidebar-dropdown-item:hover {
            background: var(--primary);
            color: white;
        }

        .sidebar-dropdown-item i {
            width: 16px;
            text-align: center;
        }

        .sidebar-dropdown-divider {
            height: 1px;
            background: #e9ecef;
            margin: 4px 0;
        }

        .sidebar-logout-item {
            color: #dc3545;
        }

        .sidebar-logout-item:hover {
            background: #dc3545;
            color: white;
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

            .sidebar-user-section {
                padding: 10px;
            }

            .sidebar-user-details {
                display: none;
            }

            .sidebar-dropdown-icon {
                display: none;
            }

            .sidebar:hover .sidebar-user-details,
            .sidebar:hover .sidebar-dropdown-icon {
                display: flex;
            }

            .main-content {
                margin-left: 70px;
            }

            .header {
                left: 70px;
                width: calc(100% - 70px);
            }

            .header-actions {
                gap: 10px;
            }

            .header-action-btn {
                padding: 8px;
                font-size: 1rem;
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
            <li><a href="<?php echo URL_ROOT.'/Driver/dashboard'?>" class="active" data-tab="dashboard"><i class="fa-solid fa-gauge-high"></i> <span>Dashboard</span></a></li>
            <li><a href="<?php echo URL_ROOT.'/Driver/tours'?>" data-tab="tours"><i class="fa-solid fa-calendar-days"></i> <span>Schedule</span></a></li>
            <li><a href="<?php echo URL_ROOT.'/Driver/requests'?>" data-tab="requests"><i class="fa-solid fa-code-pull-request"></i> <span>Requests</span></a></li>
            <li><a href="<?php echo URL_ROOT.'/Driver/vehicles'?>" data-tab="vehicles"><i class="fa-solid fa-car"></i></i> <span>Vehicles</span></a></li>
            <li><a href="<?php echo URL_ROOT.'/Driver/earnings'?>" data-tab="earnings"><i class="fa-solid fa-sack-dollar"></i> <span>Earnings</span></a></li>
        </ul>

        <!-- User Info Section -->
        <div class="sidebar-user-section">
            <!-- Pre-login: buttons -->
            <div id="sidebarAuthContainer" class="sidebar-auth-buttons">
                <button class="sidebar-auth-btn sidebar-login-btn" id="sidebarLoginBtn">Login</button>
                <button class="sidebar-auth-btn sidebar-signup-btn" id="sidebarSignupBtn">Sign Up</button>
            </div>

            <!-- Post-login: user info + dropdown -->
            <div id="sidebarUserContainer" class="sidebar-user-info" tabindex="0" style="display: none;" role="button" aria-haspopup="true" aria-expanded="false">
                <div class="sidebar-user-avatar" id="sidebarUserAvatar">A</div>
                <div class="sidebar-user-details">
                    <span class="sidebar-user-name" id="sidebarUserName">Admin</span>
                    <span class="sidebar-user-role">Driver</span>
                </div>
                <i class="fas fa-chevron-up sidebar-dropdown-icon"></i>
                <div class="sidebar-dropdown-menu" id="sidebarUserDropdown">
                    <a href="#" class="sidebar-dropdown-item" id="sidebarProfileSettingsBtn">
                        <i class="fas fa-cog"></i> Profile Settings
                    </a>
                    <a href="<?php echo URL_ROOT.'/Guide/guideProfile'?>" class="sidebar-dropdown-item">
                        <i class="fas fa-user-circle"></i> My Profile
                    </a>
                    <div class="sidebar-dropdown-divider"></div>
                    <a href="#" class="sidebar-dropdown-item sidebar-logout-item" id="sidebarLogoutBtn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Hello <?php $user = getLoggedInUser(); echo isset($user['fullname']) ? $user['fullname'].' Welcome Back!' : 'Welcome!'; ?></h1>
            
            <!-- Header Actions -->
            <div class="header-actions">
                <button class="header-action-btn" id="notificationsBtn" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
                </button>
                <button class="header-action-btn" id="messagesBtn" title="Messages">
                    <i class="fas fa-envelope"></i>
                    <span class="message-badge" id="messageBadge" style="display: none;">0</span>
                </button>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content active" id="dashboard"></div>

    <script>

        let isLoggedIn = false;

        // Sidebar elements
        const sidebarAuthContainer = document.getElementById('sidebarAuthContainer');
        const sidebarUserContainer = document.getElementById('sidebarUserContainer');
        const sidebarUserAvatar = document.getElementById('sidebarUserAvatar');
        const sidebarUserName = document.getElementById('sidebarUserName');
        const sidebarUserDropdown = document.getElementById('sidebarUserDropdown');
        const sidebarLoginBtn = document.getElementById('sidebarLoginBtn');
        const sidebarSignupBtn = document.getElementById('sidebarSignupBtn');
        const sidebarLogoutBtn = document.getElementById('sidebarLogoutBtn');
        const sidebarProfileSettingsBtn = document.getElementById('sidebarProfileSettingsBtn');
        
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

             console.log('Loaded data for tab:', encodedData);


            try {

                const data = encodedData;

                // Skip if no data to load (default dashboard view)
                if (!data || !data.html) {
                    tabElement.innerHTML = `
                        <div class="welcome-section" style="text-align: center; padding-top: 60px;">
                            <h1 style="color: var(--primary); margin-bottom: 10px;">Welcome Back!</h1>
                            <p style="color: #666;">Select an option from the sidebar to get started.</p>
                        </div>
                    `;
                    return;
                }

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
            const isShown = sidebarUserDropdown.classList.contains('show');
            sidebarUserDropdown.classList.toggle('show', !isShown);
            sidebarUserContainer.setAttribute('aria-expanded', !isShown);
        }

        // Close dropdown
        function closeDropdown() {
            sidebarUserDropdown.classList.remove('show');
            sidebarUserContainer.setAttribute('aria-expanded', 'false');
        }

        // Bind click to user container (avatar + name)
        sidebarUserContainer.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleDropdown();
        });

        // Keyboard: Enter/Space to open dropdown
        sidebarUserContainer.addEventListener('keydown', function(e) {
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
        sidebarUserDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        // Login action
        sidebarLoginBtn.addEventListener('click', function() {
            window.location.href = '<?php echo URL_ROOT.'/User/login'?>';
        });

        //Register button
        sidebarSignupBtn.addEventListener('click', function() {
           window.location.href = '<?php echo URL_ROOT.'/User/register'?>';
        });

        // Logout
        sidebarLogoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('🔒 Are you sure you want to log out?')) {
                window.location.href = '<?php echo URL_ROOT.'/User/logout'?>';
            }
        });

        sidebarProfileSettingsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('⚙️ Opening Profile Settings...');
        });



// notification and messages logic with buttons 😅        
        // Notification button
        const notificationsBtn = document.getElementById('notificationsBtn');
        const notificationBadge = document.getElementById('notificationBadge');

        notificationsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('🔔 Opening Notifications...');
            // Reset badge count when opened
            notificationBadge.textContent = '0';
            notificationBadge.style.display = 'none';
        });

        // Messages button
        const messagesBtn = document.getElementById('messagesBtn');
        const messageBadge = document.getElementById('messageBadge');

        messagesBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('💬 Opening Messages...');
            // Reset badge count when opened
            messageBadge.textContent = '0';
            messageBadge.style.display = 'none';
        });

        // Simulate receiving notifications (for demo purposes)
        function simulateNotifications() {
            setTimeout(() => {
                notificationBadge.textContent = '3';
                notificationBadge.style.display = 'flex';
            }, 3000);

            setTimeout(() => {
                messageBadge.textContent = '2';
                messageBadge.style.display = 'flex';
            }, 5000);
        }

        // Start simulation if user is logged in
        if (<?php echo isLoggedIn() ? 'true' : 'false'?>) {
            simulateNotifications();
        }


        

        //To update the username and profile displaying
        function updateUI() {

            const userNameValue = '<?php $u = getLoggedInUser(); echo isset($u["fullname"]) ? $u["fullname"] : "Guest"; ?>';
            const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'?>;

            if (isLoggedIn) {
                sidebarAuthContainer.style.display = 'none';
                sidebarUserContainer.style.display = 'flex';

                const initial = userNameValue.charAt(0).toUpperCase();
                sidebarUserAvatar.textContent = initial;
                sidebarUserName.textContent = userNameValue;
            } else {
                sidebarAuthContainer.style.display = 'flex';
                sidebarUserContainer.style.display = 'none';
                closeDropdown();
            }
        }


        // Global Notification System
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 10001;
                animation: slideInRight 0.3s ease;
                max-width: 400px;
            `;

            // Set colors based on type
            const colors = {
                success: '#28a745',
                error: '#dc3545',
                warning: '#ffc107',
                info: '#17a2b8'
            };

            notification.style.background = colors[type] || colors.info;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                ${message}
            `;

            document.body.appendChild(notification);

            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }

        // Make it globally available
        window.showNotification = showNotification;


    </script>

    <style>
        /* Notification Animations */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>

    <!-- Help Widget CSS -->
    <link rel="stylesheet" href="<?php echo URL_ROOT.'/public/css/helper/helpWidget.css'?>">

    <!-- Help Widget Container -->
    <div class="help-widget-container">
        <div class="help-options-popup" id="helpOptionsPopup">
            <div class="help-popup-header">
                <h4><i class="fas fa-hands-helping"></i> How can we help?</h4>
            </div>
            <div class="help-option-item" id="openChatBtn">
                <div class="help-option-icon chat-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="help-option-text">
                    <h5>Chat with Us</h5>
                    <p>Chat with our support team</p>
                </div>
            </div>
            <a href="<?php echo URL_ROOT.'/guide/help'?>" class="help-option-item">
                <div class="help-option-icon center-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="help-option-text">
                    <h5>Help Center</h5>
                    <p>Browse FAQs & guides</p>
                </div>
            </a>
        </div>
        <button class="floating-help-btn" id="helpBtn" title="Need Help?">
            <img src="<?php echo IMG_ROOT.'/help/support.png'?>" alt="Help">
        </button>
    </div>

    <!-- Chat Widget -->
    <div class="chat-widget" id="chatWidget">
        <div class="chat-header">
            <div class="chat-header-info">
                <div class="agent-avatar">
                    <i class="fas fa-headset"></i>
                    <span class="status-dot"></span>
                </div>
                <div class="agent-details">
                    <h3>Travel Support</h3>
                    <p>Online | Typically replies in minutes</p>
                </div>
            </div>
            <div class="chat-header-actions">
                <i class="fas fa-times" id="closeChatBtn"></i>
            </div>
        </div>
        <div class="chat-body">
            <div class="chat-messages" id="chatMessages">
                <div class="date-divider"><span>Today</span></div>
                <div class="message support-message">
                    <div class="message-content">
                        <p>Hello there! 👋 Welcome to Tripingoo Travel Support. How can we help you today?</p>
                    </div>
                    <span class="message-time">Just now</span>
                </div>
            </div>
            <div class="chat-input-area">
                <div class="input-wrapper">
                    <input type="text" id="chatInput" placeholder="Type your message...">
                    <button class="send-btn" id="chatSendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Widget JS -->
    <script src="<?php echo URL_ROOT.'/public/js/helper/helpWidget.js'?>"></script>
</body>
</html>
