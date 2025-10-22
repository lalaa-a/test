<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Admin Dashboard</title>
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
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            z-index: 1000;
            border-right: 1px solid #e9ecef;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 20px;
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--primary);
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
        .form-group textarea {
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

        .no-chat-selected {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #888;
            font-style: italic;
        }

        /* Verification Tabs */
        .verification-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .verification-tab {
            padding: 10px 20px;
            background: #f8f9fa;
            border: none;
            border-radius: 6px 6px 0 0;
            cursor: pointer;
            font-weight: 500;
            color: #666;
            transition: all 0.3s ease;
        }

        .verification-tab.active {
            background: white;
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
        }

        .verification-content {
            display: none;
        }

        .verification-content.active {
            display: block;
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

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-header h2 span,
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
            .notifications-grid {
                grid-template-columns: 1fr;
            }
            .profile-details {
                grid-template-columns: 1fr;
            }
            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
            .chat-sidebar {
                width: 250px;
            }
            .message {
                max-width: 85%;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-globe-americas"></i> <span>Travel Admin</span></h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active" data-tab="dashboard"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="#" data-tab="users"><i class="fas fa-users"></i> <span>Manage Users</span></a></li>
            <li><a href="#" data-tab="verification"><i class="fas fa-user-check"></i> <span>Verification</span></a></li>
            <li><a href="#" data-tab="help-portal"><i class="fas fa-headset"></i> <span>Help Portal</span></a></li>
            <li><a href="#" data-tab="internal-chat"><i class="fas fa-comments"></i> <span>Internal Chat</span></a></li>
            <li><a href="#" data-tab="moderators"><i class="fas fa-shield-alt"></i> <span>Moderators</span></a></li>
            <li><a href="#" data-tab="itineraries"><i class="fas fa-map-marked-alt"></i> <span>Itineraries</span></a></li>
            <li><a href="#" data-tab="transactions"><i class="fas fa-file-invoice-dollar"></i> <span>Transactions</span></a></li>
            <li><a href="#" data-tab="profile"><i class="fas fa-user-cog"></i> <span>Profile Settings</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Admin Dashboard</h1>
            <div class="user-info">
                <div class="user-avatar">A</div>
                <span>Admin User</span>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content active" id="dashboard">
            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon trips">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="stat-number">1,247</div>
                    <div class="stat-label">Trips Completed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon earnings">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-number">$247,890</div>
                    <div class="stat-label">Total Earnings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">8,934</div>
                    <div class="stat-label">Active Users</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bookings">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-number">342</div>
                    <div class="stat-label">Pending Bookings</div>
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="card">
                <div class="card-header">
                    <h2>Recent Help Portal Notifications</h2>
                </div>
                <div class="notifications-grid">
                    <div class="notification-card">
                        <div class="notification-header">
                            <div class="notification-title">Payment Issue</div>
                            <div class="notification-time">2 hours ago</div>
                        </div>
                        <div class="notification-content">
                            John Doe reports payment went through but no confirmation received for itinerary ITN-001.
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-primary btn-sm">View Details</button>
                            <button class="btn btn-success btn-sm">Resolve</button>
                        </div>
                    </div>
                    <div class="notification-card">
                        <div class="notification-header">
                            <div class="notification-title">Profile Update Help</div>
                            <div class="notification-time">5 hours ago</div>
                        </div>
                        <div class="notification-content">
                            Jane Smith needs assistance updating her guide profile information and certification documents.
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-primary btn-sm">View Details</button>
                            <button class="btn btn-success btn-sm">Resolve</button>
                        </div>
                    </div>
                    <div class="notification-card">
                        <div class="notification-header">
                            <div class="notification-title">Driver Verification</div>
                            <div class="notification-time">1 day ago</div>
                        </div>
                        <div class="notification-content">
                            Mike Johnson submitted new vehicle registration documents for verification as a driver.
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-primary btn-sm">View Details</button>
                            <button class="btn btn-success btn-sm">Verify</button>
                        </div>
                    </div>
                    <div class="notification-card">
                        <div class="notification-header">
                            <div class="notification-title">Itinerary Cancellation</div>
                            <div class="notification-time">1 day ago</div>
                        </div>
                        <div class="notification-content">
                            Sarah Wilson requests cancellation of itinerary ITN-005 due to personal emergency.
                        </div>
                        <div class="notification-actions">
                            <button class="btn btn-primary btn-sm">View Details</button>
                            <button class="btn btn-warning btn-sm">Process</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manage Users -->
        <div class="dashboard-content" id="users">
            <div class="card">
                <div class="card-header">
                    <h2>Manage Users</h2>
                    <div class="search-box">
                        <input type="text" placeholder="Search users...">
                        <button class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td>john@example.com</td>
                            <td>Traveller</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="btn btn-danger btn-sm">Ban</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td>jane@example.com</td>
                            <td>Guide</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="btn btn-danger btn-sm">Ban</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Mike Johnson</td>
                            <td>mike@example.com</td>
                            <td>Driver</td>
                            <td><span class="status-badge status-banned">Banned</span></td>
                            <td>
                                <button class="btn btn-success btn-sm">Activate</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Verification - Updated with tabs -->
        <div class="dashboard-content" id="verification">
            <div class="card">
                <div class="card-header">
                    <h2>Verification</h2>
                </div>
                
                <!-- Verification Tabs -->
                <div class="verification-tabs">
                    <button class="verification-tab active" data-tab="profile">Profile Verification</button>
                    <button class="verification-tab" data-tab="vehicle">Vehicle Verification</button>
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
                                <td>Sarah Wilson</td>
                                <td>sarah@example.com</td>
                                <td>Guide</td>
                                <td>License, ID, Certificate</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-profile" data-user="sarah">View Details</button>
                                </td>
                            </tr>
                            <tr>
                                <td>David Brown</td>
                                <td>david@example.com</td>
                                <td>Driver</td>
                                <td>License, ID, Certificate</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-profile" data-user="david">View Details</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Emma Thompson</td>
                                <td>emma@example.com</td>
                                <td>Guide</td>
                                <td>License, ID, Certificate</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-profile" data-user="emma">View Details</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Vehicle Verification Content -->
                <div class="verification-content" id="vehicle-verification">
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
                                <td>Mike Johnson</td>
                                <td>Toyota Camry 2023</td>
                                <td>ABC-123</td>
                                <td>Registration, Insurance</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-vehicle" data-driver="mike">View Details</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Robert Davis</td>
                                <td>Honda Accord 2022</td>
                                <td>XYZ-789</td>
                                <td>Registration, Insurance</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-vehicle" data-driver="robert">View Details</button>
                                </td>
                            </tr>
                            <tr>
                                <td>James Wilson</td>
                                <td>Ford Explorer 2023</td>
                                <td>DEF-456</td>
                                <td>Registration, Insurance</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-vehicle" data-driver="james">View Details</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Help Portal - Updated to show moderator replies -->
        <div class="dashboard-content" id="help-portal">
            <div class="card">
                <div class="card-header">
                    <h2>Help Portal Messages</h2>
                </div>
                <table class="help-portal-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>John Doe</td>
                            <td class="help-message-content">Payment issue with itinerary ITN-001 - payment went through but no confirmation received.</td>
                            <td>2 hours ago</td>
                            <td><span class="status-badge status-replied">Replied by Moderator</span></td>
                            <td>
                                <button class="btn btn-info btn-sm view-help-chat" data-user="john">View Chat</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td class="help-message-content">Need help updating guide profile information and certification documents.</td>
                            <td>5 hours ago</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm view-help-chat" data-user="jane">Reply</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Mike Johnson</td>
                            <td class="help-message-content">Submitted vehicle registration documents for driver verification.</td>
                            <td>1 day ago</td>
                            <td><span class="status-badge status-replied">Replied by Moderator</span></td>
                            <td>
                                <button class="btn btn-info btn-sm view-help-chat" data-user="mike">View Chat</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Sarah Wilson</td>
                            <td class="help-message-content">Request cancellation of itinerary ITN-005 due to personal emergency.</td>
                            <td>1 day ago</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-primary btn-sm view-help-chat" data-user="sarah">Reply</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Internal Chat with Staff -->
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
                        <div class="chat-list" id="staffChatList">
                            <div class="chat-item active" data-staff="alex">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">Alex Johnson</div>
                                    <div class="chat-item-time">Online</div>
                                </div>
                                <div class="chat-item-preview">Content Moderator</div>
                            </div>
                            <div class="chat-item" data-staff="lisa">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">Lisa Chen</div>
                                    <div class="chat-item-time">2h ago</div>
                                </div>
                                <div class="chat-item-preview">Support Moderator</div>
                            </div>
                            <div class="chat-item" data-staff="michael">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">Michael Rodriguez</div>
                                    <div class="chat-item-time">1d ago</div>
                                </div>
                                <div class="chat-item-preview">Business Manager</div>
                            </div>
                            <div class="chat-item" data-staff="emma">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">Emma Wilson</div>
                                    <div class="chat-item-time">3d ago</div>
                                </div>
                                <div class="chat-item-preview">Operations Manager</div>
                            </div>
                        </div>
                    </div>
                    <div class="chat-main">
                        <div class="chat-header">
                            <div class="chat-avatar">A</div>
                            <div class="chat-header-info">
                                <h3>Alex Johnson</h3>
                                <p>Content Moderator - Online</p>
                            </div>
                        </div>
                        <div class="chat-messages" id="staffChatMessages">
                            <!-- Messages will be populated by JavaScript -->
                        </div>
                        <div class="chat-input">
                            <input type="text" id="staffMessageInput" placeholder="Type your message...">
                            <button id="staffSendButton"><i class="fas fa-paper-plane"></i> Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Moderators -->
        <div class="dashboard-content" id="moderators">
            <div class="card">
                <div class="card-header">
                    <h2>Add New Moderator</h2>
                </div>
                <form>
                    <div class="form-group">
                        <label for="modName">Full Name</label>
                        <input type="text" id="modName" placeholder="Enter moderator's full name">
                    </div>
                    <div class="form-group">
                        <label for="modEmail">Email Address</label>
                        <input type="email" id="modEmail" placeholder="Enter email address">
                    </div>
                    <div class="form-group">
                        <label for="modPassword">Password</label>
                        <input type="password" id="modPassword" placeholder="Enter password">
                    </div>
                    <div class="form-group">
                        <label for="modRole">Role</label>
                        <select id="modRole">
                            <option value="content">Content Moderator</option>
                            <option value="support">Support Moderator</option>
                            <option value="business">Business Manager</option>
                            <option value="operations">Operations Manager</option>
                            <option value="general">General Moderator</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Add Moderator</button>
                        <button type="reset" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Itineraries -->
        <div class="dashboard-content" id="itineraries">
            <div class="card">
                <div class="card-header">
                    <h2>View Itinerary</h2>
                </div>
                <div class="form-group">
                    <label for="itineraryNumber">Itinerary Number</label>
                    <div class="search-box">
                        <input type="text" id="itineraryNumber" placeholder="Enter itinerary number (e.g., ITN-001)">
                        <button class="btn btn-primary">Search</button>
                    </div>
                </div>
                <div id="itineraryDetails" style="display: none;">
                    <h3>Itinerary Details - ITN-001</h3>
                    <p><strong>Traveller:</strong> John Doe</p>
                    <p><strong>Duration:</strong> 7 days</p>
                    <p><strong>Destinations:</strong> Paris, Rome, Barcelona</p>
                    <p><strong>Guide:</strong> Sarah Wilson</p>
                    <p><strong>Driver:</strong> David Brown</p>
                    <p><strong>Total Cost:</strong> $2,500</p>
                    <p><strong>Status:</strong> Confirmed</p>
                </div>
            </div>
        </div>

        <!-- Transactions -->
        <div class="dashboard-content" id="transactions">
            <div class="card">
                <div class="card-header">
                    <h2>Transaction Reports</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>TXN-001</td>
                            <td>John Doe</td>
                            <td>$1,200</td>
                            <td>Itinerary Booking</td>
                            <td>2024-01-15</td>
                            <td><span class="status-badge status-active">Completed</span></td>
                        </tr>
                        <tr>
                            <td>TXN-002</td>
                            <td>Jane Smith</td>
                            <td>$800</td>
                            <td>Guide Service</td>
                            <td>2024-01-14</td>
                            <td><span class="status-badge status-active">Completed</span></td>
                        </tr>
                        <tr>
                            <td>TXN-003</td>
                            <td>Mike Johnson</td>
                            <td>$450</td>
                            <td>Driver Service</td>
                            <td>2024-01-13</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Profile Settings -->
        <div class="dashboard-content" id="profile">
            <div class="card">
                <div class="card-header">
                    <h2>Profile Settings</h2>
                </div>
                <form>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" value="admin_user">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" value="admin@travel.com">
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
                <span class="close">&times;</span>
            </div>
            <div id="modalContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-actions">
                <button class="btn btn-danger" id="rejectBtn">Reject Profile</button>
                <button class="btn btn-success" id="approveBtn">Approve Profile</button>
            </div>
        </div>
    </div>

    <!-- Vehicle Verification Modal -->
    <div id="vehicleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Vehicle Verification Details</h2>
                <span class="close vehicle-close">&times;</span>
            </div>
            <div id="vehicleModalContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-actions">
                <button class="btn btn-danger" id="rejectVehicleBtn">Reject Vehicle</button>
                <button class="btn btn-success" id="approveVehicleBtn">Approve Vehicle</button>
            </div>
        </div>
    </div>

    <!-- Help Chat Modal -->
    <div id="helpChatModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Help Chat - <span id="helpChatUser"></span></h2>
                <span class="close help-chat-close">&times;</span>
            </div>
            <div class="chat-messages" id="helpChatMessages" style="height: 400px; margin-bottom: 20px;">
                <!-- Messages will be populated by JavaScript -->
            </div>
            <div class="chat-input">
                <input type="text" id="helpMessageInput" placeholder="Type your reply...">
                <button id="helpSendButton" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Send</button>
            </div>
        </div>
    </div>

    <script>
        // Tab Navigation
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all links and content
                document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
                document.querySelectorAll('.dashboard-content').forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Show corresponding content
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
                
                // Update header title
                const headerTitle = document.querySelector('.header h1');
                const titles = {
                    'dashboard': 'Admin Dashboard',
                    'users': 'Manage Users',
                    'verification': 'Verification',
                    'help-portal': 'Help Portal Messages',
                    'internal-chat': 'Internal Chat',
                    'moderators': 'Moderators',
                    'itineraries': 'Itineraries',
                    'transactions': 'Transactions',
                    'profile': 'Profile Settings'
                };
                headerTitle.textContent = titles[tabId];
            });
        });

        // Verification Tabs
        document.querySelectorAll('.verification-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs and content
                document.querySelectorAll('.verification-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.verification-content').forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Show corresponding content
                const tabId = this.getAttribute('data-tab');
                document.getElementById(`${tabId}-verification`).classList.add('active');
            });
        });

        // Itinerary Search
        document.querySelector('#itineraries .btn-primary').addEventListener('click', function() {
            const itineraryNumber = document.getElementById('itineraryNumber').value;
            const itineraryDetails = document.getElementById('itineraryDetails');
            
            if (itineraryNumber.trim()) {
                itineraryDetails.style.display = 'block';
            } else {
                alert('Please enter an itinerary number');
            }
        });

        // Form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Action completed successfully!');
            });
        });

        // Button actions
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.textContent.includes('Ban') || this.textContent.includes('Reject')) {
                    if (confirm('Are you sure you want to perform this action?')) {
                        alert('Action completed successfully!');
                    }
                }
            });
        });

        document.querySelectorAll('.btn-success').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.textContent.includes('Approve') || this.textContent.includes('Activate')) {
                    alert('Action completed successfully!');
                }
            });
        });

        // Profile Verification Modal
        const profileModal = document.getElementById('profileModal');
        const profileCloseBtn = document.querySelector('.close:not(.vehicle-close):not(.help-chat-close)');
        const viewProfileButtons = document.querySelectorAll('.view-profile');

        // Mock user data
        const userData = {
            sarah: {
                name: "Sarah Wilson",
                email: "sarah@example.com",
                role: "Guide",
                phone: "+1 (555) 123-4567",
                address: "123 Main St, New York, NY 10001",
                experience: "5 years of guiding experience in Europe",
                languages: "English, French, Spanish",
                certifications: "Certified Tour Guide License #TG-2024-001",
                documents: [
                    { type: "ID Card", url: "https://placehold.co/300x200/006A71/white?text=ID+Card" },
                    { type: "Guide License", url: "https://placehold.co/300x200/006A71/white?text=Guide+License" },
                    { type: "Certificate", url: "https://placehold.co/300x200/006A71/white?text=Certificate" }
                ]
            },
            david: {
                name: "David Brown",
                email: "david@example.com",
                role: "Driver",
                phone: "+1 (555) 987-6543",
                address: "456 Oak Ave, Los Angeles, CA 90210",
                experience: "8 years of professional driving experience",
                vehicleInfo: "Toyota Camry 2023, License Plate ABC-123",
                licenseNumber: "DL-2024-789",
                documents: [
                    { type: "Driver's License", url: "https://placehold.co/300x200/006A71/white?text=Drivers+License" },
                    { type: "ID Card", url: "https://placehold.co/300x200/006A71/white?text=ID+Card" },
                    { type: "Certificate", url: "https://placehold.co/300x200/006A71/white?text=Professional+Certificate" }
                ]
            },
            emma: {
                name: "Emma Thompson",
                email: "emma@example.com",
                role: "Guide",
                phone: "+1 (555) 456-7890",
                address: "789 Pine St, Chicago, IL 60601",
                experience: "3 years of city tour guiding experience",
                languages: "English, German",
                certifications: "City Tour Guide License #CTG-2024-002",
                documents: [
                    { type: "ID Card", url: "https://placehold.co/300x200/006A71/white?text=ID+Card" },
                    { type: "Guide License", url: "https://placehold.co/300x200/006A71/white?text=Guide+License" },
                    { type: "Certificate", url: "https://placehold.co/300x200/006A71/white?text=Certificate" }
                ]
            }
        };

        // Open modal with user details
        viewProfileButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user');
                const user = userData[userId];
                
                if (user) {
                    let documentsHtml = '';
                    user.documents.forEach(doc => {
                        documentsHtml += `
                            <div class="document-item">
                                <div class="document-preview">
                                    <img src="${doc.url}" alt="${doc.type}">
                                </div>
                                <div class="document-label">${doc.type}</div>
                            </div>
                        `;
                    });

                    const content = `
                        <div class="profile-details">
                            <div class="detail-group">
                                <h4>Personal Information</h4>
                                <p><strong>Name:</strong> ${user.name}</p>
                                <p><strong>Email:</strong> ${user.email}</p>
                                <p><strong>Phone:</strong> ${user.phone}</p>
                                <p><strong>Address:</strong> ${user.address}</p>
                                <p><strong>Role:</strong> ${user.role}</p>
                            </div>
                            <div class="detail-group">
                                <h4>Professional Details</h4>
                                ${user.experience ? `<p><strong>Experience:</strong> ${user.experience}</p>` : ''}
                                ${user.languages ? `<p><strong>Languages:</strong> ${user.languages}</p>` : ''}
                                ${user.vehicleInfo ? `<p><strong>Vehicle:</strong> ${user.vehicleInfo}</p>` : ''}
                                ${user.certifications ? `<p><strong>Certifications:</strong> ${user.certifications}</p>` : ''}
                                ${user.licenseNumber ? `<p><strong>License Number:</strong> ${user.licenseNumber}</p>` : ''}
                            </div>
                        </div>
                        <div class="documents-section">
                            <h3>Submitted Documents</h3>
                            <div class="document-grid">
                                ${documentsHtml}
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('modalContent').innerHTML = content;
                    profileModal.style.display = 'block';
                }
            });
        });

        // Close profile modal
        profileCloseBtn.addEventListener('click', function() {
            profileModal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target === profileModal) {
                profileModal.style.display = 'none';
            }
        });

        // Profile modal action buttons
        document.getElementById('approveBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to approve this profile?')) {
                alert('Profile approved successfully!');
                profileModal.style.display = 'none';
            }
        });

        document.getElementById('rejectBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to reject this profile?')) {
                alert('Profile rejected successfully!');
                profileModal.style.display = 'none';
            }
        });

        // Vehicle Verification Modal
        const vehicleModal = document.getElementById('vehicleModal');
        const vehicleCloseBtn = document.querySelector('.vehicle-close');
        const viewVehicleButtons = document.querySelectorAll('.view-vehicle');

        // Mock vehicle data
        const vehicleData = {
            mike: {
                driverName: "Mike Johnson",
                driverEmail: "mike@example.com",
                driverPhone: "+1 (555) 111-2222",
                driverAddress: "321 Elm St, Miami, FL 33101",
                vehicleMake: "Toyota Camry",
                vehicleYear: "2023",
                licensePlate: "ABC-123",
                vehicleColor: "Silver",
                registrationNumber: "REG-2024-001",
                insuranceCompany: "SafeDrive Insurance",
                insurancePolicy: "POL-789456",
                documents: [
                    { type: "Vehicle Registration", url: "https://placehold.co/300x200/006A71/white?text=Vehicle+Registration" },
                    { type: "Insurance Certificate", url: "https://placehold.co/300x200/006A71/white?text=Insurance+Certificate" }
                ]
            },
            robert: {
                driverName: "Robert Davis",
                driverEmail: "robert@example.com",
                driverPhone: "+1 (555) 333-4444",
                driverAddress: "654 Maple Ave, Seattle, WA 98101",
                vehicleMake: "Honda Accord",
                vehicleYear: "2022",
                licensePlate: "XYZ-789",
                vehicleColor: "Blue",
                registrationNumber: "REG-2024-002",
                insuranceCompany: "AutoProtect Insurance",
                insurancePolicy: "POL-123789",
                documents: [
                    { type: "Vehicle Registration", url: "https://placehold.co/300x200/006A71/white?text=Vehicle+Registration" },
                    { type: "Insurance Certificate", url: "https://placehold.co/300x200/006A71/white?text=Insurance+Certificate" }
                ]
            },
            james: {
                driverName: "James Wilson",
                driverEmail: "james@example.com",
                driverPhone: "+1 (555) 555-6666",
                driverAddress: "987 Cedar Rd, Denver, CO 80202",
                vehicleMake: "Ford Explorer",
                vehicleYear: "2023",
                licensePlate: "DEF-456",
                vehicleColor: "Black",
                registrationNumber: "REG-2024-003",
                insuranceCompany: "National Auto Insurance",
                insurancePolicy: "POL-456123",
                documents: [
                    { type: "Vehicle Registration", url: "https://placehold.co/300x200/006A71/white?text=Vehicle+Registration" },
                    { type: "Insurance Certificate", url: "https://placehold.co/300x200/006A71/white?text=Insurance+Certificate" }
                ]
            }
        };

        // Open vehicle modal with details
        viewVehicleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const driverId = this.getAttribute('data-driver');
                const vehicle = vehicleData[driverId];
                
                if (vehicle) {
                    let documentsHtml = '';
                    vehicle.documents.forEach(doc => {
                        documentsHtml += `
                            <div class="document-item">
                                <div class="document-preview">
                                    <img src="${doc.url}" alt="${doc.type}">
                                </div>
                                <div class="document-label">${doc.type}</div>
                            </div>
                        `;
                    });

                    const content = `
                        <div class="profile-details">
                            <div class="detail-group">
                                <h4>Driver Information</h4>
                                <p><strong>Name:</strong> ${vehicle.driverName}</p>
                                <p><strong>Email:</strong> ${vehicle.driverEmail}</p>
                                <p><strong>Phone:</strong> ${vehicle.driverPhone}</p>
                                <p><strong>Address:</strong> ${vehicle.driverAddress}</p>
                            </div>
                            <div class="detail-group">
                                <h4>Vehicle Information</h4>
                                <p><strong>Make/Model:</strong> ${vehicle.vehicleMake} ${vehicle.vehicleYear}</p>
                                <p><strong>License Plate:</strong> ${vehicle.licensePlate}</p>
                                <p><strong>Color:</strong> ${vehicle.vehicleColor}</p>
                                <p><strong>Registration Number:</strong> ${vehicle.registrationNumber}</p>
                            </div>
                            <div class="detail-group">
                                <h4>Insurance Information</h4>
                                <p><strong>Insurance Company:</strong> ${vehicle.insuranceCompany}</p>
                                <p><strong>Policy Number:</strong> ${vehicle.insurancePolicy}</p>
                            </div>
                        </div>
                        <div class="documents-section">
                            <h3>Submitted Documents</h3>
                            <div class="document-grid">
                                ${documentsHtml}
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('vehicleModalContent').innerHTML = content;
                    vehicleModal.style.display = 'block';
                }
            });
        });

        // Close vehicle modal
        vehicleCloseBtn.addEventListener('click', function() {
            vehicleModal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target === vehicleModal) {
                vehicleModal.style.display = 'none';
            }
        });

        // Vehicle modal action buttons
        document.getElementById('approveVehicleBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to approve this vehicle?')) {
                alert('Vehicle approved successfully!');
                vehicleModal.style.display = 'none';
            }
        });

        document.getElementById('rejectVehicleBtn').addEventListener('click', function() {
            if (confirm('Are you sure you want to reject this vehicle?')) {
                alert('Vehicle rejected successfully!');
                vehicleModal.style.display = 'none';
            }
        });

        // Help Chat Modal
        const helpChatModal = document.getElementById('helpChatModal');
        const helpChatCloseBtn = document.querySelector('.help-chat-close');
        const viewHelpChatButtons = document.querySelectorAll('.view-help-chat');

        // Mock help chat data
        const helpChatData = {
            john: {
                name: "John Doe",
                messages: [
                    { sender: "user", text: "Hi, I'm having trouble with my itinerary booking. The payment went through but I didn't receive confirmation.", time: "2 hours ago" },
                    { sender: "moderator", text: "Hello John! I see your payment was processed. Let me check your booking status right away.", time: "1 hour ago" },
                    { sender: "moderator", text: "I found the issue - there was a small delay in our system. Your confirmation email has been sent now.", time: "1 hour ago" }
                ]
            },
            jane: {
                name: "Jane Smith",
                messages: [
                    { sender: "user", text: "Hello, I need assistance updating my profile information as a guide.", time: "5 hours ago" }
                ]
            },
            mike: {
                name: "Mike Johnson",
                messages: [
                    { sender: "user", text: "I've submitted my new vehicle registration documents for verification as a driver.", time: "1 day ago" },
                    { sender: "moderator", text: "Thanks Mike! I've received your documents and will review them within 24 hours.", time: "12 hours ago" },
                    { sender: "moderator", text: "Your documents look good! Your driver profile has been verified and activated.", time: "6 hours ago" }
                ]
            },
            sarah: {
                name: "Sarah Wilson",
                messages: [
                    { sender: "user", text: "I need to request cancellation of itinerary ITN-005 due to a personal emergency.", time: "1 day ago" }
                ]
            }
        };

        // Open help chat modal
        viewHelpChatButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user');
                const user = helpChatData[userId];
                
                if (user) {
                    // Set user name in modal header
                    document.getElementById('helpChatUser').textContent = user.name;
                    
                    // Load messages
                    const messagesContainer = document.getElementById('helpChatMessages');
                    messagesContainer.innerHTML = '';
                    
                    user.messages.forEach(msg => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = `message ${msg.sender}`;
                        messageDiv.innerHTML = `
                            ${msg.text}
                            <div class="message-time">${msg.time}</div>
                        `;
                        messagesContainer.appendChild(messageDiv);
                    });
                    
                    // Scroll to bottom
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    
                    // Show modal
                    helpChatModal.style.display = 'block';
                }
            });
        });

        // Close help chat modal
        helpChatCloseBtn.addEventListener('click', function() {
            helpChatModal.style.display = 'none';
        });

        // Send help message
        document.getElementById('helpSendButton').addEventListener('click', function() {
            const messageInput = document.getElementById('helpMessageInput');
            const messageText = messageInput.value.trim();
            
            if (!messageText) return;
            
            const userName = document.getElementById('helpChatUser').textContent;
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
                    sender: "admin",
                    text: messageText,
                    time: currentTime
                });
                
                // Add message to UI
                const messagesContainer = document.getElementById('helpChatMessages');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message admin';
                messageDiv.innerHTML = `
                    ${messageText}
                    <div class="message-time">${currentTime}</div>
                `;
                messagesContainer.appendChild(messageDiv);
                
                // Clear input and scroll to bottom
                messageInput.value = '';
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                // Update the help portal table status
                const helpPortalRows = document.querySelectorAll('#help-portal table tbody tr');
                helpPortalRows.forEach(row => {
                    const userNameCell = row.querySelector('td:first-child');
                    if (userNameCell && userNameCell.textContent === userName) {
                        const statusCell = row.querySelector('td:nth-child(4)');
                        statusCell.innerHTML = '<span class="status-badge status-replied">Replied by Admin</span>';
                        const actionCell = row.querySelector('td:last-child');
                        actionCell.innerHTML = '<button class="btn btn-info btn-sm view-help-chat" data-user="' + userId + '">View Chat</button>';
                    }
                });
            }
        });

        // Internal Chat Functionality
        const staffChatList = document.getElementById('staffChatList');
        const staffChatMessages = document.getElementById('staffChatMessages');
        const staffMessageInput = document.getElementById('staffMessageInput');
        const staffSendButton = document.getElementById('staffSendButton');
        const staffChatHeaderInfo = document.querySelector('.chat-header-info');

        // Mock staff chat data
        const staffChatData = {
            alex: {
                name: "Alex Johnson",
                role: "Content Moderator",
                status: "Online",
                messages: [
                    { sender: "staff", text: "Hi Admin! I've reviewed the new content guidelines and they look good.", time: "10 minutes ago" },
                    { sender: "admin", text: "Great! Please implement them starting tomorrow.", time: "5 minutes ago" },
                    { sender: "staff", text: "Will do. Also, we have a new content creator application to review.", time: "2 minutes ago" }
                ]
            },
            lisa: {
                name: "Lisa Chen",
                role: "Support Moderator",
                messages: [
                    { sender: "staff", text: "We've been getting more help requests about payment issues lately.", time: "2 hours ago" },
                    { sender: "admin", text: "Let's schedule a meeting to discuss this. Can you compile the common issues?", time: "1 hour ago" }
                ]
            },
            michael: {
                name: "Michael Rodriguez",
                role: "Business Manager",
                messages: [
                    { sender: "staff", text: "The quarterly revenue report is ready for your review.", time: "1 day ago" },
                    { sender: "admin", text: "Thanks Michael. I'll review it by end of day.", time: "12 hours ago" }
                ]
            },
            emma: {
                name: "Emma Wilson",
                role: "Operations Manager",
                messages: [
                    { sender: "staff", text: "We need to discuss the new driver onboarding process.", time: "3 days ago" },
                    { sender: "admin", text: "Let's set up a meeting for next week. What times work for you?", time: "2 days ago" }
                ]
            }
        };

        // Load staff messages for selected user
        function loadStaffMessages(staffId) {
            const staff = staffChatData[staffId];
            if (!staff) return;
            
            // Update header
            staffChatHeaderInfo.querySelector('h3').textContent = staff.name;
            staffChatHeaderInfo.querySelector('p').textContent = `${staff.role} - ${staff.status || 'Last seen recently'}`;
            
            // Clear and load messages
            staffChatMessages.innerHTML = '';
            staff.messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${msg.sender === 'admin' ? 'admin' : 'moderator'}`;
                messageDiv.innerHTML = `
                    ${msg.text}
                    <div class="message-time">${msg.time}</div>
                `;
                staffChatMessages.appendChild(messageDiv);
            });
            
            // Scroll to bottom
            staffChatMessages.scrollTop = staffChatMessages.scrollHeight;
        }

        // Initialize with Alex's chat
        loadStaffMessages('alex');

        // Staff chat item selection
        staffChatList.addEventListener('click', function(e) {
            const chatItem = e.target.closest('.chat-item');
            if (chatItem) {
                // Update active state
                document.querySelectorAll('.chat-item').forEach(item => {
                    item.classList.remove('active');
                });
                chatItem.classList.add('active');
                
                const staffId = chatItem.getAttribute('data-staff');
                loadStaffMessages(staffId);
            }
        });

        // Send staff message
        function sendStaffMessage() {
            const messageText = staffMessageInput.value.trim();
            if (!messageText) return;
            
            const activeChat = document.querySelector('.chat-item.active');
            if (!activeChat) return;
            
            const staffId = activeChat.getAttribute('data-staff');
            const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            // Add message to chat data
            staffChatData[staffId].messages.push({
                sender: "admin",
                text: messageText,
                time: currentTime
            });
            
            // Add message to UI
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message admin';
            messageDiv.innerHTML = `
                ${messageText}
                <div class="message-time">${currentTime}</div>
            `;
            staffChatMessages.appendChild(messageDiv);
            
            // Clear input and scroll to bottom
            staffMessageInput.value = '';
            staffChatMessages.scrollTop = staffChatMessages.scrollHeight;
        }

        staffSendButton.addEventListener('click', sendStaffMessage);
        staffMessageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendStaffMessage();
            }
        });
    </script>
</body>
</html>
