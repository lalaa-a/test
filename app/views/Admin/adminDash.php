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

        /* If you need to apply globally without a specific class */

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
            display: flex;
            justify-content: center;
        }
        
        .sidebar-logo {
            width: 100px;
            height: 55px;
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
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: capitalize;
        }
        .status-badge.site_moderator {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .status-badge.business_manager {
            background-color: #f3e5f5;
            color: #7b1fa2;
        }
        .btn-sm {
            padding: 4px 8px;
            font-size: 0.8rem;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-danger:hover {
            background-color: #c82333;
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
            position: relative;
        }
        .vehicle-photo-thumbnail {
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .vehicle-photo-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            border-color: var(--primary);
        }
        .vehicle-photo-thumbnail::after {
            content: '🔍 Click to view';
            position: absolute;
            bottom: 5px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
        }
        .vehicle-photo-thumbnail:hover::after {
            opacity: 1;
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
        
        /* Image Viewer Modal */
        .image-modal-content {
            max-width: 90vw;
            max-height: 90vh;
        }
        .image-viewer-container {
            text-align: center;
            padding: 20px;
        }
        .image-viewer-container img {
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
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
            .sidebar-header {
                padding: 15px 10px;
            }
            .logo-container {
                padding: 10px;
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

        /* Itinerary Details Styles */
        .itinerary-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        .info-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .info-card h3 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .info-card h3 i {
            font-size: 20px;
        }
        
        .info-item {
            margin-bottom: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        
        .info-item strong {
            min-width: 120px;
            color: #374151;
        }
        
        .info-item span {
            color: #6b7280;
            flex: 1;
        }
        
        .guide-info, .driver-info {
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }
        
        .guide-avatar, .driver-avatar {
            width: 60px;
            height: 60px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }
        
        .guide-details, .driver-details {
            flex: 1;
        }
        
        .guide-card {
            border-left: 4px solid #28a745;
        }
        
        .driver-card {
            border-left: 4px solid #17a2b8;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-completed {
            background-color: #cce5ff;
            color: #004085;
        }
        
        @media (max-width: 768px) {
            .itinerary-info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .guide-info, .driver-info {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .info-item {
                justify-content: center;
            }
            
            .info-item strong {
                min-width: auto;
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
                <?php
                $user = getLoggedInUser();
                $firstInitial = !empty($user['fullname']) ? strtoupper(substr($user['fullname'], 0, 1)) : 'A';
                $profilePhoto = $user['profile_photo'] ?? null;
                ?>
                <div class="user-avatar">
                    <?php if (!empty($profilePhoto) && file_exists(ROOT_PATH.'/public/'.$user['profile_photo'])): ?>
                        <img src="<?=URL_ROOT.'/public/'.$user['profile_photo']?>" alt="Profile Photo">
                    <?php else: ?>
                        <?= $firstInitial ?>
                    <?php endif; ?>
                </div>
                <span><?= htmlspecialchars($user['fullname'] ?? 'Admin User') ?></span>
                <button class="logout-btn" onclick="window.location.href='<?php echo URL_ROOT; ?>/user/logout'">Logout</button>
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
                    <div class="stat-number">Rs. 37,183,500</div>
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
                            chiran reports payment went through but no confirmation received for itinerary ITN-001.
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
                            sewmini needs assistance updating her guide profile information and certification documents.
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
                            chiran submitted new vehicle registration documents for verification as a driver.
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
                            akila requests cancellation of itinerary ITN-005 due to personal emergency.
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
                            <td>chiran sandeepa</td>
                            <td>chiran@gmail.com </td>
                            <td>Traveller</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="btn btn-danger btn-sm">Ban</button>
                            </td>
                        </tr>
                        <tr>
                            <td>sewmini oshadi</td>
                            <td>sewmini@gmail.com</td>
                            <td>Guide</td>
                            <td><span class="status-badge status-active">Active</span></td>
                            <td>
                                <button class="btn btn-danger btn-sm">Ban</button>
                            </td>
                        </tr>
                        <tr>
                            <td>ransara geeneth</td>
                            <td>ransara@gmail.com</td>
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
                                <td>akila</td>
                                <td>akila@gmail.com</td>
                                <td>Guide</td>
                                <td>License, ID, Certificate</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-profile" data-user="akila">View Details</button>
                                </td>
                            </tr>
                            <tr>
                                <td>chiran</td>
                                <td>chiran@example.com</td>
                                <td>Driver</td>
                                <td>License, ID, Certificate</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-profile" data-user="chiran">View Details</button>
                                </td>
                            </tr>
                            <tr>
                                <td>ridma sandamini</td>
                                <td>ridma@gmail.com</td>
                                <td>Guide</td>
                                <td>License, ID, Certificate</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-profile" data-user="ridma">View Details</button>
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
                                <th>Submitted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="pendingVehiclesTable">
                            <!-- Content will be loaded via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Help Portal - Updated to handle complaints and show who replied -->
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
                    <table class="help-portal-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Replied By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>ransara geeneth</td>
                                <td class="help-message-content">Payment issue with itinerary ITN-001 - payment went through but no confirmation received.</td>
                                <td>2 hours ago</td>
                                <td><span class="status-badge status-replied-by-moderator">Replied</span></td>
                                <td>kasun (Support)</td>
                                <td>
                                    <button class="btn btn-info btn-sm view-help-chat" data-user="ransara">View Chat</button>
                                </td>
                            </tr>
                            <tr>
                                <td>pevindi</td>
                                <td class="help-message-content">Need help updating guide profile information and certification documents.</td>
                                <td>5 hours ago</td>
                                <td><span class="status-badge status-pending">Pending</span></td>
                                <td>-</td>
                                <td>
                                    <button class="btn btn-primary btn-sm view-help-chat" data-user="pevindi">Reply</button>
                                </td>
                            </tr>
                            <tr>
                                <td>lalinda</td>
                                <td class="help-message-content">Submitted vehicle registration documents for driver verification.</td>
                                <td>1 day ago</td>
                                <td><span class="status-badge status-replied-by-moderator">Replied</span></td>
                                <td>lalinda ravishan (Content)</td>
                                <td>
                                    <button class="btn btn-info btn-sm view-help-chat" data-user="mike">View Chat</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Complaints Content -->
                <div class="help-content" id="complaints-content">
                    <table class="help-portal-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Complaint</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Replied By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Robert Davis</td>
                                <td class="help-message-content">Driver was late by 2 hours for pickup and was very rude during the trip.</td>
                                <td>1 day ago</td>
                                <td><span class="status-badge status-complaint">Pending</span></td>
                                <td>-</td>
                                <td>
                                    <button class="btn btn-complaint btn-sm view-help-chat" data-user="robert">Investigate</button>
                                </td>
                            </tr>
                            <tr>
                                <td>chanupa</td>
                                <td class="help-message-content">Request cancellation of itinerary ITN-005 due to personal emergency.</td>
                                <td>1 day ago</td>
                                <td><span class="status-badge status-replied-by-moderator">Investigated</span></td>
                                <td>chanupa dulnuwan (Content)</td>
                                <td>
                                    <button class="btn btn-info btn-sm view-help-chat" data-user="akila">View Details</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
                                    <div class="chat-item-name">sewmini oshadi</div>
                                    <div class="chat-item-time">Online</div>
                                </div>
                                <div class="chat-item-preview">Content Moderator</div>
                            </div>
                            <div class="chat-item" data-staff="kasun">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">kasun</div>
                                    <div class="chat-item-time">2h ago</div>
                                </div>
                                <div class="chat-item-preview">Support Moderator</div>
                            </div>
                            <div class="chat-item" data-staff="tharindu">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">kasun</div>
                                    <div class="chat-item-time">1d ago</div>
                                </div>
                                <div class="chat-item-preview">Business Manager</div>
                            </div>
                            <div class="chat-item" data-staff="ridma">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">ridma sandamini</div>
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
                                <h3>vihanga tharushan</h3>
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
                <form id="addModeratorForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modFullname">Full Name *</label>
                            <input type="text" id="modFullname" name="fullname" placeholder="Enter moderator's full name" required>
                        </div>
                        <div class="form-group">
                            <label for="modEmail">Email Address *</label>
                            <input type="email" id="modEmail" name="email" placeholder="Enter email address" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modPassword">Password *</label>
                            <input type="password" id="modPassword" name="password" placeholder="Enter password" required>
                        </div>
                        <div class="form-group">
                            <label for="modPhone">Phone Number *</label>
                            <input type="tel" id="modPhone" name="phone" placeholder="Enter phone number" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modSecondaryPhone">Secondary Phone</label>
                            <input type="tel" id="modSecondaryPhone" name="secondary_phone" placeholder="Enter secondary phone (optional)">
                        </div>
                        <div class="form-group">
                            <label for="modLanguage">Primary Language *</label>
                            <select id="modLanguage" name="language" required>
                                <option value="">Select Language</option>
                                <option value="English">English</option>
                                <option value="Spanish">Spanish</option>
                                <option value="French">French</option>
                                <option value="German">German</option>
                                <option value="Italian">Italian</option>
                                <option value="Portuguese">Portuguese</option>
                                <option value="Chinese">Chinese</option>
                                <option value="Japanese">Japanese</option>
                                <option value="Arabic">Arabic</option>
                                <option value="Hindi">Hindi</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modDob">Date of Birth *</label>
                            <input type="date" id="modDob" name="dob" max="" required>
                        </div>
                        <div class="form-group">
                            <label for="modGender">Gender *</label>
                            <select id="modGender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                                <option value="Prefer not to say">Prefer not to say</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="modAddress">Address *</label>
                        <textarea id="modAddress" name="address" placeholder="Enter full address" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="modAccountType">Moderator Role *</label>
                        <select id="modAccountType" name="account_type" required>
                            <option value="">Select Role</option>
                            <option value="site_moderator">Site Moderator</option>
                            <option value="business_manager">Business Manager</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Add Moderator</button>
                        <button type="reset" class="btn btn-secondary">Clear Form</button>
                    </div>
                </form>
            </div>

            <!-- Moderator List -->
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h2>Current Moderators</h2>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="moderatorsList">
                            <!-- Moderators will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Itineraries -->
        <div class="dashboard-content" id="itineraries">
            <div class="card">
                <div class="card-header">
                    <h2>View Itinerary Details</h2>
                </div>
                <div class="form-group">
                    <label for="itineraryNumber">Itinerary Number</label>
                    <div class="search-box">
                        <input type="text" id="itineraryNumber" placeholder="Enter itinerary number (e.g., ITN-001)">
                        <button class="btn btn-primary" id="searchItineraryBtn">Search</button>
                    </div>
                </div>
                
                <!-- Loading indicator -->
                <div id="itineraryLoading" style="display: none; text-align: center; padding: 20px;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--primary);"></i>
                    <p>Searching itinerary...</p>
                </div>
                
                <!-- Error message -->
                <div id="itineraryError" style="display: none; padding: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px; margin-top: 15px;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="errorMessage">Itinerary not found</span>
                </div>
                
                <!-- Itinerary Details -->
                <div id="itineraryDetails" style="display: none;">
                    <div class="itinerary-info-grid">
                        <!-- Basic Information Card -->
                        <div class="info-card">
                            <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
                            <div class="info-item">
                                <strong>Itinerary Number:</strong>
                                <span id="displayItineraryNumber">-</span>
                            </div>
                            <div class="info-item">
                                <strong>Traveller:</strong>
                                <span id="displayTravellerName">-</span>
                            </div>
                            <div class="info-item">
                                <strong>Email:</strong>
                                <span id="displayTravellerEmail">-</span>
                            </div>
                            <div class="info-item">
                                <strong>Duration:</strong>
                                <span id="displayDuration">-</span>
                            </div>
                            <div class="info-item">
                                <strong>Destinations:</strong>
                                <span id="displayDestinations">-</span>
                            </div>
                            <div class="info-item">
                                <strong>Total Cost:</strong>
                                <span id="displayTotalCost">-</span>
                            </div>
                            <div class="info-item">
                                <strong>Status:</strong>
                                <span id="displayStatus" class="status-badge">-</span>
                            </div>
                            <div class="info-item">
                                <strong>Created Date:</strong>
                                <span id="displayCreatedDate">-</span>
                            </div>
                        </div>
                        
                        <!-- Guide Information Card -->
                        <div class="info-card guide-card">
                            <h3><i class="fas fa-user-tie"></i> Assigned Guide</h3>
                            <div class="guide-info">
                                <div class="guide-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="guide-details">
                                    <div class="info-item">
                                        <strong>Name:</strong>
                                        <span id="displayGuideName">-</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>ID:</strong>
                                        <span id="displayGuideId">-</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Phone:</strong>
                                        <span id="displayGuidePhone">-</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Email:</strong>
                                        <span id="displayGuideEmail">-</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Languages:</strong>
                                        <span id="displayGuideLanguages">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Driver Information Card -->
                        <div class="info-card driver-card">
                            <h3><i class="fas fa-car"></i> Assigned Driver</h3>
                            <div class="driver-info">
                                <div class="driver-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="driver-details">
                                    <div class="info-item">
                                        <strong>Name:</strong>
                                        <span id="displayDriverName">-</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>ID:</strong>
                                        <span id="displayDriverId">-</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Phone:</strong>
                                        <span id="displayDriverPhone">-</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Email:</strong>
                                        <span id="displayDriverEmail">-</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>License Number:</strong>
                                        <span id="displayDriverLicense">-</span>
                                    </div>
                                    <div class="info-item">
                                        <strong>Vehicle Number:</strong>
                                        <span id="displayVehicleNumber">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                            <td>ridama</td>
                            <td>Rs. 180,000</td>
                            <td>Itinerary Booking</td>
                            <td>2024-01-15</td>
                            <td><span class="status-badge status-active">Completed</span></td>
                        </tr>
                        <tr>
                            <td>TXN-002</td>
                            <td>pevindi</td>
                            <td>Rs. 120,000</td>
                            <td>Guide Service</td>
                            <td>2024-01-14</td>
                            <td><span class="status-badge status-active">Completed</span></td>
                        </tr>
                        <tr>
                            <td>TXN-003</td>
                            <td>pevindi</td>
                            <td>Rs. 67,500</td>
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
    <!-- Vehicle Verification Modal -->
    <div id="vehicleVerificationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Vehicle Verification Details</h2>
                <span class="close vehicle-verification-close">&times;</span>
            </div>
            <div id="vehicleVerificationContent">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-actions">
                <button class="btn btn-danger" id="rejectVehicleVerificationBtn">Reject Vehicle</button>
                <button class="btn btn-success" id="approveVehicleVerificationBtn">Approve Vehicle</button>
            </div>
        </div>
    </div>

    <!-- Image Viewer Modal -->
    <div id="imageViewerModal" class="modal">
        <div class="modal-content image-modal-content">
            <div class="modal-header">
                <h2 id="imageViewerTitle">Image Viewer</h2>
                <span class="close image-viewer-close">&times;</span>
            </div>
            <div class="image-viewer-container">
                <img id="fullSizeImage" src="" alt="Full Size Image" style="max-width: 100%; max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>

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

    <!-- Edit Moderator Modal -->
    <div id="editModeratorModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Moderator</h2>
                <span class="close edit-moderator-close">&times;</span>
            </div>
            <form id="editModeratorForm">
                <input type="hidden" id="editModeratorId" name="id">
                <div class="form-row">
                    <div class="form-group">
                        <label for="editFullname">Full Name*</label>
                        <input type="text" id="editFullname" name="fullname" required>
                    </div>
                    <div class="form-group">
                        <label for="editEmail">Email*</label>
                        <input type="email" id="editEmail" name="email" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="editPhone">Phone*</label>
                        <input type="tel" id="editPhone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="editSecondaryPhone">Secondary Phone</label>
                        <input type="tel" id="editSecondaryPhone" name="secondary_phone">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="editLanguage">Language*</label>
                        <select id="editLanguage" name="language" required>
                            <option value="">Select Language</option>
                            <option value="english">English</option>
                            <option value="sinhala">Sinhala</option>
                            <option value="tamil">Tamil</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editGender">Gender*</label>
                        <select id="editGender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="editDob">Date of Birth*</label>
                        <input type="date" id="editDob" name="dob" max="" required>
                    </div>
                    <div class="form-group">
                        <label for="editAccountType">Account Type*</label>
                        <select id="editAccountType" name="account_type" required>
                            <option value="">Select Role</option>
                            <option value="site_moderator">Site Moderator</option>
                            <option value="business_manager">Business Manager</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="editAddress">Address*</label>
                    <textarea id="editAddress" name="address" rows="3" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelEditModerator">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Moderator</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Set max date for date of birth fields to today
        function setMaxDateForDOB() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('modDob').setAttribute('max', today);
            document.getElementById('editDob').setAttribute('max', today);
        }

        // Call on page load
        document.addEventListener('DOMContentLoaded', function() {
            setMaxDateForDOB();
        });

        // Additional client-side validation for date of birth
        function validateDateOfBirth(dateString) {
            const dobDate = new Date(dateString);
            const today = new Date();
            
            if (dobDate > today) {
                return 'Date of birth cannot be in the future';
            }
            
            // Check minimum age (18 years)
            const minAgeDate = new Date();
            minAgeDate.setFullYear(minAgeDate.getFullYear() - 18);
            
            if (dobDate > minAgeDate) {
                return 'Moderator must be at least 18 years old';
            }
            
            return null; // Valid
        }

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
        // Itinerary Search
        document.getElementById('searchItineraryBtn').addEventListener('click', function() {
            searchItinerary();
        });
        
        // Allow Enter key to trigger search
        document.getElementById('itineraryNumber').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchItinerary();
            }
        });
        
        function searchItinerary() {
            const itineraryNumber = document.getElementById('itineraryNumber').value.trim();
            const itineraryDetails = document.getElementById('itineraryDetails');
            const itineraryLoading = document.getElementById('itineraryLoading');
            const itineraryError = document.getElementById('itineraryError');
            
            if (!itineraryNumber) {
                showItineraryError('Please enter an itinerary number');
                return;
            }
            
            // Hide previous results and show loading
            itineraryDetails.style.display = 'none';
            itineraryError.style.display = 'none';
            itineraryLoading.style.display = 'block';
            
            // Make AJAX request to get itinerary details
            fetch('<?php echo URL_ROOT; ?>/Admin/getItinerary', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    itinerary_number: itineraryNumber
                })
            })
            .then(response => response.json())
            .then(data => {
                itineraryLoading.style.display = 'none';
                
                if (data.success) {
                    displayItineraryDetails(data.data);
                } else {
                    showItineraryError(data.message || 'Itinerary not found');
                }
            })
            .catch(error => {
                itineraryLoading.style.display = 'none';
                showItineraryError('Error fetching itinerary details. Please try again.');
                console.error('Error:', error);
            });
        }
        
        function displayItineraryDetails(data) {
            // Populate basic information
            document.getElementById('displayItineraryNumber').textContent = data.itinerary_number;
            document.getElementById('displayTravellerName').textContent = data.traveller_name;
            document.getElementById('displayTravellerEmail').textContent = data.traveller_email;
            document.getElementById('displayDuration').textContent = data.duration;
            document.getElementById('displayDestinations').textContent = data.destinations;
            document.getElementById('displayTotalCost').textContent = data.total_cost;
            document.getElementById('displayCreatedDate').textContent = data.created_date;
            
            // Set status with appropriate styling
            const statusElement = document.getElementById('displayStatus');
            statusElement.textContent = data.status;
            statusElement.className = 'status-badge status-' + data.status.toLowerCase();
            
            // Populate guide information
            document.getElementById('displayGuideName').textContent = data.guide_name || 'Not assigned';
            document.getElementById('displayGuideId').textContent = data.guide_id || '-';
            document.getElementById('displayGuidePhone').textContent = data.guide_phone || '-';
            document.getElementById('displayGuideEmail').textContent = data.guide_email || '-';
            document.getElementById('displayGuideLanguages').textContent = data.guide_languages || '-';
            
            // Populate driver information
            document.getElementById('displayDriverName').textContent = data.driver_name || 'Not assigned';
            document.getElementById('displayDriverId').textContent = data.driver_id || '-';
            document.getElementById('displayDriverPhone').textContent = data.driver_phone || '-';
            document.getElementById('displayDriverEmail').textContent = data.driver_email || '-';
            document.getElementById('displayDriverLicense').textContent = data.driver_license || '-';
            document.getElementById('displayVehicleNumber').textContent = data.vehicle_number || '-';
            
            // Show the details
            document.getElementById('itineraryDetails').style.display = 'block';
        }
        
        function showItineraryError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('itineraryError').style.display = 'block';
            document.getElementById('itineraryDetails').style.display = 'none';
        }
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
            akila: {
                name: "akila ponnamperuma",
                email: "akila@gmail.com",
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
            chiran: {
                name: "chiran",
                email: "chiran@example.com",
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
            ridma: {
                name: "ridma sandamini",
                email: "ridma@example.com",
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
                driverName: "ransara geeneth",
                driverEmail: "ransara@gamil.com",
                driverPhone: "+1 (555) 111-2222",
                driverAddress: "lindagawa watta mattaka",
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
        // Mock help chat data with complaints and replies
        const helpChatData = {
            lalinda: {
                name: "lalinda",
                type: "help",
                messages: [
                    { sender: "user", text: "Hi, I'm having trouble with my itinerary booking. The payment went through but I didn't receive confirmation.", time: "2 hours ago" },
                    { sender: "moderator", text: "Hello lalinda! I see your payment was processed. Let me check your booking status right away.", time: "1 hour ago" },
                    { sender: "moderator", text: "I found the issue - there was a small delay in our system. Your confirmation email has been sent now.", time: "1 hour ago" }
                ]
            },
            pevindi: {
                name: "pevindi",
                type: "help",
                messages: [
                    { sender: "user", text: "Hello, I need assistance updating my profile information as a guide.", time: "5 hours ago" }
                ]
            },
            mike: {
                name: "lalinda",
                type: "help",
                messages: [
                    { sender: "user", text: "I've submitted my new vehicle registration documents for verification as a driver.", time: "1 day ago" },
                    { sender: "moderator", text: "Thanks Mike! I've received your documents and will review them within 24 hours.", time: "12 hours ago" },
                    { sender: "moderator", text: "Your documents look good! Your driver profile has been verified and activated.", time: "6 hours ago" }
                ]
            },
            robert: {
                name: "Robert Davis",
                type: "complaint",
                messages: [
                    { sender: "user", text: "Driver was late by 2 hours for pickup and was very rude during the trip.", time: "1 day ago" }
                ]
            },
            akila: {
                name: "akila",
                type: "complaint",
                messages: [
                    { sender: "user", text: "I need to request cancellation of itinerary ITN-005 due to a personal emergency.", time: "1 day ago" },
                    { sender: "moderator", text: "Thank you for reporting this,akila. I understand the situation and will process the cancellation immediately.", time: "12 hours ago" },
                    { sender: "moderator", text: "Your cancellation has been processed and a refund will be issued within 3-5 business days.", time: "12 hours ago" }
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
                            statusCell.innerHTML = '<span class="status-badge status-replied-by-admin">Investigated</span>';
                        } else {
                            statusCell.innerHTML = '<span class="status-badge status-replied-by-admin">Replied</span>';
                        }
                        
                        repliedByCell.textContent = 'Admin User';
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
                name: "lalinda",
                role: "Content Moderator",
                status: "Online",
                messages: [
                    { sender: "staff", text: "Hi Admin! I've reviewed the new content guidelines and they look good.", time: "10 minutes ago" },
                    { sender: "admin", text: "Great! Please implement them starting tomorrow.", time: "5 minutes ago" },
                    { sender: "staff", text: "Will do. Also, we have a new content creator application to review.", time: "2 minutes ago" }
                ]
            },
            kasun: {
                name: "kasun",
                role: "Support Moderator",
                messages: [
                    { sender: "staff", text: "We've been getting more help requests about payment issues lately.", time: "2 hours ago" },
                    { sender: "admin", text: "Let's schedule a meeting to discuss this. Can you compile the common issues?", time: "1 hour ago" }
                ]
            },
            tharindu: {
                name: "tharindu",
                role: "Business Manager",
                messages: [
                    { sender: "staff", text: "The quarterly revenue report is ready for your review.", time: "1 day ago" },
                    { sender: "admin", text: "Thanks tharindu. I'll review it by end of day.", time: "12 hours ago" }
                ]
            },
            ridma: {
                name: "ridma sanadmini",
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
        
        // Load pending vehicles when verification tab is opened
        document.querySelector('[data-tab="verification"]').addEventListener('click', function() {
            setTimeout(() => {
                loadPendingVehicles();
            }, 100);
        });
        
        // Load pending vehicles for verification
        function loadPendingVehicles() {
            fetch('<?php echo URL_ROOT; ?>/VehicleController/getPendingVerification')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updatePendingVehiclesTable(data.vehicles);
                } else {
                    console.error('Error loading pending vehicles:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        // Update pending vehicles table
        function updatePendingVehiclesTable(vehicles) {
            const tbody = document.getElementById('pendingVehiclesTable');
            if (!tbody) return;
            
            tbody.innerHTML = '';
            
            if (vehicles.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No pending vehicle verifications</td></tr>';
                return;
            }
            
            vehicles.forEach(vehicle => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${vehicle.driver_name || 'Unknown Driver'}</td>
                    <td>${vehicle.make} ${vehicle.model} ${vehicle.year}</td>
                    <td>${vehicle.license_plate}</td>
                    <td>${new Date(vehicle.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="viewVehicleDetails(${vehicle.id})">View Details</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // View vehicle details
        function viewVehicleDetails(vehicleId) {
            fetch(`<?php echo URL_ROOT; ?>/VehicleController/getDetails/${vehicleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showVehicleVerificationModal(data.vehicle);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading vehicle details.');
            });
        }
        
        // Show vehicle verification modal
        function showVehicleVerificationModal(vehicle) {
            const modal = document.getElementById('vehicleVerificationModal');
            const content = document.getElementById('vehicleVerificationContent');
            const urlRoot = '<?php echo URL_ROOT; ?>';
            
            // Helper function to get correct image path
            function getImagePath(imagePath) {
                if (!imagePath) return '';
                // Remove 'public/' prefix if it exists
                const cleanPath = imagePath.startsWith('public/') ? imagePath.substring(7) : imagePath;
                console.log('Original path:', imagePath, 'Clean path:', cleanPath, 'Full URL:', urlRoot + '/' + cleanPath);
                return cleanPath;
            }
            
            content.innerHTML = `
                <div class="profile-details">
                    <div class="detail-group">
                        <h4>Driver Information</h4>
                        <p><strong>Name:</strong> ${vehicle.driver_name || 'Unknown'}</p>
                        <p><strong>Email:</strong> ${vehicle.driver_email || 'Unknown'}</p>
                    </div>
                    <div class="detail-group">
                        <h4>Vehicle Information</h4>
                        <p><strong>Make/Model:</strong> ${vehicle.make} ${vehicle.model} ${vehicle.year}</p>
                        <p><strong>License Plate:</strong> ${vehicle.license_plate}</p>
                        <p><strong>Color:</strong> ${vehicle.color}</p>
                        <p><strong>Type:</strong> ${vehicle.vehicle_type}</p>
                        <p><strong>Seat Count:</strong> ${vehicle.seat_count || 'Not specified'}</p>
                        <p><strong>Daily Rate:</strong> ${vehicle.daily_rate ? '$' + parseFloat(vehicle.daily_rate).toFixed(2) : 'Not specified'}</p>
                        <p><strong>Submitted:</strong> ${new Date(vehicle.created_at).toLocaleString()}</p>
                    </div>
                </div>
                <div class="documents-section">
                    <h3>Vehicle Photos</h3>
                    <div class="document-grid">
                        ${vehicle.front_photo ? `
                            <div class="document-item">
                                <div class="document-preview vehicle-photo-thumbnail" onclick="showFullImage('${urlRoot}/public/${getImagePath(vehicle.front_photo)}', 'Front View')">
                                    <img src="${urlRoot}/public/${getImagePath(vehicle.front_photo)}" alt="Front View" 
                                         onload="console.log('Front photo loaded successfully:', this.src);"
                                         onerror="console.log('Front photo failed to load:', this.src, 'Original path:', '${vehicle.front_photo}'); this.style.display='none'; this.parentNode.innerHTML='<i class=\\'fas fa-image\\'></i><br>Image not found';">
                                </div>
                                <div class="document-label">Front View</div>
                            </div>
                        ` : '<div class="document-item"><div class="document-preview"><i class="fas fa-image"></i><br>No front photo</div><div class="document-label">Front View</div></div>'}
                        ${vehicle.back_photo ? `
                            <div class="document-item">
                                <div class="document-preview vehicle-photo-thumbnail" onclick="showFullImage('${urlRoot}/public/${getImagePath(vehicle.back_photo)}', 'Back View')">
                                    <img src="${urlRoot}/public/${getImagePath(vehicle.back_photo)}" alt="Back View" 
                                         onload="console.log('Back photo loaded successfully:', this.src);"
                                         onerror="console.log('Back photo failed to load:', this.src, 'Original path:', '${vehicle.back_photo}'); this.style.display='none'; this.parentNode.innerHTML='<i class=\\'fas fa-image\\'></i><br>Image not found';">
                                </div>
                                <div class="document-label">Back View</div>
                            </div>
                        ` : '<div class="document-item"><div class="document-preview"><i class="fas fa-image"></i><br>No back photo</div><div class="document-label">Back View</div></div>'}
                        ${vehicle.side_photo ? `
                            <div class="document-item">
                                <div class="document-preview vehicle-photo-thumbnail" onclick="showFullImage('${urlRoot}/public/${getImagePath(vehicle.side_photo)}', 'Side View')">
                                    <img src="${urlRoot}/public/${getImagePath(vehicle.side_photo)}" alt="Side View" 
                                         onload="console.log('Side photo loaded successfully:', this.src);"
                                         onerror="console.log('Side photo failed to load:', this.src, 'Original path:', '${vehicle.side_photo}'); this.style.display='none'; this.parentNode.innerHTML='<i class=\\'fas fa-image\\'></i><br>Image not found';">
                                </div>
                                <div class="document-label">Side View</div>
                            </div>
                        ` : '<div class="document-item"><div class="document-preview"><i class="fas fa-image"></i><br>No side photo</div><div class="document-label">Side View</div></div>'}
                    </div>
                </div>
            `;
            
            // Set vehicle ID for approve/reject actions
            modal.setAttribute('data-vehicle-id', vehicle.id);
            modal.style.display = 'block';
        }

        // Show full size image
        function showFullImage(imageSrc, title) {
            const modal = document.getElementById('imageViewerModal');
            const image = document.getElementById('fullSizeImage');
            const titleElement = document.getElementById('imageViewerTitle');
            
            image.src = imageSrc;
            titleElement.textContent = title || 'Vehicle Photo';
            modal.style.display = 'block';
        }
        
        // Close image viewer modal
        document.querySelector('.image-viewer-close').addEventListener('click', function() {
            document.getElementById('imageViewerModal').style.display = 'none';
        });

        // Close image viewer when clicking outside
        window.addEventListener('click', function(event) {
            const imageModal = document.getElementById('imageViewerModal');
            if (event.target === imageModal) {
                imageModal.style.display = 'none';
            }
        });
        
        // Close vehicle verification modal
        document.querySelector('.vehicle-verification-close').addEventListener('click', function() {
            document.getElementById('vehicleVerificationModal').style.display = 'none';
        });
        
        // Approve vehicle
        document.getElementById('approveVehicleVerificationBtn').addEventListener('click', function() {
            const modal = document.getElementById('vehicleVerificationModal');
            const vehicleId = modal.getAttribute('data-vehicle-id');
            
            if (confirm('Are you sure you want to approve this vehicle?')) {
                const formData = new FormData();
                formData.append('status', 'approved');
                
                fetch(`<?php echo URL_ROOT; ?>/VehicleController/verify/${vehicleId}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        modal.style.display = 'none';
                        loadPendingVehicles(); // Refresh the list
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while approving the vehicle.');
                });
            }
        });
        
        // Reject vehicle
        document.getElementById('rejectVehicleVerificationBtn').addEventListener('click', function() {
            const modal = document.getElementById('vehicleVerificationModal');
            const vehicleId = modal.getAttribute('data-vehicle-id');
            
            const rejectionReason = prompt('Please provide a reason for rejection:');
            if (rejectionReason && rejectionReason.trim()) {
                const formData = new FormData();
                formData.append('status', 'rejected');
                formData.append('rejection_reason', rejectionReason.trim());
                
                fetch(`<?php echo URL_ROOT; ?>/VehicleController/verify/${vehicleId}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        modal.style.display = 'none';
                        loadPendingVehicles(); // Refresh the list
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while rejecting the vehicle.');
                });
            }
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('vehicleVerificationModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
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

        // Moderator Management Functions
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#28a745' : '#dc3545'};
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                z-index: 10000;
                animation: slideIn 0.3s ease;
            `;
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Load moderators list
        function loadModerators() {
            fetch('<?= URL_ROOT ?>/Admin/getModerators', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('moderatorsList');
                if (data.success && data.moderators) {
                    tbody.innerHTML = data.moderators.map(moderator => `
                        <tr>
                            <td>${moderator.id}</td>
                            <td>${moderator.fullname}</td>
                            <td>${moderator.email}</td>
                            <td>${moderator.phone}</td>
                            <td>
                                <span class="status-badge ${moderator.account_type}">
                                    ${moderator.account_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}
                                </span>
                            </td>
                            <td>${new Date(moderator.created_at).toLocaleDateString()}</td>
                            <td>${moderator.last_login ? new Date(moderator.last_login).toLocaleDateString() : 'Never'}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editModerator(${moderator.id})" style="margin-right: 5px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteModerator(${moderator.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="8">No moderators found</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading moderators:', error);
                showNotification('Failed to load moderators', 'error');
            });
        }

        // Add moderator form submission
        document.getElementById('addModeratorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const moderatorData = Object.fromEntries(formData);
            
            // Basic validation
            if (!moderatorData.fullname || !moderatorData.email || !moderatorData.password || 
                !moderatorData.phone || !moderatorData.language || !moderatorData.dob || 
                !moderatorData.gender || !moderatorData.address || !moderatorData.account_type) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }

            // Validate date of birth
            const dobError = validateDateOfBirth(moderatorData.dob);
            if (dobError) {
                showNotification(dobError, 'error');
                return;
            }

            fetch('<?= URL_ROOT ?>/Admin/addModerator', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(moderatorData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Moderator added successfully!');
                    this.reset();
                    loadModerators();
                } else {
                    showNotification(data.message || 'Failed to add moderator', 'error');
                }
            })
            .catch(error => {
                console.error('Error adding moderator:', error);
                showNotification('An error occurred while adding the moderator', 'error');
            });
        });

        // Delete moderator
        function deleteModerator(moderatorId) {
            if (!confirm('Are you sure you want to delete this moderator?')) {
                return;
            }

            fetch('<?= URL_ROOT ?>/Admin/deleteModerator', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: moderatorId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Moderator deleted successfully!');
                    loadModerators();
                } else {
                    showNotification(data.message || 'Failed to delete moderator', 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting moderator:', error);
                showNotification('An error occurred while deleting the moderator', 'error');
            });
        }

        // Edit moderator
        function editModerator(moderatorId) {
            // Fetch moderator details
            fetch(`<?= URL_ROOT ?>/Admin/getModerator?id=${moderatorId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.moderator) {
                    const moderator = data.moderator;
                    
                    // Populate the edit form
                    document.getElementById('editModeratorId').value = moderator.id;
                    document.getElementById('editFullname').value = moderator.fullname;
                    document.getElementById('editEmail').value = moderator.email;
                    document.getElementById('editPhone').value = moderator.phone;
                    document.getElementById('editSecondaryPhone').value = moderator.secondary_phone || '';
                    document.getElementById('editLanguage').value = moderator.language;
                    document.getElementById('editGender').value = moderator.gender;
                    document.getElementById('editDob').value = moderator.dob;
                    document.getElementById('editAccountType').value = moderator.account_type;
                    document.getElementById('editAddress').value = moderator.address;
                    
                    // Show the modal
                    document.getElementById('editModeratorModal').style.display = 'block';
                } else {
                    showNotification(data.message || 'Failed to load moderator details', 'error');
                }
            })
            .catch(error => {
                console.error('Error loading moderator:', error);
                showNotification('An error occurred while loading moderator details', 'error');
            });
        }

        // Handle edit moderator form submission
        document.getElementById('editModeratorForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const moderatorData = Object.fromEntries(formData);
            
            // Basic validation
            if (!moderatorData.fullname || !moderatorData.email || !moderatorData.phone || 
                !moderatorData.language || !moderatorData.dob || !moderatorData.gender || 
                !moderatorData.address || !moderatorData.account_type) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }

            // Validate date of birth
            const dobError = validateDateOfBirth(moderatorData.dob);
            if (dobError) {
                showNotification(dobError, 'error');
                return;
            }

            fetch('<?= URL_ROOT ?>/Admin/updateModerator', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(moderatorData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Moderator updated successfully!');
                    document.getElementById('editModeratorModal').style.display = 'none';
                    loadModerators();
                } else {
                    showNotification(data.message || 'Failed to update moderator', 'error');
                }
            })
            .catch(error => {
                console.error('Error updating moderator:', error);
                showNotification('An error occurred while updating the moderator', 'error');
            });
        });

        // Close edit moderator modal
        document.querySelector('.edit-moderator-close').addEventListener('click', function() {
            document.getElementById('editModeratorModal').style.display = 'none';
        });

        document.getElementById('cancelEditModerator').addEventListener('click', function() {
            document.getElementById('editModeratorModal').style.display = 'none';
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const editModal = document.getElementById('editModeratorModal');
            if (event.target === editModal) {
                editModal.style.display = 'none';
            }
        });

        // Load moderators when the moderators tab is shown
        document.querySelector('[data-tab="moderators"]').addEventListener('click', function() {
            setTimeout(() => {
                loadModerators();
            }, 100);
        });
    </script>
</body>
</html>