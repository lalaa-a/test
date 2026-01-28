<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide Dashboard</title>
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
        .stat-icon.locations {
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
        /* Location List */
        .location-list {
            margin-top: 15px;
        }
        .location-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .location-item:last-child {
            border-bottom: none;
        }
        .location-info {
            font-size: 0.95rem;
        }
        .location-status {
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
        .message.guide {
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
        /* Location Photos Grid */
        .location-photos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        .location-photo-item {
            text-align: center;
        }
        .location-photo-preview {
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
        .location-photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .location-photo-preview i {
            color: #888;
            font-size: 2rem;
        }
        .location-photo-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
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
        .help-message.guide {
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
            max-width: 600px;
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
            <li><a href="#" data-tab="locations"><i class="fas fa-map-marker-alt"></i> <span>Guiding Locations</span></a></li>
            <li><a href="#" data-tab="visits"><i class="fas fa-calendar-check"></i> <span>Visits</span></a></li>
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
            <h1>Guide Dashboard</h1>
            <div class="user-info">
                <?php
                $user = getLoggedInUser();
                $firstInitial = !empty($user['fullname']) ? strtoupper(substr($user['fullname'], 0, 1)) : 'G';
                $profilePhoto = $user['profile_photo'] ?? null;
                ?>
                <div class="user-avatar">
                    <?php if (!empty($profilePhoto) && file_exists(ROOT_PATH.'/public/'.$user['profile_photo'])): ?>
                        <img src="<?=URL_ROOT.'/public/'.$user['profile_photo']?>" alt="Profile Photo">
                    <?php else: ?>
                        <?= $firstInitial ?>
                    <?php endif; ?>
                </div>
                <span><?= htmlspecialchars($user['fullname'] ?? 'Guide User') ?></span>
                <button class="logout-btn" onclick="window.location.href='<?php echo URL_ROOT; ?>/user/logout'">Logout</button>
            </div>
        </div>
        <!-- Dashboard Content -->
        <div class="dashboard-content active" id="dashboard">
            <!-- Welcome Box -->
            <div class="welcome-box">
                <?php
                $user = getLoggedInUser();
                $firstInitial = !empty($user['fullname']) ? strtoupper(substr($user['fullname'], 0, 1)) : 'G';
                $firstName = !empty($user['fullname']) ? explode(' ', $user['fullname'])[0] : 'Guide';
                ?>
                <div class="welcome-avatar"><?= $firstInitial ?></div>
                <div class="welcome-info">
                    <h3>Welcome back, <?= htmlspecialchars($firstName) ?>!</h3>
                    <p><i class="fas fa-star"></i> 4.9 Rating • <span class="status-badge verified">Verified</span></p>
                    <div class="welcome-status">
                        <div class="status-item">1 ongoing visit</div>
                        <div class="status-item">3 upcoming visits</div>
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
                    <div class="stat-number">89</div>
                    <div class="stat-label">Completed Visits</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon rating">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-number">4.9</div>
                    <div class="stat-label">Average Rating</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon earnings">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-number">Rs. 1,312,500</div>
                    <div class="stat-label">Total Earnings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon locations">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="stat-number" id="dashboardLocationsCount"><?php echo isset($location_count) ? $location_count : '0'; ?></div>
                    <div class="stat-label">Guiding Locations</div>
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
                <h3>Guiding Locations</h3>
                <div class="location-list" id="dashboardLocationsList">
                    <?php if (isset($locations) && !empty($locations)): ?>
                        <?php foreach ($locations as $location): ?>
                            <div class="location-item" data-location-id="<?php echo $location->id; ?>">
                                <div class="location-info">
                                    <strong><?php echo htmlspecialchars($location->location_name); ?></strong><br>
                                    <?php echo htmlspecialchars($location->city); ?> • <?php echo $location->visit_hours; ?> hours
                                </div>
                                <span class="location-status verified"><?php echo $location->visit_hours; ?>h</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="location-item" id="noDashboardLocations">
                            <div class="location-info" style="text-align: center; color: #666; font-style: italic;">
                                No locations added yet. Click "Add New Location" to get started.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Current Visit -->
            <div class="card">
                <div class="card-header">
                    <h2>Current Visit</h2>
                </div>
                <div class="trip-info">
                    <h3>Visit to Eiffel Tower</h3>
                    <p><strong>Traveller:</strong> John Doe</p>
                    <div class="trip-details">
                        <div class="trip-detail-item">
                            <strong>Location:</strong><br>
                            galle fort
                        </div>
                        <div class="trip-detail-item">
                            <strong>Duration:</strong><br>
                            3 hours
                        </div>
                    </div>
                    <button class="btn btn-warning" style="margin-top: 20px;">Ongoing</button>
                </div>
            </div>
            <!-- Upcoming Visits -->
            <div class="card">
                <div class="card-header">
                    <h2>Upcoming Visits</h2>
                    <button class="btn btn-info">View All</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Traveller</th>
                            <th>Location</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2024-01-20</td>
                            <td>10:00 AM</td>
                            <td>Jane Smith</td>
                            <td>Louvre Museum</td>
                            <td>4 hours</td>
                        </tr>
                        <tr>
                            <td>2024-01-22</td>
                            <td>02:00 PM</td>
                            <td>Mike Johnson</td>
                            <td>Montmartre</td>
                            <td>3 hours</td>
                        </tr>
                        <tr>
                            <td>2024-01-25</td>
                            <td>09:00 AM</td>
                            <td>Sarah Wilson</td>
                            <td>Versailles Palace</td>
                            <td>6 hours</td>
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
                            <div class="request-title">Eiffel Tower Tour</div>
                            <div class="request-time">2 hours ago</div>
                        </div>
                        <div class="request-content">
                            John Doe requests a 3-hour guided tour of the Eiffel Tower with historical insights.
                        </div>
                        <div class="request-actions">
                            <button class="btn btn-success btn-sm">Accept</button>
                            <button class="btn btn-danger btn-sm">Decline</button>
                            <button class="btn btn-primary btn-sm">Details</button>
                        </div>
                    </div>
                    <div class="request-card">
                        <div class="request-header">
                            <div class="request-title">Louvre Museum Visit</div>
                            <div class="request-time">5 hours ago</div>
                        </div>
                        <div class="request-content">
                            Jane Smith needs a guided tour of the Louvre Museum focusing on Renaissance art.
                        </div>
                        <div class="request-actions">
                            <button class="btn btn-success btn-sm">Accept</button>
                            <button class="btn btn-danger btn-sm">Decline</button>
                            <button class="btn btn-primary btn-sm">Details</button>
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
                                    <div class="help-chat-item-preview">Need help updating guide profile information</div>
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
                                <div class="help-message guide">
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
                                <div class="help-message guide">
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
        <div class="dashboard-content" id="locations">
            <div class="card">
                <div class="card-header">
                    <h2>My Guiding Locations</h2>
                    <button class="btn btn-primary" onclick="showAddLocationModal()">Add New Location</button>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon locations">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="stat-number" id="totalLocationsCount"><?php echo isset($location_count) ? $location_count : '0'; ?></div>
                        <div class="stat-label">Total Locations</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon trips">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number">89</div>
                        <div class="stat-label">Visits Completed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon earnings">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-number">Rs. 930,000</div>
                        <div class="stat-label">Earnings from Locations</div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>City</th>
                            <th>Visit Hours</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="locationsTableBody">
                        <?php if (isset($locations) && !empty($locations)): ?>
                            <?php foreach ($locations as $location): ?>
                                <tr data-location-id="<?php echo $location->id; ?>">
                                    <td><?php echo htmlspecialchars($location->location_name); ?></td>
                                    <td><?php echo htmlspecialchars($location->city); ?></td>
                                    <td><?php echo $location->visit_hours; ?> hours</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="editLocation(<?php echo $location->id; ?>)">Edit</button>
                                        <button class="btn btn-danger btn-sm" onclick="deleteLocation(<?php echo $location->id; ?>)">Remove</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="noLocationsRow">
                                <td colspan="4" style="text-align: center; color: #666; padding: 40px;">
                                    No locations added yet. Click "Add New Location" to get started.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="dashboard-content" id="visits">
            <div class="card">
                <div class="card-header">
                    <h2>Visits Management</h2>
                </div>
                <div class="tabs">
                    <button class="tab active" data-tab="today">Today</button>
                    <button class="tab" data-tab="upcoming">Upcoming</button>
                    <button class="tab" data-tab="completed">Completed</button>
                    <button class="tab" data-tab="cancelled">Cancelled</button>
                </div>
                <div class="tab-content active" id="today-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Traveller</th>
                                <th>Location</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-01-18</td>
                                <td>10:00 AM</td>
                                <td>John Doe</td>
                                <td>Eiffel Tower</td>
                                <td>3 hours</td>
                                <td>
                                    <button class="btn btn-warning btn-sm">Ongoing</button>
                                    <button class="btn btn-success btn-sm">Mark Complete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-content" id="upcoming-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Traveller</th>
                                <th>Location</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-01-20</td>
                                <td>10:00 AM</td>
                                <td>Jane Smith</td>
                                <td>Louvre Museum</td>
                                <td>4 hours</td>
                                <td>
                                    <button class="btn btn-primary btn-sm">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-01-22</td>
                                <td>02:00 PM</td>
                                <td>Mike Johnson</td>
                                <td>Montmartre</td>
                                <td>3 hours</td>
                                <td>
                                    <button class="btn btn-primary btn-sm">View</button>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-01-25</td>
                                <td>09:00 AM</td>
                                <td>Sarah Wilson</td>
                                <td>Versailles Palace</td>
                                <td>6 hours</td>
                                <td>
                                    <button class="btn btn-primary btn-sm">View</button>
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
                                <th>Time</th>
                                <th>Traveller</th>
                                <th>Location</th>
                                <th>Duration</th>
                                <th>Earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-01-15</td>
                                <td>09:00 AM</td>
                                <td>Robert Davis</td>
                                <td>Notre Dame</td>
                                <td>2 hours</td>
                                <td>Rs. 45,000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-content" id="cancelled-content">
                    <p>No cancelled visits.</p>
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
                        <div class="stat-number">Rs. 1,312,500</div>
                        <div class="stat-label">Total Earnings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon trips">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number">Rs. 1,170,000</div>
                        <div class="stat-label">Completed Earnings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon rating">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number">Rs. 142,500</div>
                        <div class="stat-label">Pending Earnings</div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Visit</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2024-01-15</td>
                            <td>Notre Dame Tour</td>
                            <td>Rs. 45,000</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                        </tr>
                        <tr>
                            <td>2024-01-18</td>
                            <td>Eiffel Tower Tour</td>
                            <td>Rs. 67,500</td>
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
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Traveller</th>
                                <th>Location</th>
                                <th>Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-01-18</td>
                                <td>02:00 PM</td>
                                <td>Alex Thompson</td>
                                <td>Seine River Cruise</td>
                                <td>2 hours</td>
                                <td>
                                    <button class="btn btn-success btn-sm">Accept</button>
                                    <button class="btn btn-danger btn-sm">Decline</button>
                                    <button class="btn btn-primary btn-sm">Details</button>
                                </td>
                            </tr>
                            <tr>
                                <td>2024-01-17</td>
                                <td>11:00 AM</td>
                                <td>Emma Wilson</td>
                                <td>Latin Quarter</td>
                                <td>3 hours</td>
                                <td>
                                    <button class="btn btn-success btn-sm">Accept</button>
                                    <button class="btn btn-danger btn-sm">Decline</button>
                                    <button class="btn btn-primary btn-sm">Details</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-content" id="accepted-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Traveller</th>
                                <th>Location</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-01-20</td>
                                <td>10:00 AM</td>
                                <td>Jane Smith</td>
                                <td>Louvre Museum</td>
                                <td>4 hours</td>
                                <td><span class="status-badge status-active">Confirmed</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-content" id="rejected-content">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Traveller</th>
                                <th>Location</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2024-01-16</td>
                                <td>03:00 PM</td>
                                <td>David Brown</td>
                                <td>Champs-Élysées</td>
                                <td>2 hours</td>
                                <td><span class="status-badge status-cancelled">Rejected</span></td>
                            </tr>
                        </tbody>
                    </table>
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
                                <div class="chat-item-preview">When will we meet?</div>
                            </div>
                        </div>
                    </div>
                    <div class="chat-main">
                        <div class="chat-header">
                            <div class="chat-avatar">J</div>
                            <div class="chat-header-info">
                                <h3>John Doe</h3>
                                <p>Eiffel Tower Visit - Online</p>
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
                            <div class="image-preview">G</div>
                            <div class="image-label">Profile Photo</div>
                            <input type="file" id="profilePhoto" accept="image/*">
                            <button class="upload-btn" onclick="document.getElementById('profilePhoto').click()">Upload Photo</button>
                        </div>
                        <div class="image-upload">
                            <div class="image-label">Visit Photos</div>
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
                            <input type="text" id="fullName" value="Sarah Wilson">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" value="sarah@example.com">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" value="+94782498755">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" value="galle">
                        </div>
                        <div class="form-group">
                            <label for="experience">Guiding Experience</label>
                            <input type="text" id="experience" value="5 years of professional guiding experience in galle">
                        </div>
                        <div class="form-group">
                            <label for="languages">Languages Spoken</label>
                            <input type="text" id="languages" value="English, French, Spanish">
                        </div>
                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" rows="3">Certified tour guide with extensive knowledge of galle history and culture. Passionate about providing memorable experiences for travelers from around the world.</textarea>
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
    <!-- Add Location Modal -->
    <div id="addLocationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Guiding Location</h2>
                <span class="close" onclick="closeAddLocationModal()">&times;</span>
            </div>
            <form id="addLocationForm">
                <div class="form-group">
                    <label for="locationName">Location Name</label>
                    <input type="text" id="locationName" placeholder="e.g., galle fort" required>
                </div>
                <div class="form-group">
                    <label for="locationCity">City</label>
                    <input type="text" id="locationCity" placeholder="e.g., Galle" required>
                </div>
                <div class="form-group">
                    <label for="visitHours">Visit Duration (Hours)</label>
                    <input type="number" id="visitHours" placeholder="e.g., 2.5" min="0.5" max="24" step="0.5" value="2.0" required>
                    <small style="color: #666; font-size: 0.85rem;">How many hours does it take to visit this location?</small>
                </div>
                <div class="form-group">
                    <label for="locationRate">Rate per Hour Rs.</label>
                    <input type="number" id="locationRate" placeholder="e.g., 25.00" min="1" step="0.01" required>
                    <small style="color: #666; font-size: 0.85rem;">Your hourly rate for guiding this location</small>
                </div>
                <div class="form-group">
                    <label for="locationDescription">Description</label>
                    <textarea id="locationDescription" rows="3" placeholder="Brief description of the location and what you offer"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-danger" onclick="closeAddLocationModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Location</button>
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
    <!-- Edit Location Modal -->
    <div id="editLocationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Guiding Location</h2>
                <span class="close" onclick="closeEditLocationModal()">&times;</span>
            </div>
            <form id="editLocationForm">
                <input type="hidden" id="editLocationId">
                <div class="form-group">
                    <label for="editLocationName">Location Name</label>
                    <input type="text" id="editLocationName" placeholder="e.g., galle fort" required>
                </div>
                <div class="form-group">
                    <label for="editLocationCity">City</label>
                    <input type="text" id="editLocationCity" placeholder="e.g., galle" required>
                </div>
                <div class="form-group">
                    <label for="editVisitHours">Visit Duration (Hours)</label>
                    <input type="number" id="editVisitHours" placeholder="e.g., 2.5" min="0.5" max="24" step="0.5" value="2.0" required>
                    <small style="color: #666; font-size: 0.85rem;">How many hours does it take to visit this location?</small>
                </div>
                <div class="form-group">
                    <label for="editLocationRate">Rate per Hour ($)</label>
                    <input type="number" id="editLocationRate" placeholder="e.g., 25.00" min="1" step="0.01" required>
                    <small style="color: #666; font-size: 0.85rem;">Your hourly rate for guiding this location</small>
                </div>
                <div class="form-group">
                    <label for="editLocationDescription">Description</label>
                    <textarea id="editLocationDescription" rows="3" placeholder="Brief description of the location and what you offer"></textarea>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-danger" onclick="closeEditLocationModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Location</button>
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
                    'dashboard': 'Guide Dashboard',
                    'locations': 'Guiding Locations',
                    'visits': 'Visits Management',
                    'earnings': 'Earnings',
                    'requests': 'Travel Requests',
                    'messaging': 'Messaging',
                    'help-portal': 'Help Portal',
                    'profile': 'Profile Settings'
                };
                headerTitle.textContent = titles[tabId];
            });
        });
        // Visit Tabs
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
        // Add Location Modal Functions
        function showAddLocationModal() {
            document.getElementById('addLocationModal').style.display = 'block';
        }
        function closeAddLocationModal() {
            document.getElementById('addLocationModal').style.display = 'none';
            document.getElementById('addLocationForm').reset();
        }
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
        
        // Edit Location Modal Functions
        function showEditLocationModal(locationData) {
            document.getElementById('editLocationId').value = locationData.id;
            document.getElementById('editLocationName').value = locationData.location_name;
            document.getElementById('editLocationCity').value = locationData.city;
            document.getElementById('editVisitHours').value = locationData.visit_hours;
            document.getElementById('editLocationRate').value = locationData.rate_per_hour || '';
            document.getElementById('editLocationDescription').value = locationData.description || '';
            document.getElementById('editLocationModal').style.display = 'block';
        }
        function closeEditLocationModal() {
            document.getElementById('editLocationModal').style.display = 'none';
            document.getElementById('editLocationForm').reset();
        }
        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const addLocationModal = document.getElementById('addLocationModal');
            const editLocationModal = document.getElementById('editLocationModal');
            const newHelpModal = document.getElementById('newHelpModal');
            const newComplaintModal = document.getElementById('newComplaintModal');
            
            if (event.target === addLocationModal) {
                closeAddLocationModal();
            }
            if (event.target === editLocationModal) {
                closeEditLocationModal();
            }
            if (event.target === newHelpModal) {
                closeNewHelpModal();
            }
            if (event.target === newComplaintModal) {
                closeNewComplaintModal();
            }
        });
        
        // Helper functions for location management
        function updateLocationCount(newCount) {
            document.getElementById('totalLocationsCount').textContent = newCount;
            document.getElementById('dashboardLocationsCount').textContent = newCount;
        }
        
        function refreshDashboardLocationsList() {
            fetch('<?php echo URL_ROOT; ?>/GuideDashboard/getLocations')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const dashboardList = document.getElementById('dashboardLocationsList');
                        if (data.locations.length === 0) {
                            dashboardList.innerHTML = '<div class="location-item" id="noDashboardLocations"><div class="location-info" style="text-align: center; color: #666; font-style: italic;">No locations added yet. Click "Add New Location" to get started.</div></div>';
                        } else {
                            let html = '';
                            data.locations.forEach(location => {
                                html += `
                                    <div class="location-item" data-location-id="${location.id}">
                                        <div class="location-info">
                                            <strong>${escapeHtml(location.location_name)}</strong><br>
                                            ${escapeHtml(location.city)} • ${location.visit_hours} hours
                                        </div>
                                        <span class="location-status verified">${location.visit_hours}h</span>
                                    </div>
                                `;
                            });
                            dashboardList.innerHTML = html;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error refreshing dashboard locations:', error);
                });
        }
        
        function refreshLocationsTable() {
            fetch('<?php echo URL_ROOT; ?>/GuideDashboard/getLocations')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tableBody = document.getElementById('locationsTableBody');
                        if (data.locations.length === 0) {
                            tableBody.innerHTML = '<tr id="noLocationsRow"><td colspan="4" style="text-align: center; color: #666; padding: 40px;">No locations added yet. Click "Add New Location" to get started.</td></tr>';
                        } else {
                            let html = '';
                            data.locations.forEach(location => {
                                html += `
                                    <tr data-location-id="${location.id}">
                                        <td>${escapeHtml(location.location_name)}</td>
                                        <td>${escapeHtml(location.city)}</td>
                                        <td>${location.visit_hours} hours</td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" onclick="editLocation(${location.id})">Edit</button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteLocation(${location.id})">Remove</button>
                                        </td>
                                    </tr>
                                `;
                            });
                            tableBody.innerHTML = html;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error refreshing locations:', error);
                });
        }
        
        function deleteLocation(locationId) {
            if (!confirm('Are you sure you want to remove this location?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('location_id', locationId);
            
            fetch('<?php echo URL_ROOT; ?>/GuideDashboard/deleteLocation', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateLocationCount(data.location_count);
                    refreshLocationsTable();
                    refreshDashboardLocationsList();
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while deleting the location.', 'error');
            });
        }
        
        function editLocation(locationId) {
            // Fetch location details first
            fetch(`<?php echo URL_ROOT; ?>/GuideDashboard/getLocationDetails/${locationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showEditLocationModal(data.location);
                    } else {
                        showNotification(data.message || 'Failed to load location details', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading location details:', error);
                    showNotification('An error occurred while loading location details.', 'error');
                });
        }
        
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
                color: white;
                padding: 15px 25px;
                border-radius: 6px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                z-index: 3000;
                font-weight: 500;
                max-width: 350px;
                word-wrap: break-word;
                transform: translateX(400px);
                transition: transform 0.3s ease;
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Slide in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Remove after 4 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(400px)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 4000);
        }
        
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Handle form submissions
        document.getElementById('addLocationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('location_name', document.getElementById('locationName').value);
            formData.append('city', document.getElementById('locationCity').value);
            formData.append('visit_hours', document.getElementById('visitHours').value);
            formData.append('rate_per_hour', document.getElementById('locationRate').value);
            formData.append('description', document.getElementById('locationDescription').value);
            
            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Adding...';
            submitBtn.disabled = true;
            
            fetch('<?php echo URL_ROOT; ?>/GuideDashboard/addLocation', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update location count in dashboard
                    updateLocationCount(data.location_count);
                    
                    // Refresh locations table
                    refreshLocationsTable();
                    
                    // Refresh dashboard locations list
                    refreshDashboardLocationsList();
                    
                    // Show success message
                    showNotification(data.message, 'success');
                    closeAddLocationModal();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while adding the location.', 'error');
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
        
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
        
        // Edit Location Form Handler
        document.getElementById('editLocationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('location_id', document.getElementById('editLocationId').value);
            formData.append('location_name', document.getElementById('editLocationName').value);
            formData.append('city', document.getElementById('editLocationCity').value);
            formData.append('visit_hours', document.getElementById('editVisitHours').value);
            formData.append('rate_per_hour', document.getElementById('editLocationRate').value);
            formData.append('description', document.getElementById('editLocationDescription').value);
            
            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Updating...';
            submitBtn.disabled = true;
            
            fetch('<?php echo URL_ROOT; ?>/GuideDashboard/updateLocation', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh locations table and dashboard list
                    refreshLocationsTable();
                    refreshDashboardLocationsList();
                    
                    // Show success message
                    showNotification(data.message, 'success');
                    closeEditLocationModal();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating the location.', 'error');
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
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
                        { sender: "guide", text: "Hi, I'm having trouble with my itinerary booking. The payment went through but I didn't receive confirmation.", time: "2 hours ago" },
                        { sender: "admin", text: "Hello! I see your payment was processed. Let me check your booking status right away.", time: "1 hour ago" },
                        { sender: "admin", text: "I found the issue - there was a small delay in our system. Your confirmation email has been sent now.", time: "1 hour ago" }
                    ]
                },
                profile: {
                    name: "Profile Update",
                    status: "Pending",
                    messages: [
                        { sender: "guide", text: "Need help updating my guide profile information and certification documents.", time: "5 hours ago" }
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
            messageDiv.className = 'help-message guide';
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
                        { sender: "guide", text: "Traveller was disrespectful and damaged my equipment during the tour.", time: "1 day ago" },
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
            messageDiv.className = 'help-message guide';
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
                visit: "Eiffel Tower Visit",
                status: "Online",
                messages: [
                    { sender: "user", text: "Hi Sarah! Thanks for the amazing tour today!", time: "10 minutes ago" },
                    { sender: "guide", text: "You're welcome, John! It was my pleasure to show you around the Eiffel Tower.", time: "8 minutes ago" },
                    { sender: "user", text: "I'll definitely recommend your services to my friends!", time: "5 minutes ago" }
                ]
            },
            jane: {
                name: "Jane Smith",
                visit: "Louvre Museum Visit",
                status: "Last seen 2h ago",
                messages: [
                    { sender: "user", text: "Hi Sarah, when will we meet for our Louvre tour tomorrow?", time: "2 hours ago" },
                    { sender: "guide", text: "Hi Jane! We'll meet at 10 AM at the main entrance of the Louvre.", time: "2 hours ago" }
                ]
            }
        };
        
        // Load traveler messages for selected user
        function loadTravelerMessages(travelerId) {
            const traveler = travelerChatData[travelerId];
            if (!traveler) return;
            
            // Update header
            travelerChatHeaderInfo.querySelector('h3').textContent = traveler.name;
            travelerChatHeaderInfo.querySelector('p').textContent = `${traveler.visit} - ${traveler.status}`;
            
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
                sender: "guide",
                text: messageText,
                time: currentTime
            });
            
            // Add message to UI
            const messageDiv = document.createElement('div');
            messageDiv.className = 'message guide';
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
                alert(`${e.target.files.length} visit photos uploaded successfully!`);
            }
        });
    </script>
</body>
</html>

