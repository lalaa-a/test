<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Moderator Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary: #006A71;
            --primary-hover: #9ACBD0;
            --secondary: #f5f7fb;
            --text-color: #212529;
            --sidebar-width: 250px;
            --header-height: 70px;
        }

        body {
            background-color: var(--secondary);
            display: flex;
            min-height: 100vh;
            color: var(--text-color);
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            color: var(--text-color);
            height: 100vh;
            position: fixed;
            padding: 20px 0;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            z-index: 1000;
            border-right: 1px solid #e9ecef;
        }

        .sidebar-logo {
            width: 100px;
            height: 55px;
            object-fit: contain;
            margin-bottom: 10px;
            
        }

        .sidebar-header {
            display: flex;
            justify-content: center;
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
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
            margin: 0 5px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: var(--primary-hover);
            color: var(--primary);
        }

        .sidebar-menu a i {
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
        }

        /* Header */
        .header {
            background: white;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            border-radius: 12px;
        }

        .header h1 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
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
            font-size: 1.2rem;
            overflow: hidden;
            border: 2px solid var(--primary);
        }
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
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

        .btn-success {
            background: #4CAF50;
            color: white;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-warning {
            background: #FF9800;
            color: white;
        }

        .btn-info {
            background: #2196F3;
            color: white;
        }

        .btn-complaint {
            background: #e74c3c;
            color: white;
        }

        .btn-help {
            background: #3498db;
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

        .stat-icon.packages {
            background: linear-gradient(135deg, #006A71, #005a5f);
        }

        .stat-icon.places {
            background: linear-gradient(135deg, #4CAF50, #45a049);
        }

        .stat-icon.help {
            background: linear-gradient(135deg, #2196F3, #1976D2);
        }

        .stat-icon.verifications {
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

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: var(--primary);
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-banned {
            background: #f8d7da;
            color: #721c24;
        }

        .status-replied {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-complaint {
            background: #fadbd8;
            color: #922b21;
        }

        .status-help {
            background: #d6eaf8;
            color: #1a5276;
        }

        .status-replied-by-moderator {
            background: #d5f4e6;
            color: #27ae60;
        }

        .status-replied-by-admin {
            background: #ebf5fb;
            color: #2980b9;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary);
        }

        .form-group input,
        .form-group select,
        .form-group textarea,
        .form-group file {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        /* Search Box */
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-box input {
            flex: 1;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
        }

        /* Chat Styles */
        .chat-container {
            display: flex;
            height: calc(100vh - 200px);
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
        }

        .chat-sidebar {
            width: 300px;
            background: white;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
        }

        .chat-search {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .chat-search input {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .chat-list {
            flex: 1;
            overflow-y: auto;
        }

        .chat-item {
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .chat-item:hover,
        .chat-item.active {
            background-color: #f8f9fa;
        }

        .chat-item.active {
            background-color: var(--primary-hover);
        }

        .chat-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .chat-item-name {
            font-weight: 600;
            color: var(--primary);
        }

        .chat-item-time {
            font-size: 0.75rem;
            color: #888;
        }

        .chat-item-preview {
            font-size: 0.85rem;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            background: white;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .chat-header-info h3 {
            color: var(--primary);
            margin-bottom: 2px;
        }

        .chat-header-info p {
            font-size: 0.85rem;
            color: #666;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            line-height: 1.4;
            position: relative;
            word-wrap: break-word;
        }

        .message.user {
            background: var(--primary);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .message.admin {
            background: white;
            color: var(--primary);
            border: 1px solid #e9ecef;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }

        .message.moderator {
            background: #e3f2fd;
            color: #1976d2;
            align-self: flex-start;
            border: 1px solid #bbdefb;
            border-bottom-left-radius: 4px;
        }

        .message.other-moderator {
            background: #f3e5f5;
            color: #7b1fa2;
            align-self: flex-start;
            border: 1px solid #e1bee7;
            border-bottom-left-radius: 4px;
        }

        .message-time {
            font-size: 0.7rem;
            opacity: 0.8;
            margin-top: 5px;
            text-align: right;
        }

        .chat-input {
            padding: 15px;
            background: white;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 10px;
        }

        .chat-input input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 0.95rem;
        }

        .chat-input button {
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            background: var(--primary);
            color: white;
            cursor: pointer;
            font-weight: 500;
        }

        .chat-input button:hover {
            background: #005a5f;
        }

        /* Place Management */
        .place-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .place-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.2s ease;
        }

        .place-card:hover {
            transform: translateY(-5px);
        }

        .place-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(45deg, #006A71, #005a5f);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .place-content {
            padding: 20px;
        }

        .place-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .place-location {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .place-description {
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .place-actions {
            display: flex;
            gap: 10px;
        }

        /* Package Management */
        .package-form {
            margin-top: 20px;
        }

        .package-places {
            margin-top: 20px;
            border-top: 2px solid #f0f0f0;
            padding-top: 20px;
        }

        .place-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            margin-bottom: 10px;
            border-radius: 6px;
        }

        /* Map Placeholder */
        .map-placeholder {
            width: 100%;
            height: 300px;
            background: #f8f9fa;
            border: 2px dashed #e9ecef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
            margin-top: 15px;
            font-size: 1.1rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 80%;
            max-width: 800px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .modal-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: var(--primary);
        }

        .profile-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .detail-group {
            margin-bottom: 15px;
        }

        .detail-group h4 {
            color: var(--primary);
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .detail-group p {
            color: #666;
            line-height: 1.5;
        }

        .documents-section {
            margin-top: 20px;
        }

        .documents-section h3 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .document-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .document-item {
            text-align: center;
        }

        .document-preview {
            width: 100%;
            height: 120px;
            background: #f8f9fa;
            border: 2px dashed #e9ecef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            overflow: hidden;
        }

        .document-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .document-preview i {
            color: #888;
            font-size: 2rem;
        }

        .document-label {
            font-size: 0.85rem;
            color: #666;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }

        /* Help Portal Tabs */
        .help-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .help-tab {
            padding: 10px 20px;
            background: #f8f9fa;
            border: none;
            border-radius: 6px 6px 0 0;
            cursor: pointer;
            font-weight: 500;
            color: #666;
            transition: all 0.3s ease;
        }

        .help-tab.active {
            background: white;
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
        }

        .help-content {
            display: none;
        }

        .help-content.active {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .sidebar-header {
                padding: 15px 10px;
            }

            .sidebar-logo {
                width: 35px;
                height: 35px;
            }
            
            .sidebar-menu a span {
                display: none;
            }
            .sidebar-menu a {
                justify-content: center;
                padding: 15px;
            }
            .sidebar-menu a i {
                font-size: 1.3rem;
            }
            .main-content {
                margin-left: 70px;
            }
            .stats-grid,
            .place-grid {
                grid-template-columns: 1fr;
            }
            .chat-sidebar {
                width: 250px;
            }
            .message {
                max-width: 85%;
            }
            .modal-content {
                width: 95%;
                margin: 10% auto;
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
            <li><a href="#" class="active" data-tab="dashboard"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="#" data-tab="verification"><i class="fas fa-user-check"></i> <span>Verification</span></a></li>
            <li><a href="#" data-tab="packages"><i class="fas fa-box"></i> <span>Travel Packages</span></a></li>
            <li><a href="#" data-tab="places"><i class="fas fa-map-marker-alt"></i> <span>Places</span></a></li>
            <li><a href="#" data-tab="help-portal"><i class="fas fa-headset"></i> <span>Help Portal</span></a></li>
            <li><a href="#" data-tab="internal-chat"><i class="fas fa-comments"></i> <span>Internal Chat</span></a></li>
            <li><a href="#" data-tab="profile"><i class="fas fa-user-cog"></i> <span>Profile Settings</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Moderator Dashboard</h1>
            <div class="user-info">
                <?php
                $user = getLoggedInUser();
                $firstInitial = !empty($user['fullname']) ? strtoupper(substr($user['fullname'], 0, 1)) : 'M';
                $profilePhoto = $user['profile_photo'] ?? null;
                ?>
                <div class="user-avatar">
                    <?php if (!empty($profilePhoto) && file_exists(ROOT_PATH.'/public/'.$user['profile_photo'])): ?>
                        <img src="<?=URL_ROOT.'/public/'.$user['profile_photo']?>" alt="Profile Photo">
                    <?php else: ?>
                        <?= $firstInitial ?>
                    <?php endif; ?>
                </div>
                <span><?= htmlspecialchars($user['fullname'] ?? 'Site Moderator') ?></span>
                <button class="logout-btn" onclick="window.location.href='<?php echo URL_ROOT; ?>/user/logout'">Logout</button>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content active" id="dashboard">
            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon packages">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-number">24</div>
                    <div class="stat-label">Travel Packages</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon places">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="stat-number">156</div>
                    <div class="stat-label">Places Added</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon help">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="stat-number">47</div>
                    <div class="stat-label">Help Requests</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon verifications">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-number">18</div>
                    <div class="stat-label">Pending Verifications</div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h2>Recent Activity</h2>
                </div>
                <div style="display: grid; gap: 15px;">
                    <div style="padding: 15px; border-left: 4px solid var(--primary); background: #f8f9fa; border-radius: 0 8px 8px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <strong>New Place Added</strong>
                            <small>2 hours ago</small>
                        </div>
                        <p>Added " galle fort" to the places database with photos and location details.</p>
                    </div>
                    <div style="padding: 15px; border-left: 4px solid #4CAF50; background: #f8f9fa; border-radius: 0 8px 8px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <strong>Package Created</strong>
                            <small>5 hours ago</small>
                        </div>
                        <p>Created new travel package " hiking adventure" with 5 destinations.</p>
                    </div>
                    <div style="padding: 15px; border-left: 4px solid #2196F3; background: #f8f9fa; border-radius: 0 8px 8px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <strong>Profile Verified</strong>
                            <small>1 day ago</small>
                        </div>
                        <p>Verified guide profile for ridama with all required documents.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verification -->
        <div class="dashboard-content" id="verification">
            <div class="card">
                <div class="card-header">
                    <h2>Verification</h2>
                </div>
                <div style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;">
                    <button class="verification-tab active" style="padding: 10px 20px; background: white; border: none; border-radius: 6px 6px 0 0; cursor: pointer; font-weight: 500; color: var(--primary); border-bottom: 2px solid var(--primary);" data-tab="profile">Profile Verification</button>
                    <button class="verification-tab" style="padding: 10px 20px; background: #f8f9fa; border: none; border-radius: 6px 6px 0 0; cursor: pointer; font-weight: 500; color: #666;" data-tab="vehicle">Vehicle Verification</button>
                </div>
                
                <!-- Profile Verification Content -->
                <div class="verification-content active" id="profile-verification">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Documents</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ridam</td>
                                <td>ridma@gmail.com</td>
                                <td>Guide</td>
                                <td>License, ID, Certificate</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="openProfileModal('ridma')">View Details</button>
                                </td>
                            </tr>
                            <tr>
                                <td>chiran</td>
                                <td>chiran@gmail.com</td>
                                <td>Driver</td>
                                <td>License, ID, Certificate</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="openProfileModal('chiran')">View Details</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Vehicle Verification Content -->
                <div class="verification-content" id="vehicle-verification" style="display: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Driver Name</th>
                                <th>Vehicle Make/Model</th>
                                <th>License Plate</th>
                                <th>Documents</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>chiran</td>
                                <td>Toyota Camry 2023</td>
                                <td>ABC-123</td>
                                <td>Registration, Insurance</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="openVehicleModal('chiran')">View Details</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Travel Packages -->
        <div class="dashboard-content" id="packages">
            <div class="card">
                <div class="card-header">
                    <h2>Create Travel Package</h2>
                </div>
                <form class="package-form">
                    <div class="form-group">
                        <label for="packageName">Package Name</label>
                        <input type="text" id="packageName" placeholder="Enter package name">
                    </div>
                    <div class="form-group">
                        <label for="packageDescription">Description</label>
                        <textarea id="packageDescription" rows="4" placeholder="Enter package description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="packageDuration">Duration (Days)</label>
                        <input type="number" id="packageDuration" placeholder="Enter number of days">
                    </div>
                    <div class="form-group">
                        <label for="packagePrice">Price ($)</label>
                        <input type="number" id="packagePrice" placeholder="Enter package price">
                    </div>
                    <div class="form-group">
                        <label for="packageImage">Package Image</label>
                        <input type="file" id="packageImage" accept="image/*">
                    </div>
                    
                    <div class="package-places">
                        <h3>Package Destinations</h3>
                        <div class="search-box">
                            <input type="text" id="placeSearch" placeholder="Search and add places to package">
                            <button class="btn btn-primary" type="button"><i class="fas fa-plus"></i> Add Place</button>
                        </div>
                        <div id="selectedPlaces">
                            <div class="place-item">
                                <span>galle fort</span>
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </div>
                            <div class="place-item">
                                <span>mirissa </span>
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Create Package</button>
                        <button type="reset" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Places Management -->
        <div class="dashboard-content" id="places">
            <div class="card">
                <div class="card-header">
                    <h2>Manage Places</h2>
                    <button class="btn btn-primary" onclick="showAddPlaceForm()"><i class="fas fa-plus"></i> Add New Place</button>
                </div>
                
                <!-- Add Place Form (Initially Hidden) -->
                <div class="card" id="addPlaceForm" style="display: none; margin-top: 20px;">
                    <div class="card-header">
                        <h2>Add New Place</h2>
                    </div>
                    <form onsubmit="return addPlace(event)">
                        <div class="form-group">
                            <label for="placeName">Place Name</label>
                            <input type="text" id="placeName" placeholder="Enter place name" required>
                        </div>
                        <div class="form-group">
                            <label for="placeLocation">Location</label>
                            <input type="text" id="placeLocation" placeholder="Enter city, country" required>
                        </div>
                        <div class="form-group">
                            <label for="placeDescription">Description</label>
                            <textarea id="placeDescription" rows="4" placeholder="Enter detailed description of the place" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="placeImage">Place Image</label>
                            <input type="file" id="placeImage" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label>Location on Map</label>
                            <div class="map-placeholder">
                                <i class="fas fa-map-marked-alt"></i> Map Integration (Google Maps API)
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Add Place</button>
                            <button type="button" class="btn btn-secondary" onclick="hideAddPlaceForm()">Cancel</button>
                        </div>
                    </form>
                </div>
                
                <!-- Places Grid -->
                <div class="place-grid">
                    <div class="place-card">
                        <div class="place-image">
                            galle fort
                        </div>
                        <div class="place-content">
                            <h3 class="place-title">galle fort</h3>
                            <p class="place-location">galle</p>
                            <p class="place-description">ancient rock fortress</p>
                            <div class="place-actions">
                                <button class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Edit</button>
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                            </div>
                        </div>
                    </div>
                    <div class="place-card">
                        <div class="place-image" style="background: linear-gradient(45deg, #4CAF50, #45a049);">
                            mirissa
                        </div>
                        <div class="place-content">
                            <h3 class="place-title">mirissa</h3>
                            <p class="place-location">matara</p>
                            <p class="place-description">whale watching.</p>
                            <div class="place-actions">
                                <button class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Edit</button>
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                            </div>
                        </div>
                    </div>
                    <div class="place-card">
                        <div class="place-image" style="background: linear-gradient(45deg, #2196F3, #1976D2);">
                            kandy
                        </div>
                        <div class="place-content">
                            <h3 class="place-title">kandy</h3>
                            <p class="place-location">kandy city</p>
                            <p class="place-description">historical kandy city.</p>
                            <div class="place-actions">
                                <button class="btn btn-info btn-sm"><i class="fas fa-edit"></i> Edit</button>
                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Portal - Updated to preserve content when switching tabs -->
        <div class="dashboard-content" id="help-portal">
            <div class="card">
                <div class="card-header">
                    <h2>Help Portal Messages</h2>
                </div>
                
                <!-- Help Portal Tabs -->
                <div class="help-tabs">
                    <button class="help-tab active" data-help-type="help">Help Messages</button>
                    <button class="help-tab" data-help-type="complaints">Complaints</button>
                </div>
                
                <!-- Help Messages Content -->
                <div class="help-content active" id="help-messages-content">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                        <thead>
                            <tr>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">User</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">Message</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">Date</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">Status</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">Replied By</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 15px;">ransara</td>
                                <td style="padding: 15px; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Payment issue with itinerary ITN-001 - payment went through but no confirmation received.</td>
                                <td style="padding: 15px;">2 hours ago</td>
                                <td style="padding: 15px;">
                                    <span class="status-badge status-replied-by-moderator">Replied</span>
                                </td>
                                <td style="padding: 15px;">sewmini (Support)</td>
                                <td style="padding: 15px;">
                                    <button class="btn btn-info btn-sm" onclick="openHelpChat('john', 'help')">View Chat</button>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 15px;">supun</td>
                                <td style="padding: 15px; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Need help updating guide profile information and certification documents.</td>
                                <td style="padding: 15px;">5 hours ago</td>
                                <td style="padding: 15px;">
                                    <span class="status-badge status-pending">Pending</span>
                                </td>
                                <td style="padding: 15px;">-</td>
                                <td style="padding: 15px;">
                                    <button class="btn btn-help btn-sm" onclick="openHelpChat('supun', 'help')">Reply</button>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 15px;">chiran</td>
                                <td style="padding: 15px; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Question about driver verification process and required documents.</td>
                                <td style="padding: 15px;">1 day ago</td>
                                <td style="padding: 15px;">
                                    <span class="status-badge status-replied-by-admin">Replied</span>
                                </td>
                                <td style="padding: 15px;">Admin User</td>
                                <td style="padding: 15px;">
                                    <button class="btn btn-info btn-sm" onclick="openHelpChat('chiran', 'help')">View Chat</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Complaints Content -->
                <div class="help-content" id="complaints-content">
                    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                        <thead>
                            <tr>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">User</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">Complaint</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">Date</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">Status</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">Replied By</th>
                                <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: 600; color: var(--primary);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 15px;">akila</td>
                                <td style="padding: 15px; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Driver was late by 2 hours for pickup and was very rude during the trip.</td>
                                <td style="padding: 15px;">1 day ago</td>
                                <td style="padding: 15px;">
                                    <span class="status-badge status-complaint">Pending</span>
                                </td>
                                <td style="padding: 15px;">-</td>
                                <td style="padding: 15px;">
                                    <button class="btn btn-complaint btn-sm" onclick="openHelpChat('robert', 'complaint')">Investigate</button>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 15px;">janani</td>
                                <td style="padding: 15px; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">The guide provided incorrect information about historical sites and seemed unprepared.</td>
                                <td style="padding: 15px;">3 days ago</td>
                                <td style="padding: 15px;">
                                    <span class="status-badge status-replied-by-moderator">Investigated</span>
                                </td>
                                <td style="padding: 15px;">kasun (Content)</td>
                                <td style="padding: 15px;">
                                    <button class="btn btn-info btn-sm" onclick="openHelpChat('janani', 'complaint')">View Details</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Internal Chat -->
        <div class="dashboard-content" id="internal-chat">
            <div class="card">
                <div class="card-header">
                    <h2>Internal Chat</h2>
                </div>
                <div class="chat-container">
                    <div class="chat-sidebar">
                        <div class="chat-search">
                            <input type="text" placeholder="Search staff...">
                        </div>
                        <div class="chat-list" id="chatList">
                            <div class="chat-item active" data-staff="admin">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">Admin User</div>
                                    <div class="chat-item-time">Online</div>
                                </div>
                                <div class="chat-item-preview">Site Administrator</div>
                            </div>
                            <div class="chat-item" data-staff="sewmini">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">sewmini</div>
                                    <div class="chat-item-time">2h ago</div>
                                </div>
                                <div class="chat-item-preview">Support Moderator</div>
                            </div>
                            <div class="chat-item" data-staff="michael">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">pasan</div>
                                    <div class="chat-item-time">1d ago</div>
                                </div>
                                <div class="chat-item-preview">Business Manager</div>
                            </div>
                        </div>
                    </div>
                    <div class="chat-main">
                        <div class="chat-header">
                            <div class="chat-avatar">A</div>
                            <div class="chat-header-info">
                                <h3 id="currentChatName">Admin User</h3>
                                <p id="currentChatStatus">Site Administrator - Online</p>
                            </div>
                        </div>
                        <div class="chat-messages" id="staffChatMessages">
                            <div class="message admin">
                                Hi kasun! How are the new travel packages coming along?
                                <div class="message-time">10 minutes ago</div>
                            </div>
                            <div class="message moderator">
                                Going well! I've added 3 new packages this week.
                                <div class="message-time">5 minutes ago</div>
                            </div>
                        </div>
                        <div class="chat-input">
                            <input type="text" id="staffMessageInput" placeholder="Type your message...">
                            <button id="staffSendButton"><i class="fas fa-paper-plane"></i> Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Settings -->
        <div class="dashboard-content" id="profile">
            <div class="card">
                <div class="card-header">
                    <h2>Profile Settings</h2>
                </div>
                <form onsubmit="return updateProfile(event)">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" value="akasun">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" value="kasun@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" value="kasun">
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role">
                            <option value="content">Content Moderator</option>
                            <option value="support">Support Moderator</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="currentPassword">Current Password</label>
                        <input type="password" id="currentPassword" placeholder="Enter current password">
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" id="newPassword" placeholder="Enter new password">
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <input type="password" id="confirmPassword" placeholder="Confirm new password">
                    </div>
                    <div class="form-group">
                        <label>Profile Picture</label>
                        <input type="file" accept="image/*">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                        <button type="button" class="btn btn-danger">Delete Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Profile Verification Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Profile Verification Details</h2>
                <span class="close" onclick="closeProfileModal()">&times;</span>
            </div>
            <div id="modalContent">
                <div class="profile-details">
                    <div class="detail-group">
                        <h4>Personal Information</h4>
                        <p><strong>Name:</strong> ridam</p>
                        <p><strong>Email:</strong> ridma@gmail.com</p>
                        <p><strong>Phone:</strong> +94782498755</p>
                        <p><strong>Address:</strong> ishuru uyana,rathgama.</p>
                        <p><strong>Role:</strong> Guide</p>
                    </div>
                    <div class="detail-group">
                        <h4>Professional Details</h4>
                        <p><strong>Experience:</strong> 5 years of guiding experience in Europe</p>
                        <p><strong>Languages:</strong> English, French, Spanish</p>
                        <p><strong>Certifications:</strong> Certified Tour Guide License #TG-2024-001</p>
                    </div>
                </div>
                <div class="documents-section">
                    <h3>Submitted Documents</h3>
                    <div class="document-grid">
                        <div class="document-item">
                            <div class="document-preview">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="document-label">ID Card</div>
                        </div>
                        <div class="document-item">
                            <div class="document-preview">
                                <i class="fas fa-file-certificate"></i>
                            </div>
                            <div class="document-label">Guide License</div>
                        </div>
                        <div class="document-item">
                            <div class="document-preview">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <div class="document-label">Certificate</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-danger" onclick="rejectProfile()">Reject Profile</button>
                <button class="btn btn-success" onclick="approveProfile()">Approve Profile</button>
            </div>
        </div>
    </div>

    <!-- Vehicle Verification Modal -->
    <div id="vehicleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Vehicle Verification Details</h2>
                <span class="close" onclick="closeVehicleModal()">&times;</span>
            </div>
            <div id="vehicleModalContent">
                <div class="profile-details">
                    <div class="detail-group">
                        <h4>Driver Information</h4>
                        <p><strong>Name:</strong> chiran</p>
                        <p><strong>Email:</strong> chiran@gamil.com</p>
                        <p><strong>Phone:</strong> +1 (555) 111-2222</p>
                        <p><strong>Address:</strong> 321 Elm St, Miami, FL 33101</p>
                    </div>
                    <div class="detail-group">
                        <h4>Vehicle Information</h4>
                        <p><strong>Make/Model:</strong> Toyota Camry 2023</p>
                        <p><strong>License Plate:</strong> ABC-123</p>
                        <p><strong>Color:</strong> Silver</p>
                        <p><strong>Registration Number:</strong> REG-2024-001</p>
                    </div>
                    <div class="detail-group">
                        <h4>Insurance Information</h4>
                        <p><strong>Insurance Company:</strong> SafeDrive Insurance</p>
                        <p><strong>Policy Number:</strong> POL-789456</p>
                    </div>
                </div>
                <div class="documents-section">
                    <h3>Submitted Documents</h3>
                    <div class="document-grid">
                        <div class="document-item">
                            <div class="document-preview">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="document-label">Vehicle Registration</div>
                        </div>
                        <div class="document-item">
                            <div class="document-preview">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div class="document-label">Insurance Certificate</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn btn-danger" onclick="rejectVehicle()">Reject Vehicle</button>
                <button class="btn btn-success" onclick="approveVehicle()">Approve Vehicle</button>
            </div>
        </div>
    </div>

    <!-- Help Chat Modal -->
    <div id="helpChatModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Help Chat - <span id="helpChatUser">ransara</span></h2>
                <span class="close" onclick="closeHelpChat()">&times;</span>
            </div>
            <div class="chat-messages" id="helpChatMessages" style="height: 400px; margin-bottom: 20px; background: #f8f9fa; padding: 20px; border-radius: 12px;">
                <!-- Messages will be populated by JavaScript -->
            </div>
            <div style="display: flex; gap: 10px;">
                <input type="text" id="helpMessageInput" placeholder="Type your reply..." style="flex: 1; padding: 12px 15px; border: 2px solid #e9ecef; border-radius: 25px; font-size: 0.95rem;">
                <button class="btn btn-primary" onclick="sendHelpMessage()"><i class="fas fa-paper-plane"></i> Send</button>
            </div>
        </div>
    </div>

    <script>
        // Tab Navigation
        function switchTab(tabId) {
            // Remove active class from all links and content
            document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
            document.querySelectorAll('.dashboard-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked link
            const link = document.querySelector(`[data-tab="${tabId}"]`);
            if (link) {
                link.classList.add('active');
            }
            
            // Show corresponding content
            const content = document.getElementById(tabId);
            if (content) {
                content.classList.add('active');
            }
            
            // Update header title
            const headerTitle = document.querySelector('.header h1');
            const titles = {
                'dashboard': 'Moderator Dashboard',
                'verification': 'Verification',
                'packages': 'Travel Packages',
                'places': 'Places',
                'help-portal': 'Help Portal Messages',
                'internal-chat': 'Internal Chat',
                'profile': 'Profile Settings'
            };
            if (headerTitle) {
                headerTitle.textContent = titles[tabId] || 'Moderator Dashboard';
            }
        }

        // Add event listeners to sidebar links
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tabId = this.getAttribute('data-tab');
                switchTab(tabId);
            });
        });

        // Verification Tabs
        function switchVerificationTab(tabType) {
            document.querySelectorAll('.verification-tab').forEach(tab => {
                tab.style.background = '#f8f9fa';
                tab.style.color = '#666';
                tab.style.borderBottom = '2px solid transparent';
            });
            document.querySelectorAll('.verification-content').forEach(content => {
                content.style.display = 'none';
            });
            
            const activeTab = document.querySelector(`[data-tab="${tabType}"]`);
            if (activeTab) {
                activeTab.style.background = 'white';
                activeTab.style.color = '#006A71';
                activeTab.style.borderBottom = '2px solid #006A71';
            }
            
            const content = document.getElementById(`${tabType}-verification`);
            if (content) {
                content.style.display = 'block';
            }
        }

        // Add event listeners to verification tabs
        document.querySelectorAll('.verification-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabType = this.getAttribute('data-tab');
                switchVerificationTab(tabType);
            });
        });

        // Help Portal Tabs - Fixed to preserve content and avoid null errors
        function switchHelpTab(tabType) {
            // Update active tab styling
            const tabs = document.querySelectorAll('.help-tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            
            const activeTab = document.querySelector(`[data-help-type="${tabType}"]`);
            if (activeTab) {
                activeTab.classList.add('active');
            }
            
            // Hide all content and show selected content
            const helpMessagesContent = document.getElementById('help-messages-content');
            const complaintsContent = document.getElementById('complaints-content');
            
            if (tabType === 'help') {
                if (helpMessagesContent) {
                    helpMessagesContent.classList.add('active');
                    helpMessagesContent.style.display = 'block';
                }
                if (complaintsContent) {
                    complaintsContent.classList.remove('active');
                    complaintsContent.style.display = 'none';
                }
            } else if (tabType === 'complaints') {
                if (complaintsContent) {
                    complaintsContent.classList.add('active');
                    complaintsContent.style.display = 'block';
                }
                if (helpMessagesContent) {
                    helpMessagesContent.classList.remove('active');
                    helpMessagesContent.style.display = 'none';
                }
            }
        }

        // Add event listeners to help tabs
        document.querySelectorAll('.help-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabType = this.getAttribute('data-help-type');
                switchHelpTab(tabType);
            });
        });

        // Modal Functions
        function openProfileModal(userId) {
            const modal = document.getElementById('profileModal');
            if (modal) {
                modal.style.display = 'block';
            }
        }

        function closeProfileModal() {
            const modal = document.getElementById('profileModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function openVehicleModal(driverId) {
            const modal = document.getElementById('vehicleModal');
            if (modal) {
                modal.style.display = 'block';
            }
        }

        function closeVehicleModal() {
            const modal = document.getElementById('vehicleModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Help Chat Data with different reply types
        const helpChatData = {
            john: {
                name: "lalinda",
                type: "help",
                messages: [
                    { sender: "user", text: "Hi, I'm having trouble with my itinerary booking. The payment went through but I didn't receive confirmation.", time: "2 hours ago" },
                    { sender: "other-moderator", text: "Hello lalinda! I see your payment was processed. Let me check your booking status right away.", time: "1 hour ago", moderator: "sewmini" },
                    { sender: "other-moderator", text: "I found the issue - there was a small delay in our system. Your confirmation email has been sent now.", time: "1 hour ago", moderator: "sewminni" }
                ]
            },
            supun: {
                name: "supun",
                type: "help",
                messages: [
                    { sender: "user", text: "Hello, I need assistance updating my profile information as a guide.", time: "5 hours ago" }
                ]
            },
            chiran: {
                name: "chiran",
                type: "help",
                messages: [
                    { sender: "user", text: "Question about driver verification process and required documents.", time: "1 day ago" },
                    { sender: "admin", text: "Hi chiran! For driver verification, you need to submit your driver's license, vehicle registration, and insurance certificate through your profile.", time: "12 hours ago" },
                    { sender: "admin", text: "Once submitted, our team will review within 24-48 hours.", time: "12 hours ago" }
                ]
            },
            robert: {
                name: "akila",
                type: "complaint",
                messages: [
                    { sender: "user", text: "Driver was late by 2 hours for pickup and was very rude during the trip.", time: "1 day ago" }
                ]
            },
            janani: {
                name: "janani",
                type: "complaint",
                messages: [
                    { sender: "user", text: "The guide provided incorrect information about historical sites and seemed unprepared.", time: "3 days ago" },
                    { sender: "moderator", text: "Thank you for reporting this, janani. I've investigated the guide's performance and will be providing additional training.", time: "2 days ago", moderator: "kasun" },
                    { sender: "moderator", text: "We've also issued you a partial refund for the inconvenience.", time: "2 days ago", moderator: "kasun" }
                ]
            }
        };

        function openHelpChat(userId, type) {
            const user = helpChatData[userId];
            if (!user) return;
            
            const userNameElement = document.getElementById('helpChatUser');
            if (userNameElement) {
                userNameElement.textContent = user.name;
            }
            
            // Load messages
            const messagesContainer = document.getElementById('helpChatMessages');
            if (messagesContainer) {
                messagesContainer.innerHTML = '';
                
                user.messages.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message ${msg.sender}`;
                    let messageText = msg.text;
                    
                    // Add moderator name for other moderators
                    if (msg.sender === 'other-moderator' && msg.moderator) {
                        messageText = `<strong>${msg.moderator}:</strong> ${msg.text}`;
                    } else if (msg.sender === 'moderator' && msg.moderator) {
                        messageText = `<strong>${msg.moderator}:</strong> ${msg.text}`;
                    }
                    
                    messageDiv.innerHTML = `
                        ${messageText}
                        <div class="message-time">${msg.time}</div>
                    `;
                    messagesContainer.appendChild(messageDiv);
                });
                
                // Scroll to bottom
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            
            // Show modal
            const modal = document.getElementById('helpChatModal');
            if (modal) {
                modal.style.display = 'block';
            }
        }

        function closeHelpChat() {
            const modal = document.getElementById('helpChatModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function approveProfile() {
            if (confirm('Are you sure you want to approve this profile?')) {
                alert('Profile approved successfully!');
                closeProfileModal();
            }
        }

        function rejectProfile() {
            if (confirm('Are you sure you want to reject this profile?')) {
                alert('Profile rejected successfully!');
                closeProfileModal();
            }
        }

        function approveVehicle() {
            if (confirm('Are you sure you want to approve this vehicle?')) {
                alert('Vehicle approved successfully!');
                closeVehicleModal();
            }
        }

        function rejectVehicle() {
            if (confirm('Are you sure you want to reject this vehicle?')) {
                alert('Vehicle rejected successfully!');
                closeVehicleModal();
            }
        }

        function sendHelpMessage() {
            const messageInput = document.getElementById('helpMessageInput');
            if (!messageInput) return;
            
            const messageText = messageInput.value.trim();
            
            if (!messageText) {
                alert('Please enter a message.');
                return;
            }
            
            const userNameElement = document.getElementById('helpChatUser');
            if (!userNameElement) return;
            
            const userName = userNameElement.textContent;
            let userId = '';
            for (const [key, value] of Object.entries(helpChatData)) {
                if (value.name === userName) {
                    userId = key;
                    break;
                }
            }
            
            if (userId) {
                const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                
                // Add message to chat data
                helpChatData[userId].messages.push({
                    sender: "moderator",
                    text: messageText,
                    time: currentTime,
                    moderator: "kasun"
                });
                
                // Add message to UI
                const messagesContainer = document.getElementById('helpChatMessages');
                if (messagesContainer) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message moderator';
                    messageDiv.innerHTML = `
                        <strong>sakun:</strong> ${messageText}
                        <div class="message-time">${currentTime}</div>
                    `;
                    messagesContainer.appendChild(messageDiv);
                    
                    // Clear input and scroll to bottom
                    messageInput.value = '';
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
                
                // Update the help portal table status in the correct tab
                const messageType = helpChatData[userId].type;
                const targetContentId = messageType === 'complaint' ? 'complaints-content' : 'help-messages-content';
                const helpPortalRows = document.querySelectorAll(`#${targetContentId} table tbody tr`);
                
                helpPortalRows.forEach(row => {
                    const userNameCell = row.querySelector('td:first-child');
                    if (userNameCell && userNameCell.textContent === userName) {
                        const statusCell = row.querySelector('td:nth-child(4)');
                        const repliedByCell = row.querySelector('td:nth-child(5)');
                        const actionCell = row.querySelector('td:last-child');
                        
                        if (messageType === 'complaint') {
                            if (statusCell) statusCell.innerHTML = '<span class="status-badge status-replied-by-moderator">Investigated</span>';
                        } else {
                            if (statusCell) statusCell.innerHTML = '<span class="status-badge status-replied-by-moderator">Replied</span>';
                        }
                        
                        if (repliedByCell) repliedByCell.textContent = 'kasun (Content)';
                        if (actionCell) actionCell.innerHTML = `<button class="btn btn-info btn-sm" onclick="openHelpChat('${userId}', '${messageType}')">View Chat</button>`;
                    }
                });
                
                alert('Reply sent successfully!');
            }
        }

        // Places Management
        function showAddPlaceForm() {
            const form = document.getElementById('addPlaceForm');
            if (form) {
                form.style.display = 'block';
            }
        }

        function hideAddPlaceForm() {
            const form = document.getElementById('addPlaceForm');
            if (form) {
                form.style.display = 'none';
            }
        }

        function addPlace(e) {
            e.preventDefault();
            alert('Place added successfully!');
            hideAddPlaceForm();
            const form = document.querySelector('#addPlaceForm form');
            if (form) {
                form.reset();
            }
        }

        function updateProfile(e) {
            e.preventDefault();
            alert('Profile updated successfully!');
        }

        // Internal Chat - Fixed Chat Selection
        let currentChat = 'admin';
        const chatData = {
            admin: {
                name: "Admin User",
                role: "Site Administrator",
                status: "Online",
                messages: [
                    { sender: "admin", text: "Hi kasun! How are the new travel packages coming along?", time: "10 minutes ago" },
                    { sender: "moderator", text: "Going well! I've added 3 new packages this week.", time: "5 minutes ago" }
                ]
            },
            sewmini: {
                name: "sewmini",
                role: "Support Moderator",
                status: "Last seen 2h ago",
                messages: [
                    { sender: "staff", text: "We've been getting more help requests about payment issues lately.", time: "2 hours ago" },
                    { sender: "moderator", text: "Let's schedule a meeting to discuss this. Can you compile the common issues?", time: "1 hour ago" }
                ]
            },
            michael: {
                name: "pasan",
                role: "Business Manager",
                status: "Last seen 1d ago",
                messages: [
                    { sender: "staff", text: "The quarterly revenue report is ready for your review.", time: "1 day ago" },
                    { sender: "moderator", text: "Thanks Michael. I'll review it by end of day.", time: "12 hours ago" }
                ]
            }
        };

        function loadChat(staffId) {
            currentChat = staffId;
            const chat = chatData[staffId];
            
            if (!chat) return;
            
            // Update header
            const chatNameElement = document.getElementById('currentChatName');
            const chatStatusElement = document.getElementById('currentChatStatus');
            if (chatNameElement) chatNameElement.textContent = chat.name;
            if (chatStatusElement) chatStatusElement.textContent = `${chat.role} - ${chat.status}`;
            
            // Update chat list active state
            document.querySelectorAll('.chat-item').forEach(item => {
                item.classList.remove('active');
            });
            const activeItem = document.querySelector(`[data-staff="${staffId}"]`);
            if (activeItem) {
                activeItem.classList.add('active');
            }
            
            // Load messages
            const messagesContainer = document.getElementById('staffChatMessages');
            if (messagesContainer) {
                messagesContainer.innerHTML = '';
                
                chat.messages.forEach(msg => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message ${msg.sender === 'moderator' ? 'moderator' : (msg.sender === 'admin' ? 'admin' : 'admin')}`;
                    messageDiv.innerHTML = `
                        ${msg.text}
                        <div class="message-time">${msg.time}</div>
                    `;
                    messagesContainer.appendChild(messageDiv);
                });
                
                // Scroll to bottom
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }

        // Add click event to chat items
        document.querySelectorAll('.chat-item').forEach(item => {
            item.addEventListener('click', function() {
                const staffId = this.getAttribute('data-staff');
                if (staffId) {
                    loadChat(staffId);
                }
            });
        });

        // Send message function
        document.getElementById('staffSendButton').addEventListener('click', function() {
            sendMessage();
        });

        document.getElementById('staffMessageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        function sendMessage() {
            const messageInput = document.getElementById('staffMessageInput');
            if (!messageInput) return;
            
            const messageText = messageInput.value.trim();
            
            if (!messageText) return;
            
            // Add message to current chat
            const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            if (chatData[currentChat]) {
                chatData[currentChat].messages.push({
                    sender: "moderator",
                    text: messageText,
                    time: currentTime
                });
            }
            
            // Update UI
            loadChat(currentChat);
            
            // Clear input
            messageInput.value = '';
        }

        // Initialize with admin chat
        loadChat('admin');

        // Initialize help portal with help messages active
        switchHelpTab('help');

        // Initialize with dashboard active
        switchTab('dashboard');
    </script>
</body>
</html>