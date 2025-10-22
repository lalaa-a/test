<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
        .stat-icon.rating {
            background: linear-gradient(135deg, #FF9800, #F57C00);
        }
        .stat-icon.vehicles {
            background: linear-gradient(135deg, #2196F3, #1976D2);
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
        /* Welcome Box */
        .welcome-box {
            background: linear-gradient(135deg, #006A71, #005a5f);
            color: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 25px;
        }
        .welcome-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
        }
        .welcome-info {
            flex: 1;
        }
        .welcome-info h3 {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }
        .welcome-info p {
            margin-bottom: 12px;
            opacity: 0.9;
        }
        .welcome-status {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            font-size: 0.9rem;
        }
        .status-item {
            background: rgba(255,255,255,0.15);
            padding: 6px 12px;
            border-radius: 20px;
        }
        /* Status Toggle */
        .status-toggle {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 15px;
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #4CAF50;
        }
        input:checked + .slider:before {
            transform: translateX(30px);
        }
        /* Vehicle List */
        .vehicle-list {
            margin-top: 15px;
        }
        .vehicle-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .vehicle-item:last-child {
            border-bottom: none;
        }
        .vehicle-info {
            font-size: 0.95rem;
        }
        .vehicle-status {
            font-size: 0.8rem;
            padding: 4px 10px;
            border-radius: 12px;
        }
        .verified {
            background: #d4edda;
            color: #155724;
        }
        .not-verified {
            background: #f8d7da;
            color: #721c24;
        }
        /* Trip Info */
        .trip-info {
            text-align: center;
            padding: 30px 20px;
        }
        .trip-info h3 {
            color: var(--primary);
            margin-bottom: 15px;
        }
        .trip-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }
        .trip-detail-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
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
        .status-completed {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        .status-complaint {
            background: #fadbd8;
            color: #922b21;
        }
        .status-help {
            background: #d6eaf8;
            color: #1a5276;
        }
        /* Requests Grid */
        .requests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .request-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 20px;
            border-left: 4px solid var(--primary);
        }
        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .request-title {
            font-weight: 600;
            color: var(--primary);
        }
        .request-time {
            font-size: 0.8rem;
            color: #888;
        }
        .request-content {
            color: #666;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .request-details {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .request-details p {
            margin: 4px 0;
        }
        .request-actions {
            display: flex;
            gap: 10px;
        }
        /* Tabs */
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        .tab {
            padding: 10px 20px;
            background: #f8f9fa;
            border: none;
            border-radius: 6px 6px 0 0;
            cursor: pointer;
            font-weight: 500;
            color: #666;
            transition: all 0.3s ease;
        }
        .tab.active {
            background: white;
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        /* Itinerary Details Modal */
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
        .itinerary-days {
            margin-top: 20px;
        }
        .day-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }
        .activity-list {
            margin-left: 20px;
        }
        .activity-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-radius: 6px;
        }
        .activity-checkbox {
            margin-right: 15px;
        }
        .activity-details {
            flex: 1;
        }
        .activity-time {
            font-weight: bold;
            color: var(--primary);
        }
        .activity-location {
            color: #666;
            font-size: 0.9rem;
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
        .message.driver {
            background: white;
            color: var(--primary);
            border: 1px solid #e9ecef;
            align-self: flex-start;
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
        /* Profile Settings */
        .profile-section {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 25px;
        }
        .profile-images {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .image-upload {
            text-align: center;
            padding: 20px;
            border: 2px dashed #e9ecef;
            border-radius: 12px;
            background: #f8f9fa;
        }
        .image-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 15px;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }
        .image-label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 10px;
        }
        .image-upload input {
            display: none;
        }
        .upload-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            margin-top: 10px;
        }
        .trip-photos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        .trip-photo {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
            font-size: 0.8rem;
            cursor: pointer;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
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
        /* Verification Section */
        .verification-section {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-top: 25px;
            border: 2px solid #e9ecef;
        }
        .verification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        .verification-status {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .verification-badge {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .verified-badge {
            background: #d4edda;
            color: #155724;
        }
        .pending-badge {
            background: #fff3cd;
            color: #856404;
        }
        .not-verified-badge {
            background: #f8d7da;
            color: #721c24;
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
        /* Help Chat Container */
        .help-chat-container {
            display: flex;
            height: calc(100vh - 250px);
            border: 1px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
        }
        .help-chat-sidebar {
            width: 300px;
            background: white;
            border-right: 1px solid #e9ecef;
            display: flex;
            flex-direction: column;
        }
        .help-chat-list {
            flex: 1;
            overflow-y: auto;
        }
        .help-chat-item {
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .help-chat-item:hover,
        .help-chat-item.active {
            background-color: #f8f9fa;
        }
        .help-chat-item.active {
            background-color: var(--primary-hover);
        }
        .help-chat-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .help-chat-item-name {
            font-weight: 600;
            color: var(--primary);
        }
        .help-chat-item-time {
            font-size: 0.75rem;
            color: #888;
        }
        .help-chat-item-preview {
            font-size: 0.85rem;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .help-chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .help-chat-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            background: white;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .help-chat-header-info h3 {
            color: var(--primary);
            margin-bottom: 2px;
        }
        .help-chat-header-info p {
            font-size: 0.85rem;
            color: #666;
        }
        .help-chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .help-message {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            line-height: 1.4;
            position: relative;
            word-wrap: break-word;
        }
        .help-message.driver {
            background: white;
            color: var(--primary);
            border: 1px solid #e9ecef;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }
        .help-message.admin {
            background: #e3f2fd;
            color: #1976d2;
            align-self: flex-start;
            border: 1px solid #bbdefb;
            border-bottom-left-radius: 4px;
        }
        .help-message-time {
            font-size: 0.7rem;
            opacity: 0.8;
            margin-top: 5px;
            text-align: right;
        }
        .help-chat-input {
            padding: 15px;
            background: white;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 10px;
        }
        .help-chat-input input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 0.95rem;
        }
        .help-chat-input button {
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            background: var(--primary);
            color: white;
            cursor: pointer;
            font-weight: 500;
        }
        .help-chat-input button:hover {
            background: #005a5f;
        }
        /* Vehicle Photos Grid */
        .vehicle-photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .vehicle-photo-item {
            text-align: center;
        }
        .vehicle-photo-preview {
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
            cursor: pointer;
        }
        .vehicle-photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .vehicle-photo-preview i {
            color: #888;
            font-size: 2rem;
        }
        .vehicle-photo-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
        }
        /* License Photos Grid */
        .license-photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .license-photo-item {
            text-align: center;
        }
        .license-photo-preview {
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
            cursor: pointer;
        }
        .license-photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .license-photo-preview i {
            color: #888;
            font-size: 2rem;
        }
        .license-photo-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
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
            .requests-grid {
                grid-template-columns: 1fr;
            }
            .trip-details {
                grid-template-columns: 1fr;
            }
            .welcome-box {
                flex-direction: column;
                text-align: center;
            }
            .welcome-status {
                justify-content: center;
            }
            .profile-section,
            .form-grid {
                grid-template-columns: 1fr;
            }
            .chat-sidebar,
            .help-chat-sidebar {
                width: 250px;
            }
            .message,
            .help-message {
                max-width: 85%;
            }
            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
            .vehicle-photos-grid,
            .license-photos-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-car"></i> <span>Driver Dashboard</span></h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active" data-tab="dashboard"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="#" data-tab="vehicles"><i class="fas fa-car"></i> <span>Vehicles</span></a></li>
            <li><a href="#" data-tab="tours"><i class="fas fa-map-marked-alt"></i> <span>Tours</span></a></li>
            <li><a href="#" data-tab="earnings"><i class="fas fa-dollar-sign"></i> <span>Earnings</span></a></li>
            <li><a href="#" data-tab="requests"><i class="fas fa-envelope"></i> <span>Requests</span></a></li>
            <li><a href="#" data-tab="messaging"><i class="fas fa-comments"></i> <span>Messaging</span></a></li>
            <li><a href="#" data-tab="help-portal"><i class="fas fa-headset"></i> <span>Help Portal</span></a></li>
            <li><a href="#" data-tab="profile"><i class="fas fa-user-cog"></i> <span>Profile</span></a></li>
        </ul>
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Driver Dashboard</h1>
            <div class="user-info">
                <?php
                $user = getLoggedInUser();
                $firstInitial = !empty($user['fullname']) ? strtoupper(substr($user['fullname'], 0, 1)) : 'D';
                ?>
                <div class="user-avatar"><?= $firstInitial ?></div>
                <span><?= htmlspecialchars($user['fullname'] ?? 'Driver User') ?></span>
                <button class="logout-btn" onclick="window.location.href='<?php echo URL_ROOT; ?>/user/logout'">Logout</button>
            </div>
        </div>
        <!-- Dashboard Content -->
        <div class="dashboard-content active" id="dashboard">
            <!-- Welcome Box -->
            <div class="welcome-box">
                <?php
                $user = getLoggedInUser();
                $firstInitial = !empty($user['fullname']) ? strtoupper(substr($user['fullname'], 0, 1)) : 'D';
                $firstName = !empty($user['fullname']) ? explode(' ', $user['fullname'])[0] : 'Driver';
                ?>
                <div class="welcome-avatar"><?= $firstInitial ?></div>
                <div class="welcome-info">
                    <h3>Welcome back, <?= htmlspecialchars($firstName) ?>!</h3>
                    <p><i class="fas fa-star"></i> 4.8 Rating • <span class="status-badge verified">Verified</span></p>
                    <div class="welcome-status">
                        <div class="status-item">1 ongoing trip</div>
                        <div class="status-item">3 upcoming trips</div>
                        <div class="status-item">5 new requests</div>
                    </div>
                    <button class="btn btn-primary">View Requests</button>
                </div>
            </div>
            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon trips">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number">127</div>
                    <div class="stat-label">Completed Trips</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rating">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-number">4.8</div>
                    <div class="stat-label">Average Rating</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon earnings">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-number">$12,450</div>
                    <div class="stat-label">Total Earnings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon vehicles">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="stat-number" id="approvedVehiclesCount">0</div>
                    <div class="stat-label">Approved Vehicles</div>
                </div>
            </div>
            <!-- Current Status -->
            <div class="card">
                <div class="card-header">
                    <h2>Current Status</h2>
                </div>
                <div class="status-toggle">
                    <span>Online Status:</span>
                    <label class="toggle-switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                    <span>Online</span>
                </div>
                <h3>Available Vehicles</h3>
                <div class="vehicle-list">
                    <div class="vehicle-item">
                        <div class="vehicle-info">
                            <strong>Toyota Camry 2023</strong><br>
                            License Plate: ABC-123
                        </div>
                        <span class="vehicle-status verified">Verified</span>
                    </div>
                    <div class="vehicle-item">
                        <div class="vehicle-info">
                            <strong>Honda Accord 2022</strong><br>
                            License Plate: XYZ-789
                        </div>
                        <span class="vehicle-status not-verified">Not Verified</span>
                    </div>
                </div>
            </div>
            <!-- Current Trip -->
            <div class="card">
                <div class="card-header">
                    <h2>Current Trip</h2>
                </div>
                <div class="trip-info">
                    <h3>ITN-001: Paris City Tour</h3>
                    <p><strong>Traveller:</strong> John Doe</p>
                    <div class="trip-details">
                        <div class="trip-detail-item">
                            <strong>Pickup:</strong><br>
                            Eiffel Tower, Paris
                        </div>
                        <div class="trip-detail-item">
                            <strong>Destination:</strong><br>
                            Louvre Museum, Paris
                        </div>
                    </div>
                    <button class="btn btn-primary" style="margin-top: 20px;" onclick="showItinerary('ITN-001')">View Itinerary</button>
                </div>
            </div>
            <!-- Upcoming Trips -->
            <div class="card">
                <div class="card-header">
                    <h2>Upcoming Trips</h2>
                    <button class="btn btn-info">View All</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Traveller</th>
                            <th>Itinerary</th>
                            <th>Pickup</th>
                            <th>Destination</th>
                            <th>Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2024-01-20</td>
                            <td>Jane Smith</td>
                            <td>ITN-002</td>
                            <td>Charles de Gaulle Airport</td>
                            <td>Champs-Élysées</td>
                            <td>4 hours</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-002')">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2024-01-22</td>
                            <td>Mike Johnson</td>
                            <td>ITN-003</td>
                            <td>Gare du Nord</td>
                            <td>Montmartre</td>
                            <td>6 hours</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-003')">View</button>
                            </td>
                        </tr>
                        <tr>
                            <td>2024-01-25</td>
                            <td>Sarah Wilson</td>
                            <td>ITN-004</td>
                            <td>Orly Airport</td>
                            <td>Versailles Palace</td>
                            <td>8 hours</td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-004')">View</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Recent Requests -->
            <div class="card">
                <div class="card-header">
                    <h2>Recent Requests</h2>
                </div>
                <div class="requests-grid">
                    <div class="request-card">
                        <div class="request-header">
                            <div class="request-title">Paris Full Day Tour</div>
                            <div class="request-time">2 hours ago</div>
                        </div>
                        <div class="request-details">
                            <p><strong>Traveller:</strong> John Doe</p>
                            <p><strong>Pickup:</strong> Eiffel Tower, Paris</p>
                            <p><strong>Destination:</strong> Louvre Museum, Paris</p>
                        </div>
                        <div class="request-actions">
                            <button class="btn btn-success btn-sm">Accept</button>
                            <button class="btn btn-danger btn-sm">Decline</button>
                            <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-001')">Details</button>
                        </div>
                    </div>
                    <div class="request-card">
                        <div class="request-header">
                            <div class="request-title">Airport Transfer</div>
                            <div class="request-time">5 hours ago</div>
                        </div>
                        <div class="request-details">
                            <p><strong>Traveller:</strong> Jane Smith</p>
                            <p><strong>Pickup:</strong> Charles de Gaulle Airport</p>
                            <p><strong>Destination:</strong> Champs-Élysées</p>
                        </div>
                        <div class="request-actions">
                            <button class="btn btn-success btn-sm">Accept</button>
                            <button class="btn btn-danger btn-sm">Decline</button>
                            <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-002')">Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Help Portal Tab -->
        <div class="dashboard-content" id="help-portal">
            <div class="card">
                <div class="card-header">
                    <h2>Help Portal</h2>
                </div>
                <!-- Help Portal Tabs -->
                <div class="help-tabs">
                    <button class="help-tab active" data-help-type="help">Help Messages</button>
                    <button class="help-tab" data-help-type="complaints">Complaints</button>
                </div>
                <!-- Help Messages Content -->
                <div class="help-content active" id="help-messages-content">
                    <div class="help-chat-container">
                        <div class="help-chat-sidebar">
                            <div class="help-chat-list" id="helpChatList">
                                <div class="help-chat-item active" data-help="payment">
                                    <div class="help-chat-item-header">
                                        <div class="help-chat-item-name">Payment Issue</div>
                                        <div class="help-chat-item-time">2h ago</div>
                                    </div>
                                    <div class="help-chat-item-preview">Payment went through but no confirmation received</div>
                                </div>
                                <div class="help-chat-item" data-help="profile">
                                    <div class="help-chat-item-header">
                                        <div class="help-chat-item-name">Profile Update</div>
                                        <div class="help-chat-item-time">5h ago</div>
                                    </div>
                                    <div class="help-chat-item-preview">Need help updating driver profile information</div>
                                </div>
                            </div>
                        </div>
                        <div class="help-chat-main">
                            <div class="help-chat-header">
                                <div class="chat-avatar">A</div>
                                <div class="help-chat-header-info">
                                    <h3>Payment Issue</h3>
                                    <p>Help Message - Replied</p>
                                </div>
                            </div>
                            <div class="help-chat-messages" id="helpChatMessages">
                                <div class="help-message driver">
                                    Hi, I'm having trouble with my itinerary booking. The payment went through but I didn't receive confirmation.
                                    <div class="help-message-time">2 hours ago</div>
                                </div>
                                <div class="help-message admin">
                                    Hello! I see your payment was processed. Let me check your booking status right away.
                                    <div class="help-message-time">1 hour ago</div>
                                </div>
                                <div class="help-message admin">
                                    I found the issue - there was a small delay in our system. Your confirmation email has been sent now.
                                    <div class="help-message-time">1 hour ago</div>
                                </div>
                            </div>
                            <div class="help-chat-input">
                                <input type="text" id="helpMessageInput" placeholder="Type your message...">
                                <button id="helpSendButton"><i class="fas fa-paper-plane"></i> Send</button>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 20px; text-align: center;">
                        <button class="btn btn-help" onclick="showNewHelpModal()">Send New Help Message</button>
                    </div>
                </div>
                <!-- Complaints Content -->
                <div class="help-content" id="complaints-content">
                    <div class="help-chat-container">
                        <div class="help-chat-sidebar">
                            <div class="help-chat-list" id="complaintChatList">
                                <div class="help-chat-item active" data-complaint="traveller">
                                    <div class="help-chat-item-header">
                                        <div class="help-chat-item-name">Traveller Behavior</div>
                                        <div class="help-chat-item-time">1d ago</div>
                                    </div>
                                    <div class="help-chat-item-preview">Traveller was disrespectful and damaged equipment</div>
                                </div>
                            </div>
                        </div>
                        <div class="help-chat-main">
                            <div class="help-chat-header">
                                <div class="chat-avatar">A</div>
                                <div class="help-chat-header-info">
                                    <h3>Traveller Behavior</h3>
                                    <p>Complaint - Investigated</p>
                                </div>
                            </div>
                            <div class="help-chat-messages" id="complaintChatMessages">
                                <div class="help-message driver">
                                    Traveller was disrespectful and damaged my equipment during the tour.
                                    <div class="help-message-time">1 day ago</div>
                                </div>
                                <div class="help-message admin">
                                    Thank you for reporting this. We take such incidents seriously and will investigate immediately.
                                    <div class="help-message-time">12 hours ago</div>
                                </div>
                                <div class="help-message admin">
                                    The traveller has been warned and your equipment damage will be compensated. We apologize for the inconvenience.
                                    <div class="help-message-time">6 hours ago</div>
                                </div>
                            </div>
                            <div class="help-chat-input">
                                <input type="text" id="complaintMessageInput" placeholder="Type your message...">
                                <button id="complaintSendButton"><i class="fas fa-paper-plane"></i> Send</button>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 20px; text-align: center;">
                        <button class="btn btn-complaint" onclick="showNewComplaintModal()">Make New Complaint</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Other tabs remain the same as before -->
        <div class="dashboard-content" id="vehicles">
            <div class="card">
                <div class="card-header">
                    <h2>My Vehicles</h2>
                    <button class="btn btn-primary" onclick="showAddVehicleModal()">Add New Vehicle</button>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon vehicles">
                            <i class="fas fa-car"></i>
                        </div>
                        <div class="stat-number" id="totalVehiclesCount">0</div>
                        <div class="stat-label">Total Vehicles</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon trips">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number" id="approvedVehiclesCountTab">0</div>
                        <div class="stat-label">Approved Vehicles</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon earnings">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number" id="pendingVehiclesCount">0</div>
                        <div class="stat-label">Pending Verification</div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Vehicle</th>
                            <th>License Plate</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dynamic content will be loaded here -->
                        <tr>
                            <td colspan="4" style="text-align: center;">Loading vehicles...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="dashboard-content" id="tours">
            <div class="card">
                <div class="card-header">
                    <h2>Tours Management</h2>
                </div>
                <div class="tabs">
                    <button class="tab active" data-tab="today">Today</button>
                    <button class="tab" data-tab="upcoming">Upcoming</button>
                    <button class="tab" data-tab="completed">Completed</button>
                    <button class="tab" data-tab="cancelled">Cancelled</button>
                </div>
                <div class="tab-content active" id="today-content">
                    <div class="trip-info">
                        <h3>ITN-001: Paris City Tour</h3>
                        <p><strong>Traveller:</strong> John Doe</p>
                        <div class="trip-details">
                            <div class="trip-detail-item">
                                <strong>Pickup:</strong><br>
                                Eiffel Tower, Paris
                            </div>
                            <div class="trip-detail-item">
                                <strong>Destination:</strong><br>
                                Louvre Museum, Paris
                            </div>
                        </div>
                        <button class="btn btn-warning" style="margin-top: 20px;" onclick="showItinerary('ITN-001')">Ongoing</button>
                    </div>
                </div>
                <div class="tab-content" id="upcoming-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Traveller</th>
                                <th>Itinerary</th>
                                <th>Pickup</th>
                                <th>Destination</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-01-20</td>
                                <td>Jane Smith</td>
                                <td>ITN-002</td>
                                <td>Charles de Gaulle Airport</td>
                                <td>Champs-Élysées</td>
                                <td>4 hours</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-002')">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-01-22</td>
                                <td>Mike Johnson</td>
                                <td>ITN-003</td>
                                <td>Gare du Nord</td>
                                <td>Montmartre</td>
                                <td>6 hours</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-003')">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-01-25</td>
                                <td>Sarah Wilson</td>
                                <td>ITN-004</td>
                                <td>Orly Airport</td>
                                <td>Versailles Palace</td>
                                <td>8 hours</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-004')">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-content" id="completed-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Traveller</th>
                                <th>Itinerary</th>
                                <th>Pickup</th>
                                <th>Destination</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-01-15</td>
                                <td>Robert Davis</td>
                                <td>ITN-000</td>
                                <td>Hotel de Ville</td>
                                <td>Notre Dame Cathedral</td>
                                <td>3 hours</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-000')">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-content" id="cancelled-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Traveller</th>
                                <th>Itinerary</th>
                                <th>Pickup</th>
                                <th>Destination</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-01-10</td>
                                <td>Alex Thompson</td>
                                <td>ITN-005</td>
                                <td>Lyon Part-Dieu</td>
                                <td>Annecy Old Town</td>
                                <td>5 hours</td>
                                <td>
                                    <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-005')">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="dashboard-content" id="earnings">
            <div class="card">
                <div class="card-header">
                    <h2>Earnings Overview</h2>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon earnings">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-number">$12,450</div>
                        <div class="stat-label">Total Earnings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon trips">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number">$11,200</div>
                        <div class="stat-label">Completed Earnings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon rating">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number">$1,250</div>
                        <div class="stat-label">Pending Earnings</div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Itinerary</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2024-01-15</td>
                            <td>ITN-000</td>
                            <td>$450</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                        </tr>
                        <tr>
                            <td>2024-01-20</td>
                            <td>ITN-001</td>
                            <td>$600</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="dashboard-content" id="requests">
            <div class="card">
                <div class="card-header">
                    <h2>Travel Requests</h2>
                </div>
                <div class="tabs">
                    <button class="tab active" data-tab="pending">Pending</button>
                    <button class="tab" data-tab="accepted">Accepted</button>
                    <button class="tab" data-tab="rejected">Rejected</button>
                </div>
                <div class="tab-content active" id="pending-content">
                    <div class="requests-grid">
                        <div class="request-card">
                            <div class="request-header">
                                <div class="request-title">Paris Full Day Tour</div>
                                <div class="request-time">2 hours ago</div>
                            </div>
                            <div class="request-details">
                                <p><strong>Traveller:</strong> John Doe</p>
                                <p><strong>Pickup:</strong> Eiffel Tower, Paris</p>
                                <p><strong>Destination:</strong> Louvre Museum, Paris</p>
                            </div>
                            <div class="request-actions">
                                <button class="btn btn-success btn-sm">Accept</button>
                                <button class="btn btn-danger btn-sm">Decline</button>
                                <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-001')">Details</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-content" id="accepted-content">
                    <div class="requests-grid">
                        <div class="request-card">
                            <div class="request-header">
                                <div class="request-title">City Tour - Rome</div>
                                <div class="request-time">1 day ago</div>
                            </div>
                            <div class="request-details">
                                <p><strong>Traveller:</strong> Mike Johnson</p>
                                <p><strong>Pickup:</strong> Colosseum, Rome</p>
                                <p><strong>Destination:</strong> Vatican City</p>
                            </div>
                            <div class="request-actions">
                                <button class="btn btn-primary btn-sm" onclick="showItinerary('ITN-003')">View Itinerary</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-content" id="rejected-content">
                    <div class="requests-grid">
                        <div class="request-card">
                            <div class="request-header">
                                <div class="request-title">Day Trip to Versailles</div>
                                <div class="request-time">3 days ago</div>
                            </div>
                            <div class="request-details">
                                <p><strong>Traveller:</strong> Sarah Wilson</p>
                                <p><strong>Pickup:</strong> Paris City Center</p>
                                <p><strong>Destination:</strong> Palace of Versailles</p>
                            </div>
                            <div class="request-actions">
                                <span class="status-badge status-cancelled">Rejected</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-content" id="messaging">
            <div class="card">
                <div class="card-header">
                    <h2>Messaging</h2>
                </div>
                <div class="chat-container">
                    <div class="chat-sidebar">
                        <div class="chat-search">
                            <input type="text" placeholder="Search travelers...">
                        </div>
                        <div class="chat-list" id="travelerChatList">
                            <div class="chat-item active" data-traveler="john">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">John Doe</div>
                                    <div class="chat-item-time">Online</div>
                                </div>
                                <div class="chat-item-preview">Thanks for the great tour!</div>
                            </div>
                            <div class="chat-item" data-traveler="jane">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">Jane Smith</div>
                                    <div class="chat-item-time">2h ago</div>
                                </div>
                                <div class="chat-item-preview">When will you arrive?</div>
                            </div>
                            <div class="chat-item" data-traveler="mike">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">Mike Johnson</div>
                                    <div class="chat-item-time">1d ago</div>
                                </div>
                                <div class="chat-item-preview">Can we extend the tour?</div>
                            </div>
                        </div>
                    </div>
                    <div class="chat-main">
                        <div class="chat-header">
                            <div class="chat-avatar">J</div>
                            <div class="chat-header-info">
                                <h3>John Doe</h3>
                                <p>ITN-001 - Online</p>
                            </div>
                        </div>
                        <div class="chat-messages" id="travelerChatMessages">
                            <!-- Messages will be populated by JavaScript -->
                        </div>
                        <div class="chat-input">
                            <input type="text" id="travelerMessageInput" placeholder="Type your message...">
                            <button id="travelerSendButton"><i class="fas fa-paper-plane"></i> Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="dashboard-content" id="profile">
            <div class="card">
                <div class="card-header">
                    <h2>Profile Settings</h2>
                </div>
                <div class="profile-section">
                    <div class="profile-images">
                        <div class="image-upload">
                            <div class="image-preview">D</div>
                            <div class="image-label">Profile Photo</div>
                            <input type="file" id="profilePhoto" accept="image/*">
                            <button class="upload-btn" onclick="document.getElementById('profilePhoto').click()">Upload Photo</button>
                        </div>
                        <div class="image-upload">
                            <div class="image-label">Trip Photos</div>
                            <div class="trip-photos">
                                <div class="trip-photo">+ Add</div>
                                <div class="trip-photo">Eiffel Tower</div>
                                <div class="trip-photo">Louvre</div>
                                <div class="trip-photo">Notre Dame</div>
                                <div class="trip-photo">Montmartre</div>
                            </div>
                            <input type="file" id="tripPhotos" accept="image/*" multiple>
                            <button class="upload-btn" onclick="document.getElementById('tripPhotos').click()">Add More Photos</button>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" id="fullName" value="David Brown">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" value="david@example.com">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" value="+1 (555) 123-4567">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" value="123 Main St, New York, NY 10001">
                        </div>
                        <div class="form-group">
                            <label for="experience">Driving Experience</label>
                            <input type="text" id="experience" value="8 years of professional driving experience">
                        </div>
                        <div class="form-group">
                            <label for="languages">Languages Spoken</label>
                            <input type="text" id="languages" value="English, French, Spanish">
                        </div>
                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" rows="3">Professional driver with extensive knowledge of Paris and surrounding areas. Passionate about providing exceptional service and ensuring passenger safety and comfort.</textarea>
                        </div>
                    </div>
                </div>
                <!-- Profile Verification Section -->
                <div class="verification-section">
                    <div class="verification-header">
                        <h2>Driver License Verification</h2>
                        <div class="verification-status">
                            <span class="verification-badge verified-badge" id="verificationStatus">Verified</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="licenseNumber">Driver License Number</label>
                        <input type="text" id="licenseNumber" value="DL-2024-789" placeholder="Enter your driver license number">
                    </div>
                    <div class="form-group">
                        <label for="licenseExpiry">License Expiry Date</label>
                        <input type="date" id="licenseExpiry" value="2028-12-31">
                    </div>
                    <div class="form-group">
                        <label>Driver License Photos (Required)</label>
                        <div class="license-photos-grid">
                            <div class="license-photo-item">
                                <div class="license-photo-preview" onclick="document.getElementById('licenseFront').click()">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div class="license-photo-label">Front Side</div>
                                <input type="file" id="licenseFront" accept="image/*" style="display: none;">
                            </div>
                            <div class="license-photo-item">
                                <div class="license-photo-preview" onclick="document.getElementById('licenseBack').click()">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div class="license-photo-label">Back Side</div>
                                <input type="file" id="licenseBack" accept="image/*" style="display: none;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                    <button type="button" class="btn btn-danger">Delete Account</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Itinerary Modal -->
    <div id="itineraryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalItineraryTitle">Itinerary Details</h2>
                <span class="close" onclick="closeItineraryModal()">&times;</span>
            </div>
            <div class="itinerary-days">
                <div class="day-card">
                    <div class="day-header">
                        <h3>Day 1: January 20, 2024</h3>
                        <span class="status-badge status-active">Active</span>
                    </div>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-checkbox">
                                <input type="checkbox" checked>
                            </div>
                            <div class="activity-details">
                                <div class="activity-time">09:00 AM</div>
                                <div class="activity-location">Charles de Gaulle Airport - Pickup</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-checkbox">
                                <input type="checkbox">
                            </div>
                            <div class="activity-details">
                                <div class="activity-time">10:30 AM</div>
                                <div class="activity-location">Arc de Triomphe - Photo Stop</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-checkbox">
                                <input type="checkbox">
                            </div>
                            <div class="activity-details">
                                <div class="activity-time">12:00 PM</div>
                                <div class="activity-location">Champs-Élysées - Lunch</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="day-card">
                    <div class="day-header">
                        <h3>Day 2: January 21, 2024</h3>
                        <span class="status-badge status-pending">Pending</span>
                    </div>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-checkbox">
                                <input type="checkbox">
                            </div>
                            <div class="activity-details">
                                <div class="activity-time">09:00 AM</div>
                                <div class="activity-location">Louvre Museum - Tour</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-checkbox">
                                <input type="checkbox">
                            </div>
                            <div class="activity-details">
                                <div class="activity-time">02:00 PM</div>
                                <div class="activity-location">Seine River Cruise</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-actions" style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px; padding-top: 20px; border-top: 2px solid #f0f0f0;">
                <button class="btn btn-primary" onclick="markItineraryComplete()">Mark Complete</button>
            </div>
        </div>
    </div>
    <!-- Add Vehicle Modal -->
    <div id="addVehicleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Vehicle</h2>
                <span class="close" onclick="closeAddVehicleModal()">&times;</span>
            </div>
            <form id="addVehicleForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="vehicleMake">Make</label>
                        <input type="text" id="vehicleMake" placeholder="e.g., Toyota" required>
                    </div>
                    <div class="form-group">
                        <label for="vehicleModel">Model</label>
                        <input type="text" id="vehicleModel" placeholder="e.g., Camry" required>
                    </div>
                    <div class="form-group">
                        <label for="vehicleYear">Year</label>
                        <input type="number" id="vehicleYear" placeholder="e.g., 2023" min="1900" max="2030" required>
                    </div>
                    <div class="form-group">
                        <label for="licensePlate">License Plate</label>
                        <input type="text" id="licensePlate" placeholder="e.g., ABC-123" required>
                    </div>
                    <div class="form-group">
                        <label for="vehicleColor">Color</label>
                        <input type="text" id="vehicleColor" placeholder="e.g., Silver" required>
                    </div>
                    <div class="form-group">
                        <label for="vehicleType">Vehicle Type</label>
                        <select id="vehicleType" required>
                            <option value="">Select Type</option>
                            <option value="sedan">Sedan</option>
                            <option value="suv">SUV</option>
                            <option value="van">Van</option>
                            <option value="luxury">Luxury</option>
                            <option value="minibus">Minibus</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Vehicle Photos (Required)</label>
                    <div class="vehicle-photos-grid">
                        <div class="vehicle-photo-item">
                            <div class="vehicle-photo-preview" onclick="document.getElementById('frontPhoto').click()">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="vehicle-photo-label">Front View<br><small>(with license plate visible)</small></div>
                            <input type="file" id="frontPhoto" accept="image/*" style="display: none;" required>
                        </div>
                        <div class="vehicle-photo-item">
                            <div class="vehicle-photo-preview" onclick="document.getElementById('backPhoto').click()">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="vehicle-photo-label">Back View<br><small>(with license plate visible)</small></div>
                            <input type="file" id="backPhoto" accept="image/*" style="display: none;" required>
                        </div>
                        <div class="vehicle-photo-item">
                            <div class="vehicle-photo-preview" onclick="document.getElementById('sidePhoto').click()">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="vehicle-photo-label">Side View</div>
                            <input type="file" id="sidePhoto" accept="image/*" style="display: none;" required>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-danger" onclick="closeAddVehicleModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Vehicle</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Edit Vehicle Modal -->
    <div id="editVehicleModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Vehicle</h2>
                <span class="close" onclick="closeEditVehicleModal()">&times;</span>
            </div>
            <form id="editVehicleForm">
                <input type="hidden" id="editVehicleId" value="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="editVehicleMake">Make</label>
                        <input type="text" id="editVehicleMake" placeholder="e.g., Toyota" required>
                    </div>
                    <div class="form-group">
                        <label for="editVehicleModel">Model</label>
                        <input type="text" id="editVehicleModel" placeholder="e.g., Camry" required>
                    </div>
                    <div class="form-group">
                        <label for="editVehicleYear">Year</label>
                        <input type="number" id="editVehicleYear" placeholder="e.g., 2023" min="1900" max="2030" required>
                    </div>
                    <div class="form-group">
                        <label for="editLicensePlate">License Plate</label>
                        <input type="text" id="editLicensePlate" placeholder="e.g., ABC-123" required>
                    </div>
                    <div class="form-group">
                        <label for="editVehicleColor">Color</label>
                        <input type="text" id="editVehicleColor" placeholder="e.g., Silver" required>
                    </div>
                    <div class="form-group">
                        <label for="editVehicleType">Vehicle Type</label>
                        <select id="editVehicleType" required>
                            <option value="">Select Type</option>
                            <option value="sedan">Sedan</option>
                            <option value="suv">SUV</option>
                            <option value="van">Van</option>
                            <option value="luxury">Luxury</option>
                            <option value="minibus">Minibus</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Vehicle Photos (Optional - Leave empty to keep current photos)</label>
                    <div class="vehicle-photos-grid">
                        <div class="vehicle-photo-item">
                            <div class="vehicle-photo-preview" id="editFrontPhotoPreview" onclick="document.getElementById('editFrontPhoto').click()">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="vehicle-photo-label">Front View<br><small>(with license plate visible)</small></div>
                            <input type="file" id="editFrontPhoto" accept="image/*" style="display: none;">
                        </div>
                        <div class="vehicle-photo-item">
                            <div class="vehicle-photo-preview" id="editBackPhotoPreview" onclick="document.getElementById('editBackPhoto').click()">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="vehicle-photo-label">Back View<br><small>(with license plate visible)</small></div>
                            <input type="file" id="editBackPhoto" accept="image/*" style="display: none;">
                        </div>
                        <div class="vehicle-photo-item">
                            <div class="vehicle-photo-preview" id="editSidePhotoPreview" onclick="document.getElementById('editSidePhoto').click()">
                                <i class="fas fa-car"></i>
                            </div>
                            <div class="vehicle-photo-label">Side View</div>
                            <input type="file" id="editSidePhoto" accept="image/*" style="display: none;">
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-danger" onclick="closeEditVehicleModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Vehicle</button>
                </div>
            </form>
        </div>
    </div>
    <!-- New Help Message Modal -->
    <div id="newHelpModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Send New Help Message</h2>
                <span class="close" onclick="closeNewHelpModal()">&times;</span>
            </div>
            <form id="newHelpForm">
                <div class="form-group">
                    <label for="helpSubject">Subject</label>
                    <input type="text" id="helpSubject" placeholder="Brief subject of your help request" required>
                </div>
                <div class="form-group">
                    <label for="helpMessage">Message</label>
                    <textarea id="helpMessage" rows="5" placeholder="Describe your issue or question in detail" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-danger" onclick="closeNewHelpModal()">Cancel</button>
                    <button type="submit" class="btn btn-help">Send Help Message</button>
                </div>
            </form>
        </div>
    </div>
    <!-- New Complaint Modal -->
    <div id="newComplaintModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Make New Complaint</h2>
                <span class="close" onclick="closeNewComplaintModal()">&times;</span>
            </div>
            <form id="newComplaintForm">
                <div class="form-group">
                    <label for="complaintSubject">Subject</label>
                    <input type="text" id="complaintSubject" placeholder="Brief subject of your complaint" required>
                </div>
                <div class="form-group">
                    <label for="complaintMessage">Complaint Details</label>
                    <textarea id="complaintMessage" rows="5" placeholder="Provide detailed information about your complaint" required></textarea>
                </div>
                <div class="form-group">
                    <label for="complaintType">Complaint Type</label>
                    <select id="complaintType" required>
                        <option value="">Select Type</option>
                        <option value="traveller">Traveller Behavior</option>
                        <option value="payment">Payment Issue</option>
                        <option value="system">System/Technical Issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-danger" onclick="closeNewComplaintModal()">Cancel</button>
                    <button type="submit" class="btn btn-complaint">Submit Complaint</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Tab Navigation
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
                document.querySelectorAll('.dashboard-content').forEach(content => content.classList.remove('active'));
                this.classList.add('active');
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
                const headerTitle = document.querySelector('.header h1');
                const titles = {
                    'dashboard': 'Driver Dashboard',
                    'vehicles': 'Vehicles',
                    'tours': 'Tours Management',
                    'earnings': 'Earnings',
                    'requests': 'Travel Requests',
                    'messaging': 'Messaging',
                    'help-portal': 'Help Portal',
                    'profile': 'Profile Settings'
                };
                headerTitle.textContent = titles[tabId];
            });
        });
        // Tour Tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                const parent = this.closest('.card');
                parent.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                parent.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const tabId = this.getAttribute('data-tab');
                parent.querySelector(`#${tabId}-content`).classList.add('active');
            });
        });
        // Help Portal Tabs - FIXED VERSION
        function switchHelpTab(tabType) {
            // Get the help portal card container
            const helpPortalCard = document.querySelector('#help-portal .card');
            if (!helpPortalCard) return;
            
            // Update active tab styling
            const tabs = helpPortalCard.querySelectorAll('.help-tab');
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Find and activate the clicked tab
            const activeTab = helpPortalCard.querySelector(`[data-help-type="${tabType}"]`);
            if (activeTab) {
                activeTab.classList.add('active');
            }
            
            // Hide all help content sections
            const helpContents = helpPortalCard.querySelectorAll('.help-content');
            helpContents.forEach(content => {
                content.classList.remove('active');
                content.style.display = 'none';
            });
            
            // Show the appropriate content based on tab type
            if (tabType === 'help') {
                const helpContent = helpPortalCard.querySelector('#help-messages-content');
                if (helpContent) {
                    helpContent.classList.add('active');
                    helpContent.style.display = 'block';
                }
            } else if (tabType === 'complaints') {
                const complaintsContent = helpPortalCard.querySelector('#complaints-content');
                if (complaintsContent) {
                    complaintsContent.classList.add('active');
                    complaintsContent.style.display = 'block';
                }
            }
        }
        
        // Add event listeners to help tabs
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('help-tab')) {
                const tabType = e.target.getAttribute('data-help-type');
                switchHelpTab(tabType);
            }
        });
        // Itinerary Modal Functions
        function showItinerary(itineraryId) {
            document.getElementById('modalItineraryTitle').textContent = `Itinerary Details - ${itineraryId}`;
            document.getElementById('itineraryModal').style.display = 'block';
        }
        function closeItineraryModal() {
            document.getElementById('itineraryModal').style.display = 'none';
        }
        function markItineraryComplete() {
            if (confirm('Are you sure you want to mark this itinerary as complete?')) {
                alert('Itinerary marked as complete!');
                closeItineraryModal();
            }
        }
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('itineraryModal');
            if (event.target === modal) {
                closeItineraryModal();
            }
        });
        // Add Vehicle Modal Functions
        function showAddVehicleModal() {
            document.getElementById('addVehicleModal').style.display = 'block';
        }
        function closeAddVehicleModal() {
            document.getElementById('addVehicleModal').style.display = 'none';
            document.getElementById('addVehicleForm').reset();
        }
        
        // Edit Vehicle Modal Functions
        function showEditVehicleModal() {
            document.getElementById('editVehicleModal').style.display = 'block';
        }
        function closeEditVehicleModal() {
            document.getElementById('editVehicleModal').style.display = 'none';
            document.getElementById('editVehicleForm').reset();
            // Clear photo previews
            clearEditPhotoPreview('front');
            clearEditPhotoPreview('back');
            clearEditPhotoPreview('side');
        }
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const addModal = document.getElementById('addVehicleModal');
            const editModal = document.getElementById('editVehicleModal');
            if (event.target === addModal) {
                closeAddVehicleModal();
            }
            if (event.target === editModal) {
                closeEditVehicleModal();
            }
        });
        // Handle vehicle photo previews
        document.getElementById('frontPhoto').addEventListener('change', function(e) {
            handlePhotoPreview(e, 'front');
        });
        document.getElementById('backPhoto').addEventListener('change', function(e) {
            handlePhotoPreview(e, 'back');
        });
        document.getElementById('sidePhoto').addEventListener('change', function(e) {
            handlePhotoPreview(e, 'side');
        });
        
        // Edit vehicle photo preview handlers
        document.getElementById('editFrontPhoto').addEventListener('change', function(e) {
            handleEditPhotoPreview(e, 'front');
        });
        document.getElementById('editBackPhoto').addEventListener('change', function(e) {
            handleEditPhotoPreview(e, 'back');
        });
        document.getElementById('editSidePhoto').addEventListener('change', function(e) {
            handleEditPhotoPreview(e, 'side');
        });
        function handleEditPhotoPreview(event, type) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(`edit${type.charAt(0).toUpperCase() + type.slice(1)}PhotoPreview`);
                    preview.innerHTML = `<img src="${e.target.result}" alt="${type} view" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">`;
                };
                reader.readAsDataURL(file);
            }
        }
        
        function clearEditPhotoPreview(type) {
            const preview = document.getElementById(`edit${type.charAt(0).toUpperCase() + type.slice(1)}PhotoPreview`);
            preview.innerHTML = '<i class="fas fa-car"></i>';
        }
        
        function handlePhotoPreview(event, type) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Find the preview div in the same parent container
                    const photoItem = document.querySelector(`#${type}Photo`).parentElement;
                    const preview = photoItem.querySelector('.vehicle-photo-preview');
                    preview.innerHTML = `<img src="${e.target.result}" alt="${type} view" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">`;
                };
                reader.readAsDataURL(file);
            }
        }
        // Handle license photo previews
        document.getElementById('licenseFront').addEventListener('change', function(e) {
            handleLicensePhotoPreview(e, 'front');
        });
        document.getElementById('licenseBack').addEventListener('change', function(e) {
            handleLicensePhotoPreview(e, 'back');
        });
        function handleLicensePhotoPreview(event, type) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.querySelector(`#${type === 'front' ? 'licenseFront' : 'licenseBack'}`).previousElementSibling;
                    preview.innerHTML = `<img src="${e.target.result}" alt="License ${type}">`;
                };
                reader.readAsDataURL(file);
            }
        }
        // Handle form submission
        document.getElementById('addVehicleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Create FormData object to handle file uploads
            const formData = new FormData();
            
            // Add text fields
            formData.append('make', document.getElementById('vehicleMake').value);
            formData.append('model', document.getElementById('vehicleModel').value);
            formData.append('year', document.getElementById('vehicleYear').value);
            formData.append('license_plate', document.getElementById('licensePlate').value);
            formData.append('color', document.getElementById('vehicleColor').value);
            formData.append('vehicle_type', document.getElementById('vehicleType').value);
            
            // Add photo files
            const frontPhoto = document.getElementById('frontPhoto').files[0];
            const backPhoto = document.getElementById('backPhoto').files[0];
            const sidePhoto = document.getElementById('sidePhoto').files[0];
            
            if (!frontPhoto || !backPhoto || !sidePhoto) {
                alert('Please upload all required vehicle photos.');
                return;
            }
            
            formData.append('front_photo', frontPhoto);
            formData.append('back_photo', backPhoto);
            formData.append('side_photo', sidePhoto);
            
            // Submit to backend
            fetch('<?php echo URL_ROOT; ?>/VehicleController/add', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeAddVehicleModal();
                    // Refresh vehicle list and stats
                    loadVehicleList();
                    updateVehicleStats();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the vehicle.');
            });
        });
        
        // Handle edit vehicle form submission
        document.getElementById('editVehicleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const vehicleId = document.getElementById('editVehicleId').value;
            
            // Create FormData object to handle file uploads
            const formData = new FormData();
            
            // Add text fields
            formData.append('id', vehicleId);
            formData.append('make', document.getElementById('editVehicleMake').value);
            formData.append('model', document.getElementById('editVehicleModel').value);
            formData.append('year', document.getElementById('editVehicleYear').value);
            formData.append('license_plate', document.getElementById('editLicensePlate').value);
            formData.append('color', document.getElementById('editVehicleColor').value);
            formData.append('vehicle_type', document.getElementById('editVehicleType').value);
            
            // Add photo files only if new ones are selected
            const frontPhoto = document.getElementById('editFrontPhoto').files[0];
            const backPhoto = document.getElementById('editBackPhoto').files[0];
            const sidePhoto = document.getElementById('editSidePhoto').files[0];
            
            if (frontPhoto) formData.append('front_photo', frontPhoto);
            if (backPhoto) formData.append('back_photo', backPhoto);
            if (sidePhoto) formData.append('side_photo', sidePhoto);
            
            // Submit to backend
            fetch('<?php echo URL_ROOT; ?>/VehicleController/update', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text(); // Get as text first to debug
            })
            .then(text => {
                console.log('Raw response:', text);
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        alert(data.message);
                        closeEditVehicleModal();
                        // Refresh vehicle list and stats
                        loadVehicleList();
                        updateVehicleStats();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    alert('Server response was not valid JSON: ' + text);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the vehicle.');
            });
        });
        
        // Load vehicle list
        function loadVehicleList() {
            fetch('<?php echo URL_ROOT; ?>/VehicleController/getMyVehicles')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateVehicleTable(data.vehicles);
                } else {
                    console.error('Error loading vehicles:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        // Update vehicle table
        function updateVehicleTable(vehicles) {
            const tbody = document.querySelector('#vehicles table tbody');
            if (!tbody) return;
            
            tbody.innerHTML = '';
            
            vehicles.forEach(vehicle => {
                const statusClass = vehicle.verification_status === 'approved' ? 'status-active' : 
                                  vehicle.verification_status === 'rejected' ? 'status-cancelled' : 'status-pending';
                const statusText = vehicle.verification_status === 'approved' ? 'Verified' : 
                                 vehicle.verification_status === 'rejected' ? 'Rejected' : 'Pending Verification';
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${vehicle.make} ${vehicle.model} ${vehicle.year}</td>
                    <td>${vehicle.license_plate}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="editVehicle(${vehicle.id})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteVehicle(${vehicle.id})">Remove</button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        // Update vehicle stats
        function updateVehicleStats() {
            fetch('<?php echo URL_ROOT; ?>/VehicleController/getStats')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update dashboard stats
                    const approvedVehiclesElement = document.getElementById('approvedVehiclesCount');
                    if (approvedVehiclesElement) {
                        approvedVehiclesElement.textContent = data.stats.approved_vehicles || '0';
                    }
                    
                    // Update vehicles tab stats
                    const totalVehiclesElement = document.getElementById('totalVehiclesCount');
                    const approvedVehiclesTabElement = document.getElementById('approvedVehiclesCountTab');
                    const pendingVehiclesElement = document.getElementById('pendingVehiclesCount');
                    
                    if (totalVehiclesElement) {
                        totalVehiclesElement.textContent = data.stats.total_vehicles || '0';
                    }
                    if (approvedVehiclesTabElement) {
                        approvedVehiclesTabElement.textContent = data.stats.approved_vehicles || '0';
                    }
                    if (pendingVehiclesElement) {
                        pendingVehiclesElement.textContent = data.stats.pending_vehicles || '0';
                    }
                }
            })
            .catch(error => {
                console.error('Error updating stats:', error);
            });
        }
        
        // Edit vehicle function
        function editVehicle(vehicleId) {
            console.log('Editing vehicle ID:', vehicleId);
            // Fetch vehicle details
            fetch(`<?php echo URL_ROOT; ?>/VehicleController/getDetails/${vehicleId}`)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success && data.vehicle) {
                    const vehicle = data.vehicle;
                    
                    // Pre-fill form with current data
                    document.getElementById('editVehicleId').value = vehicle.id;
                    document.getElementById('editVehicleMake').value = vehicle.make;
                    document.getElementById('editVehicleModel').value = vehicle.model;
                    document.getElementById('editVehicleYear').value = vehicle.year;
                    document.getElementById('editLicensePlate').value = vehicle.license_plate;
                    document.getElementById('editVehicleColor').value = vehicle.color;
                    document.getElementById('editVehicleType').value = vehicle.vehicle_type;
                    
                    // Helper function to get correct photo URL
                    function getPhotoUrl(photoPath) {
                        if (!photoPath) return '';
                        
                        console.log('Processing photo path:', photoPath);
                        
                        // Remove 'public/' prefix if it exists
                        let cleanPath = photoPath.startsWith('public/') ? photoPath.substring(7) : photoPath;
                        
                        // Ensure path starts with uploads/vehicles/
                        if (!cleanPath.startsWith('uploads/vehicles/')) {
                            cleanPath = 'uploads/vehicles/' + cleanPath.replace(/^.*\//, '');
                        }
                        
                        const finalUrl = `<?php echo URL_ROOT; ?>/${cleanPath}`;
                        console.log('Final photo URL:', finalUrl);
                        return finalUrl;
                    }
                    
                    // Show existing photos if available
                    if (vehicle.front_photo) {
                        const frontPhotoUrl = getPhotoUrl(vehicle.front_photo);
                        console.log('Front photo - Original:', vehicle.front_photo, 'URL:', frontPhotoUrl);
                        document.getElementById('editFrontPhotoPreview').innerHTML = 
                            `<img src="${frontPhotoUrl}" alt="Front view" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;" 
                             onload="console.log('Front photo loaded successfully');" 
                             onerror="console.log('Failed to load front photo:', this.src); this.parentElement.innerHTML='<i class=\\'fas fa-car\\'></i><br><small>Current photo<br>failed to load</small>';">`;
                    }
                    if (vehicle.back_photo) {
                        const backPhotoUrl = getPhotoUrl(vehicle.back_photo);
                        console.log('Back photo - Original:', vehicle.back_photo, 'URL:', backPhotoUrl);
                        document.getElementById('editBackPhotoPreview').innerHTML = 
                            `<img src="${backPhotoUrl}" alt="Back view" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;" 
                             onload="console.log('Back photo loaded successfully');" 
                             onerror="console.log('Failed to load back photo:', this.src); this.parentElement.innerHTML='<i class=\\'fas fa-car\\'></i><br><small>Current photo<br>failed to load</small>';">`;
                    }
                    if (vehicle.side_photo) {
                        const sidePhotoUrl = getPhotoUrl(vehicle.side_photo);
                        console.log('Side photo - Original:', vehicle.side_photo, 'URL:', sidePhotoUrl);
                        document.getElementById('editSidePhotoPreview').innerHTML = 
                            `<img src="${sidePhotoUrl}" alt="Side view" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;" 
                             onload="console.log('Side photo loaded successfully');" 
                             onerror="console.log('Failed to load side photo:', this.src); this.parentElement.innerHTML='<i class=\\'fas fa-car\\'></i><br><small>Current photo<br>failed to load</small>';">`;
                    }
                    
                    // Show modal
                    showEditVehicleModal();
                } else {
                    alert('Error loading vehicle details: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading vehicle details');
            });
        }
        
        // Delete vehicle function
        function deleteVehicle(vehicleId) {
            if (confirm('Are you sure you want to delete this vehicle? This action cannot be undone.')) {
                fetch(`<?php echo URL_ROOT; ?>/VehicleController/delete/${vehicleId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        loadVehicleList();
                        updateVehicleStats();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the vehicle.');
                });
            }
        }
        
        // Load vehicles when vehicles tab is opened
        document.querySelector('[data-tab="vehicles"]').addEventListener('click', function() {
            setTimeout(() => {
                loadVehicleList();
                updateVehicleStats();
            }, 100);
        });
        
        // Load initial data when page loads
        document.addEventListener('DOMContentLoaded', function() {
            updateVehicleStats();
        });
        // New Help Modal Functions
        function showNewHelpModal() {
            document.getElementById('newHelpModal').style.display = 'block';
        }
        function closeNewHelpModal() {
            document.getElementById('newHelpModal').style.display = 'none';
            document.getElementById('newHelpForm').reset();
        }
        // New Complaint Modal Functions
        function showNewComplaintModal() {
            document.getElementById('newComplaintModal').style.display = 'block';
        }
        function closeNewComplaintModal() {
            document.getElementById('newComplaintModal').style.display = 'none';
            document.getElementById('newComplaintForm').reset();
        }
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const addVehicleModal = document.getElementById('addVehicleModal');
            const newHelpModal = document.getElementById('newHelpModal');
            const newComplaintModal = document.getElementById('newComplaintModal');
            
            if (event.target === addVehicleModal) {
                closeAddVehicleModal();
            }
            if (event.target === newHelpModal) {
                closeNewHelpModal();
            }
            if (event.target === newComplaintModal) {
                closeNewComplaintModal();
            }
        });
        // Handle form submissions
        document.getElementById('newHelpForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const helpData = {
                subject: document.getElementById('helpSubject').value,
                message: document.getElementById('helpMessage').value
            };
            alert(`Help message sent successfully!\nSubject: ${helpData.subject}`);
            closeNewHelpModal();
        });
        
        document.getElementById('newComplaintForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const complaintData = {
                subject: document.getElementById('complaintSubject').value,
                message: document.getElementById('complaintMessage').value,
                type: document.getElementById('complaintType').value
            };
            alert(`Complaint submitted successfully!\nSubject: ${complaintData.subject}\nType: ${complaintData.type}`);
            closeNewComplaintModal();
        });
        // Help Chat Functionality
        const helpChatList = document.getElementById('helpChatList');
        const helpChatMessages = document.getElementById('helpChatMessages');
        const helpMessageInput = document.getElementById('helpMessageInput');
        const helpSendButton = document.getElementById('helpSendButton');
        const helpChatHeaderInfo = document.querySelector('.help-chat-header-info');
        
        // Load help chat messages for selected item
        function loadHelpChat(helpId) {
            const helpTopics = {
                payment: {
                    name: "Payment Issue",
                    status: "Replied",
                    messages: [
                        { sender: "driver", text: "Hi, I'm having trouble with my itinerary booking. The payment went through but I didn't receive confirmation.", time: "2 hours ago" },
                        { sender: "admin", text: "Hello! I see your payment was processed. Let me check your booking status right away.", time: "1 hour ago" },
                        { sender: "admin", text: "I found the issue - there was a small delay in our system. Your confirmation email has been sent now.", time: "1 hour ago" }
                    ]
                },
                profile: {
                    name: "Profile Update",
                    status: "Pending",
                    messages: [
                        { sender: "driver", text: "Need help updating my driver profile information and certification documents.", time: "5 hours ago" }
                    ]
                }
            };
            
            const help = helpTopics[helpId];
            if (!help) return;
            
            // Update header
            helpChatHeaderInfo.querySelector('h3').textContent = help.name;
            helpChatHeaderInfo.querySelector('p').textContent = `Help Message - ${help.status}`;
            
            // Clear and load messages
            helpChatMessages.innerHTML = '';
            help.messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `help-message ${msg.sender}`;
                messageDiv.innerHTML = `
                    ${msg.text}
                    <div class="help-message-time">${msg.time}</div>
                `;
                helpChatMessages.appendChild(messageDiv);
            });
            
            // Scroll to bottom
            helpChatMessages.scrollTop = helpChatMessages.scrollHeight;
        }
        
        // Initialize with payment issue chat
        loadHelpChat('payment');
        
        // Help chat item selection
        if (helpChatList) {
            helpChatList.addEventListener('click', function(e) {
                const chatItem = e.target.closest('.help-chat-item');
                if (chatItem) {
                    // Update active state
                    document.querySelectorAll('.help-chat-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    chatItem.classList.add('active');
                    const helpId = chatItem.getAttribute('data-help');
                    loadHelpChat(helpId);
                }
            });
        }
        
        // Send help message
        function sendHelpMessage() {
            const messageText = helpMessageInput.value.trim();
            if (!messageText) return;
            const activeChat = document.querySelector('.help-chat-item.active');
            if (!activeChat) return;
            const helpId = activeChat.getAttribute('data-help');
            const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            // Add message to UI
            const messageDiv = document.createElement('div');
            messageDiv.className = 'help-message driver';
            messageDiv.innerHTML = `
                ${messageText}
                <div class="help-message-time">${currentTime}</div>
            `;
            helpChatMessages.appendChild(messageDiv);
            
            // Clear input and scroll to bottom
            helpMessageInput.value = '';
            helpChatMessages.scrollTop = helpChatMessages.scrollHeight;
        }
        
        if (helpSendButton) {
            helpSendButton.addEventListener('click', sendHelpMessage);
        }
        if (helpMessageInput) {
            helpMessageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendHelpMessage();
                }
            });
        }
        
        // Complaint Chat Functionality
        const complaintChatList = document.getElementById('complaintChatList');
        const complaintChatMessages = document.getElementById('complaintChatMessages');
        const complaintMessageInput = document.getElementById('complaintMessageInput');
        const complaintSendButton = document.getElementById('complaintSendButton');
        const complaintChatHeaderInfo = document.querySelector('.help-chat-header-info');
        
        // Load complaint chat messages for selected item
        function loadComplaintChat(complaintId) {
            const complaintTopics = {
                traveller: {
                    name: "Traveller Behavior",
                    status: "Investigated",
                    messages: [
                        { sender: "driver", text: "Traveller was disrespectful and damaged my equipment during the tour.", time: "1 day ago" },
                        { sender: "admin", text: "Thank you for reporting this. We take such incidents seriously and will investigate immediately.", time: "12 hours ago" },
                        { sender: "admin", text: "The traveller has been warned and your equipment damage will be compensated. We apologize for the inconvenience.", time: "6 hours ago" }
                    ]
                }
            };
            
            const complaint = complaintTopics[complaintId];
            if (!complaint) return;
            
            // Update header
            complaintChatHeaderInfo.querySelector('h3').textContent = complaint.name;
            complaintChatHeaderInfo.querySelector('p').textContent = `Complaint - ${complaint.status}`;
            
            // Clear and load messages
            complaintChatMessages.innerHTML = '';
            complaint.messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `help-message ${msg.sender}`;
                messageDiv.innerHTML = `
                    ${msg.text}
                    <div class="help-message-time">${msg.time}</div>
                `;
                complaintChatMessages.appendChild(messageDiv);
            });
            
            // Scroll to bottom
            complaintChatMessages.scrollTop = complaintChatMessages.scrollHeight;
        }
        
        // Initialize with traveller behavior complaint
        loadComplaintChat('traveller');
        
        // Complaint chat item selection
        if (complaintChatList) {
            complaintChatList.addEventListener('click', function(e) {
                const chatItem = e.target.closest('.help-chat-item');
                if (chatItem) {
                    // Update active state
                    document.querySelectorAll('.help-chat-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    chatItem.classList.add('active');
                    const complaintId = chatItem.getAttribute('data-complaint');
                    loadComplaintChat(complaintId);
                }
            });
        }
        
        // Send complaint message
        function sendComplaintMessage() {
            const messageText = complaintMessageInput.value.trim();
            if (!messageText) return;
            const activeChat = document.querySelector('.help-chat-item.active');
            if (!activeChat) return;
            const complaintId = activeChat.getAttribute('data-complaint');
            const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            // Add message to UI
            const messageDiv = document.createElement('div');
            messageDiv.className = 'help-message driver';
            messageDiv.innerHTML = `
                ${messageText}
                <div class="help-message-time">${currentTime}</div>
            `;
            complaintChatMessages.appendChild(messageDiv);
            
            // Clear input and scroll to bottom
            complaintMessageInput.value = '';
            complaintChatMessages.scrollTop = complaintChatMessages.scrollHeight;
        }
        
        if (complaintSendButton) {
            complaintSendButton.addEventListener('click', sendComplaintMessage);
        }
        if (complaintMessageInput) {
            complaintMessageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendComplaintMessage();
                }
            });
        }
        // Messaging Functionality
        const travelerChatList = document.getElementById('travelerChatList');
        const travelerChatMessages = document.getElementById('travelerChatMessages');
        const travelerMessageInput = document.getElementById('travelerMessageInput');
        const travelerSendButton = document.getElementById('travelerSendButton');
        const travelerChatHeaderInfo = document.querySelector('.chat-header-info');
        
        // Mock traveler chat data
        const travelerChatData = {
            john: {
                name: "John Doe",
                itinerary: "ITN-001",
                status: "Online",
                messages: [
                    { sender: "user", text: "Hi David! Thanks for the amazing tour today!", time: "10 minutes ago" },
                    { sender: "driver", text: "You're welcome, John! It was my pleasure to show you around Paris.", time: "8 minutes ago" },
                    { sender: "user", text: "I'll definitely recommend your services to my friends!", time: "5 minutes ago" }
                ]
            },
            jane: {
                name: "Jane Smith",
                itinerary: "ITN-002",
                status: "Last seen 2h ago",
                messages: [
                    { sender: "user", text: "Hi David, when will you arrive for our pickup?", time: "2 hours ago" },
                    { sender: "driver", text: "Hi Jane! I'll be there in about 15 minutes.", time: "2 hours ago" }
                ]
            },
            mike: {
                name: "Mike Johnson",
                itinerary: "ITN-003",
                status: "Last seen 1d ago",
                messages: [
                    { sender: "user", text: "David, can we extend our tour by 2 more hours?", time: "1 day ago" },
                    { sender: "driver", text: "Hi Mike! Yes, that's possible. There will be an additional charge of $100 for the extra time.", time: "1 day ago" },
                    { sender: "user", text: "That works for me. Let's do it!", time: "1 day ago" }
                ]
            }
        };
        
        // Load traveler messages for selected user
        function loadTravelerMessages(travelerId) {
            const traveler = travelerChatData[travelerId];
            if (!traveler) return;
            
            // Update header
            travelerChatHeaderInfo.querySelector('h3').textContent = traveler.name;
            travelerChatHeaderInfo.querySelector('p').textContent = `${traveler.itinerary} - ${traveler.status}`;
            
            // Clear and load messages
            travelerChatMessages.innerHTML = '';
            traveler.messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${msg.sender}`;
                messageDiv.innerHTML = `
                    ${msg.text}
                    <div class="message-time">${msg.time}</div>
                `;
                travelerChatMessages.appendChild(messageDiv);
            });
            
            // Scroll to bottom
            travelerChatMessages.scrollTop = travelerChatMessages.scrollHeight;
        }
        
        // Initialize with John's chat
        loadTravelerMessages('john');
        
        // Traveler chat item selection
        if (travelerChatList) {
            travelerChatList.addEventListener('click', function(e) {
                const chatItem = e.target.closest('.chat-item');
                if (chatItem) {
                    // Update active state
                    document.querySelectorAll('.chat-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    chatItem.classList.add('active');
                    const travelerId = chatItem.getAttribute('data-traveler');
                    loadTravelerMessages(travelerId);
                }
            });
        }
        
        // Send traveler message
        function sendTravelerMessage() {
            const messageText = travelerMessageInput.value.trim();
            if (!messageText) return;
            const activeChat = document.querySelector('.chat-item.active');
            if (!activeChat) return;
            const travelerId = activeChat.getAttribute('data-traveler');
            const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            
            // Add message to chat data
            travelerChatData[travelerId].messages.push({
                sender: "driver",
                text: messageText,
                time: currentTime
            });
            
            // Add message to UI
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message driver';
            messageDiv.innerHTML = `
                ${messageText}
                <div class="message-time">${currentTime}</div>
            `;
            travelerChatMessages.appendChild(messageDiv);
            
            // Clear input and scroll to bottom
            travelerMessageInput.value = '';
            travelerChatMessages.scrollTop = travelerChatMessages.scrollHeight;
        }
        
        if (travelerSendButton) {
            travelerSendButton.addEventListener('click', sendTravelerMessage);
        }
        if (travelerMessageInput) {
            travelerMessageInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendTravelerMessage();
                }
            });
        }
        
        // Profile photo upload simulation
        document.getElementById('profilePhoto').addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                alert('Profile photo uploaded successfully!');
            }
        });
        
        document.getElementById('tripPhotos').addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                alert(`${e.target.files.length} trip photos uploaded successfully!`);
            }
        });
    </script>
</body>
</html>

