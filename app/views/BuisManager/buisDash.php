<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Business Manager Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Match typography and feel of other dashboards (admin, driver, traveller) -->
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Geologica', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        :root {
            --primary: #006A71;
            --primary-hover: #9ACBD0;
            --secondary: #f5f7fb;
            --text-color: #212529;
            --sidebar-width: 222px;
            --header-height: 70px;
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
        /* Sidebar – same as other dashboards (white, primary teal highlight) */
        .sidebar {
            width: var(--sidebar-width);
            background: white;
            color: var(--text-color);
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            padding-bottom: 90px;
            z-index: 1000;
            border-right: 1px solid #e9ecef;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-header {
            display: flex;
            justify-content: center;
            padding: 0 20px;
            margin-bottom: 24px;
        }
        .sidebar-logo {
            width: 100px;
            height: 55px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .sidebar-menu {
            margin-top: 12px;
            list-style: none;
            padding: 0 12px;
        }
        .sidebar-menu li { margin-bottom: 8px; position: relative; }
        .sidebar-menu a {
            color: black;
            text-decoration: none;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            margin: 4px 0;
            position: relative;
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
        .sidebar-menu a i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            transition: all 0.3s ease;
            color: black;
        }
        .sidebar-menu a:hover i,
        .sidebar-menu a.active i {
            transform: scale(1.1) rotate(5deg);
            color: white;
        }
        .sidebar-menu a span { font-size: 0.95rem; font-weight: 500; transition: all 0.3s ease; }
        /* Sidebar User Section at Bottom */
        .sidebar-user-section {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 12px;
            border-top: 1px solid #e9ecef;
            background: white;
        }
        .sidebar-user-info {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            cursor: pointer;
        }
        .sidebar-user-info:hover {
            background: var(--primary-hover);
        }
        .sidebar-user-info:hover .sidebar-user-name,
        .sidebar-user-info:hover .sidebar-user-role {
            color: var(--primary);
        }
        .sidebar-user-avatar {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #008891 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1rem;
            transition: all 0.3s ease;
            overflow: hidden;
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
        }
        .sidebar-user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }
        .sidebar-dropdown-icon {
            color: var(--primary);
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .sidebar-user-info[aria-expanded="true"] .sidebar-dropdown-icon {
            transform: rotate(180deg);
        }
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
            background: var(--primary-hover);
            color: var(--primary);
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
        /* Content section: main heading + horizontal sub-tabs + date filters (primary teal) */
        .section-heading { font-size: 1.75rem; font-weight: 700; color: var(--primary); margin-bottom: 24px; }
        .content-tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 20px;
        }
        .content-tabs .tab {
            padding: 12px 24px;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-muted);
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: color 0.2s, border-color 0.2s;
        }
        .content-tabs .tab:hover { color: var(--primary); }
        .content-tabs .tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        .date-filters {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .date-filters .date-btn {
            padding: 10px 20px;
            border: 1px solid #e9ecef;
            background: #fff;
            color: var(--text-color);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s, color 0.2s, border-color 0.2s;
        }
        .date-filters .date-btn:hover {
            background: var(--primary-hover);
            border-color: var(--primary);
            color: var(--primary);
        }
        .date-filters .date-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }
        .date-filters .custom-date-range {
            margin-left: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .date-filters .custom-date-range input[type="date"] {
            padding: 6px 10px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        .commission-input { width: 72px; padding: 6px 8px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 0.9rem; }
        .section-panel { display: none; }
        .section-panel.active { display: block; }
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: calc(var(--header-height) + 20px) 20px 20px;
            overflow-y: auto;
            max-height: 100vh;
        }
        /* Header */
        .header {
            background: white;
            height: var(--header-height);
            width: calc(100% - var(--sidebar-width));
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            z-index: 900;
        }
        .header h1 {
            color: var(--primary);
            font-size: 1.4rem;
            font-weight: 300;
        }
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
            color: var(--primary);
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
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-refund {
            background: #e74c3c;
            color: white;
        }
        .btn-payout {
            background: #9b59b6;
            color: white;
        }
        .btn-commission {
            background: #3498db;
            color: white;
        }
        .btn-report {
            background: #1abc9c;
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
        .stat-icon.revenue {
            background: linear-gradient(135deg, #006A71, #005a5f);
        }
        .stat-icon.payouts {
            background: linear-gradient(135deg, #4CAF50, #45a049);
        }
        .stat-icon.refunds {
            background: linear-gradient(135deg, #f44336, #e53935);
        }
        .stat-icon.commissions {
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
        .status-refunded {
            background: #d1ecf1;
            color: #0c5460;
        }
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        /* Transaction Management: category tabs + filter bar (commercial/residential style) */
        .tx-bar {
            background: linear-gradient(135deg, #006A71, #005a5f);
            padding: 18px 24px;
            margin: -25px -25px 25px -25px;
            border-radius: 12px 12px 0 0;
        }
        .tx-category-row {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }
        .tx-category-row a,
        .tx-filter-row span {
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: color 0.2s, font-weight 0.2s;
        }
        .tx-category-row a:hover,
        .tx-filter-row span:hover {
            color: #fff;
        }
        .tx-category-row a.active,
        .tx-filter-row span.active {
            color: #fff;
            font-weight: 700;
        }
        .tx-filter-row {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .tx-filter-label {
            color: rgba(255,255,255,0.85);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .tx-section-card {
            display: none;
        }
        .tx-section-card.active {
            display: block;
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
        .table-toolbar { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .table-toolbar .search-input { padding: 10px 14px; border: 2px solid #e9ecef; border-radius: 8px; min-width: 200px; }
        .table-toolbar .date-from, .table-toolbar .date-to { padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; }
        .table-toolbar select { padding: 8px 12px; border: 2px solid #e9ecef; border-radius: 6px; }
        .table-wrap { overflow-x: auto; }
        .data-table th[data-sort] { cursor: pointer; user-select: none; }
        .data-table th[data-sort]:hover { background: #e9ecef; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .placeholder-msg { color: #666; margin-bottom: 16px; }
        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: white; border-radius: 12px; padding: 24px; max-width: 420px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .modal-box h3 { margin-bottom: 12px; color: var(--primary); }
        .modal-box .modal-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px; }
        /* Analytics Charts */
        .chart-container {
            height: 300px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
            position: relative;
        }
        .chart-container canvas { max-height: 100%; }
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-logo {
                width: 35px;
                height: 35px;
            }

            .sidebar-header {
                padding: 15px 10px;
            }

            .sidebar-menu a {
                justify-content: center;
                padding: 15px;
            }
            .sidebar-menu a i {
                font-size: 1.3rem;
            }
            .header-actions {
                gap: 10px;
            }
            .header-action-btn {
                padding: 8px;
                font-size: 1rem;
            }
            .main-content {
                margin-left: 70px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
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
            <img src="<?php echo IMG_ROOT.'/logo/logo design 1(2).png'?>" alt="Logo" class="sidebar-logo">
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active" data-tab="dashboard"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="#" data-tab="transaction-management"><i class="fas fa-file-invoice-dollar"></i> <span>Transaction Management</span></a></li>
            <li><a href="#" data-tab="trip-management"><i class="fas fa-route"></i> <span>Trip Management</span></a></li>
            <li><a href="#" data-tab="driver-guide-management"><i class="fas fa-users-cog"></i> <span>Driver &amp; Guide Management</span></a></li>
            <li><a href="#" data-tab="reports-analytics"><i class="fas fa-chart-line"></i> <span>Reports &amp; Analytics</span></a></li>
            <li><a href="#" data-tab="internal-chat"><i class="fas fa-comments"></i> <span>Internal Chat</span></a></li>
        </ul>
        <div class="sidebar-user-section">
            <?php $user = getLoggedInUser(); $firstInitial = !empty($user['fullname']) ? strtoupper(substr($user['fullname'], 0, 1)) : 'B'; $profilePhoto = $user['profile_photo'] ?? null; ?>
            <div id="sidebarUserContainer" class="sidebar-user-info" tabindex="0" role="button" aria-haspopup="true" aria-expanded="false">
                <div class="sidebar-user-avatar" id="sidebarUserAvatar">
                    <?php if (!empty($profilePhoto) && file_exists(ROOT_PATH.'/public/'.$user['profile_photo'])): ?>
                        <img src="<?=URL_ROOT.'/public/'.$user['profile_photo']?>" alt="" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                        <?= $firstInitial ?>
                    <?php endif; ?>
                </div>
                <div class="sidebar-user-details">
                    <span class="sidebar-user-name" id="sidebarUserName"><?= htmlspecialchars($user['fullname'] ?? 'Business Manager') ?></span>
                    <span class="sidebar-user-role">Business Manager</span>
                </div>
                <i class="fas fa-chevron-up sidebar-dropdown-icon"></i>
                <div class="sidebar-dropdown-menu" id="sidebarUserDropdown">
                    <a href="#" class="sidebar-dropdown-item" id="sidebarProfileSettingsBtn"><i class="fas fa-cog"></i> Profile Settings</a>
                    <a href="#" class="sidebar-dropdown-item" id="sidebarMyProfileBtn"><i class="fas fa-user-circle"></i> My Profile</a>
                    <div class="sidebar-dropdown-divider"></div>
                    <a href="<?= URL_ROOT; ?>/user/logout" class="sidebar-dropdown-item sidebar-logout-item" id="sidebarLogoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Hello <?php $u = getLoggedInUser(); echo htmlspecialchars($u['fullname'] ?? 'Business Manager').' Welcome Back!'; ?></h1>
            <div class="header-actions">
                <button class="header-action-btn" id="notificationsBtn" title="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notificationBadge">3</span>
                </button>
                <button class="header-action-btn" id="messagesBtn" title="Messages">
                    <i class="fas fa-envelope"></i>
                    <span class="message-badge" id="messageBadge">2</span>
                </button>
            </div>
        </div>
        <!-- Dashboard Content -->
        <div class="dashboard-content active" id="dashboard">
            <?php $kpi = $kpiStats ?? []; ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon revenue"><i class="fas fa-dollar-sign"></i></div>
                    <div class="stat-number">Rs. <?= number_format($kpi['revenue'] ?? 0, 0); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon payouts"><i class="fas fa-route"></i></div>
                    <div class="stat-number"><?= (int)($kpi['trips_count'] ?? 0); ?></div>
                    <div class="stat-label">Trips</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon commissions"><i class="fas fa-id-card"></i></div>
                    <div class="stat-number"><?= (int)($kpi['drivers_count'] ?? 0); ?></div>
                    <div class="stat-label">Drivers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon refunds"><i class="fas fa-undo"></i></div>
                    <div class="stat-number"><?= (int)($kpi['refunds_count'] ?? 0); ?></div>
                    <div class="stat-label">Pending Refunds</div>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h2>Revenue Trend</h2></div>
                <div class="chart-container" style="height: 280px;"><canvas id="chartRevenue"></canvas></div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="card">
                    <div class="card-header"><h2>Transactions by Type</h2></div>
                    <div class="chart-container" style="height: 240px;"><canvas id="chartPie"></canvas></div>
                </div>
                <div class="card">
                    <div class="card-header"><h2>Recent Activity</h2></div>
                    <div style="display: grid; gap: 12px;">
                        <div style="padding: 12px; border-left: 4px solid var(--primary); background: #f8f9fa; border-radius: 0 8px 8px 0;"><strong>Refund Requests</strong> — <?= (int)($kpi['refunds_count'] ?? 0); ?> pending</div>
                        <div style="padding: 12px; border-left: 4px solid #4CAF50; background: #f8f9fa; border-radius: 0 8px 8px 0;"><strong>Trips</strong> — <?= (int)($kpi['trips_count'] ?? 0); ?> total</div>
                        <div style="padding: 12px; border-left: 4px solid #2196F3; background: #f8f9fa; border-radius: 0 8px 8px 0;"><strong>Platform Commission</strong> — 15%</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Transaction Management (heading + tabs + date filters + content) -->
        <div class="dashboard-content" id="transaction-management">
            <h1 class="section-heading">Transaction Management</h1>
            <div class="content-tabs" data-section="transaction-management">
                <button type="button" class="tab active" data-subtab="refunds">Refund Requests</button>
                <button type="button" class="tab" data-subtab="payouts">Payouts</button>
                <button type="button" class="tab" data-subtab="transactions">Traveller Payments</button>
            </div>
            <div class="date-filters" data-section="transaction-management">
                <button type="button" class="date-btn" data-datefilter="all">Show All</button>
                <button type="button" class="date-btn active" data-datefilter="today">Today</button>
                <button type="button" class="date-btn" data-datefilter="last7">Last 7 Days</button>
                <button type="button" class="date-btn" data-datefilter="custom">Custom Date</button>
                <span class="custom-date-range" id="custom-date-range-transaction-management" style="display:none;">
                    <input type="date" id="custom-from-transaction-management" class="custom-date-from"> to
                    <input type="date" id="custom-to-transaction-management" class="custom-date-to">
                </span>
            </div>
            <div class="section-panel active" id="tx-sub-refunds">
            <div class="card">
                <div class="card-header"><h2>Refund Requests</h2></div>
                <div class="table-toolbar">
                    <input type="text" class="search-input" placeholder="Search..." id="search-refunds">
                    <select id="filter-refund-status"><option value="">All statuses</option><option value="pending">Pending</option><option value="completed">completed</option><option value="rejected">Rejected</option></select>
                </div>
                <div class="table-wrap">
                <table class="data-table sortable">
                    <thead><tr><th data-sort="request_id">Request ID</th><th>User</th><th>Itinerary</th><th data-sort="amount">Amount</th><th>Reason</th><th data-sort="request_date">Date</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php $refundRequests = $refundRequests ?? []; if (empty($refundRequests)): ?>
                        <tr><td colspan="8" style="text-align:center;padding:30px;">No refund requests found.</td></tr>
                        <?php else: foreach ($refundRequests as $refund):
                            $reqStatus = strtolower($refund->status ?? 'pending');
                            $statusClass = in_array($reqStatus, ['approved','processed']) ? 'status-completed' : ($reqStatus === 'rejected' ? 'status-banned' : 'status-pending');
                            $requestDate = !empty($refund->request_date) ? date('Y-m-d', strtotime($refund->request_date)) : '';
                        ?>
                        <tr data-filter-status="<?= htmlspecialchars($reqStatus); ?>" data-row-date="<?= htmlspecialchars($requestDate); ?>">
                            <td><?= htmlspecialchars($refund->request_id ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($refund->user_name ?? 'Unknown'); ?></td>
                            <td><?= htmlspecialchars($refund->trip_id ?? '—'); ?></td>
                            <td>Rs. <?= number_format((float)($refund->amount ?? 0), 2); ?></td>
                            <td><?= htmlspecialchars(mb_substr($refund->reason ?? '—', 0, 40)); ?><?= mb_strlen($refund->reason ?? '') > 40 ? '…' : ''; ?></td>
                            <td><?= $requestDate; ?></td>
                            <td><span class="status-badge <?= $statusClass; ?>"><?= htmlspecialchars($refund->status ?? 'Pending'); ?></span></td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm btn-view">View</button>
                                <?php if ($reqStatus === 'pending'): ?>
                                <button type="button" class="btn btn-success btn-sm btn-approve" data-id="<?= (int)($refund->request_id ?? 0); ?>">Approve</button>
                                <button type="button" class="btn btn-danger btn-sm btn-reject" data-id="<?= (int)($refund->request_id ?? 0); ?>">Reject</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
            <div class="section-panel" id="tx-sub-payouts">
            <div class="card">
                <div class="card-header"><h2>Payouts (Drivers &amp; Guides)</h2></div>
                <div class="table-toolbar">
                    <input type="text" class="search-input" placeholder="Search..." id="search-payouts">
                    <select id="filter-payout-type"><option value="">All</option><option value="Driver">Driver</option><option value="Guide">Guide</option></select>
                    <select id="filter-payout-status"><option value="">All statuses</option><option value="pending">Pending</option><option value="completed">Completed</option></select>
                </div>
                <div class="table-wrap">
                <table class="data-table sortable">
                    <thead><tr><th>Payout ID</th><th>Provider</th><th>Trip ID</th><th>Type</th><th>Earnings</th><th>Commission</th><th>Net</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php $payouts = $payouts ?? []; if (empty($payouts)): ?>
                        <tr><td colspan="10" style="text-align:center;padding:30px;">No payouts found.</td></tr>
                        <?php else: foreach ($payouts as $p): $payoutServiceType = trim($p->service_type ?? ''); $payoutTypeLabel = in_array(strtolower($payoutServiceType), ['driver', 'guide']) ? ucfirst(strtolower($payoutServiceType)) : ($payoutServiceType ?: '—'); $payoutDate = !empty($p->payout_date) ? date('Y-m-d', strtotime($p->payout_date)) : ''; $payoutStatus = strtolower($p->payout_status ?? 'pending'); ?>
                        <tr data-service-type="<?= htmlspecialchars(strtolower($payoutServiceType)); ?>" data-row-date="<?= htmlspecialchars($payoutDate); ?>" data-payout-status="<?= htmlspecialchars($payoutStatus); ?>">
                            <td><?= htmlspecialchars($p->payoutID ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($p->providerName ?? '—'); ?></td>
                            <td><?= htmlspecialchars($p->tripID ?? '—'); ?></td>
                            <td><?= htmlspecialchars($payoutTypeLabel); ?></td>
                            <td>Rs. <?= number_format((float)($p->earnings ?? 0), 2); ?></td>
                            <td>Rs. <?= number_format((float)($p->commission ?? 0), 2); ?></td>
                            <td>Rs. <?= number_format((float)($p->net_payout ?? 0), 2); ?></td>
                            <td><span class="status-badge status-<?= strtolower($p->payout_status ?? 'pending'); ?>"><?= htmlspecialchars($p->payout_status ?? 'Pending'); ?></span></td>
                            <td><?= $payoutDate ?: '—'; ?></td>
                            <td><button type="button" class="btn btn-info btn-sm">View</button></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
            <div class="section-panel" id="tx-sub-transactions">
            <div class="card">
                <div class="card-header"><h2>Traveller Payments</h2></div>
                <div class="table-toolbar">
                    <input type="text" class="search-input" placeholder="" id="search-tx-all">
                    
                    <select id="filter-tx-status"><option value="">All statuses</option><option value="pending">Pending</option><option value="completed">Completed</option></select>
                </div>
                <div class="table-wrap">
                <table class="data-table sortable">
                    <thead><tr><th data-sort="transactionID">ID</th><th>User</th><th data-sort="amount">Amount</th><th>Type</th><th data-sort="transactionDate">Date</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php $transactions = $transactions ?? []; if (empty($transactions)): ?>
                        <tr><td colspan="7" style="text-align:center;padding:30px;">No traveller payments found.</td></tr>
                        <?php else: foreach ($transactions as $t):
                            $rowStatus = strtolower($t->transaction_status ?? 'pending');
                            $statusClass = (strpos($rowStatus, 'complete') !== false) ? 'status-completed' : 'status-pending';
                        ?>
                        <?php $txDate = !empty($t->transactionDate) ? date('Y-m-d', strtotime($t->transactionDate)) : ''; ?>
                        <tr data-filter-status="<?= htmlspecialchars($rowStatus); ?>" data-row-date="<?= htmlspecialchars($txDate); ?>">
                            <td><?= htmlspecialchars($t->transactionID ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($t->userName ?? '—'); ?></td>
                            <td>Rs. <?= number_format((float)($t->amount ?? 0), 2); ?></td>
                            <td><?= htmlspecialchars($t->type ?? '—'); ?></td>
                            <td><?= htmlspecialchars($t->transactionDate ?? '—'); ?></td>
                            <td><span class="status-badge <?= $statusClass; ?>"><?= htmlspecialchars($t->transaction_status ?? 'Pending'); ?></span></td>
                            <td>
                                <?php if (stripos($t->type ?? '', 'driver') !== false || stripos($t->type ?? '', 'guide') !== false) { if ($rowStatus === 'pending'): ?>
                                <a href="<?= URL_ROOT; ?>/dashboard/processTransaction/<?= (int)($t->transactionID ?? 0); ?>" class="btn btn-warning btn-sm">Process</a>
                                <?php else: ?><button type="button" class="btn btn-success btn-sm">Completed</button><?php endif; ?>
                                <?php } else { ?><button type="button" class="btn btn-info btn-sm">View</button><?php } ?>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
        </div>
        <!-- Trip Management (heading + tabs + date filters + content) -->
        <div class="dashboard-content" id="trip-management">
            <h1 class="section-heading">Trip Management</h1>
            <div class="content-tabs" data-section="trip-management">
                <button type="button" class="tab active" data-subtab="today">Completed Trips</button>
                <button type="button" class="tab" data-subtab="ongoing">Ongoing Trips</button>
                <button type="button" class="tab" data-subtab="upcoming">Scheduled Trips</button>
            </div>
            <div class="date-filters" data-section="trip-management">
                <button type="button" class="date-btn active" data-datefilter="all">Show All</button>
                <button type="button" class="date-btn" data-datefilter="custom">Custom Date</button>
                <span class="custom-date-range" id="custom-date-range-trip-management" style="display:none;">
                    <input type="date" id="custom-from-trip-management" class="custom-date-from"> to
                    <input type="date" id="custom-to-trip-management" class="custom-date-to">
                </span>
            </div>
            <div class="section-panel active" id="trip-sub-today">
            <div class="card">
                <div class="card-header"><h2>Completed Trips</h2></div>
                <div class="table-toolbar"><input type="text" class="search-input" placeholder="Search..." id="search-trip-today"></div>
                <div class="table-wrap">
                <table class="data-table">
                    <thead><tr><th>Trip ID</th><th>Traveller</th><th>Start Date</th><th>End Date</th><th>Transaction Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php $tripsToday = $tripsToday ?? []; if (empty($tripsToday)): ?>
                        <tr><td colspan="6" style="text-align:center;padding:30px;">No completed trips.</td></tr>
                        <?php else: foreach ($tripsToday as $tr): $start = $tr->startDate ?? $tr->start_date ?? '—'; $end = $tr->endDate ?? $tr->end_date ?? '—'; $title = $tr->tripTitle ?? $tr->trip_title ?? 'Trip #'.($tr->tripId ?? $tr->id ?? ''); $tripRowDate = (!empty($tr->startDate) || !empty($tr->start_date)) ? date('Y-m-d', strtotime($tr->startDate ?? $tr->start_date)) : ''; ?>
                        <tr data-row-date="<?= htmlspecialchars($tripRowDate); ?>">
                            <td><?= htmlspecialchars($title); ?></td>
                            <td><?= htmlspecialchars($tr->userId ?? $tr->user_id ?? '—'); ?></td>
                            <td><?= htmlspecialchars($start); ?></td>
                            <td><?= htmlspecialchars($end); ?></td>
                            <td><span class="status-badge status-pending"><?= htmlspecialchars($tr->status ?? '—'); ?></span></td>
                            <td><button type="button" class="btn btn-info btn-sm">View</button> <button type="button" class="btn btn-primary btn-sm btn-edit">Edit</button></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
            <div class="section-panel" id="trip-sub-ongoing">
            <div class="card">
                <div class="card-header"><h2>Ongoing Trips</h2></div>
                <div class="table-toolbar"><input type="text" class="search-input" placeholder="Search..." id="search-trip-ongoing"></div>
                <div class="table-wrap">
                <table class="data-table">
                    <thead><tr><th>Trip ID</th><th>Traveller</th><th>Start Date</th><th>End Date</th><th>Transaction Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php $ongoingTrips = $ongoingTrips ?? []; if (empty($ongoingTrips)): ?>
                        <tr><td colspan="6" style="text-align:center;padding:30px;">No ongoing trips.</td></tr>
                        <?php else: foreach ($ongoingTrips as $tr): $start = $tr->startDate ?? $tr->start_date ?? '—'; $end = $tr->endDate ?? $tr->end_date ?? '—'; $title = $tr->tripTitle ?? $tr->trip_title ?? 'Trip #'.($tr->tripId ?? $tr->id ?? ''); $tripRowDate = (!empty($tr->startDate) || !empty($tr->start_date)) ? date('Y-m-d', strtotime($tr->startDate ?? $tr->start_date)) : ''; ?>
                        <tr data-row-date="<?= htmlspecialchars($tripRowDate); ?>">
                            <td><?= htmlspecialchars($title); ?></td>
                            <td><?= htmlspecialchars($tr->userId ?? $tr->user_id ?? '—'); ?></td>
                            <td><?= htmlspecialchars($start); ?></td>
                            <td><?= htmlspecialchars($end); ?></td>
                            <td><span class="status-badge status-active"><?= htmlspecialchars($tr->status ?? 'Ongoing'); ?></span></td>
                            <td><button type="button" class="btn btn-info btn-sm">View</button> <button type="button" class="btn btn-primary btn-sm btn-edit">Edit</button></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
            <div class="section-panel" id="trip-sub-upcoming">
            <div class="card">
                <div class="card-header"><h2>Scheduled Trips</h2></div>
                <div class="table-toolbar"><input type="text" class="search-input" placeholder="Search..." id="search-trip-upcoming"></div>
                <div class="table-wrap">
                <table class="data-table">
                    <thead><tr><th>Trip ID</th><th>Traveller</th><th>Start Date</th><th>End Date</th><th>Transaction Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php $upcomingTrips = $upcomingTrips ?? []; if (empty($upcomingTrips)): ?>
                        <tr><td colspan="6" style="text-align:center;padding:30px;">No scheduled trips.</td></tr>
                        <?php else: foreach ($upcomingTrips as $tr): $start = $tr->startDate ?? $tr->start_date ?? '—'; $end = $tr->endDate ?? $tr->end_date ?? '—'; $title = $tr->tripTitle ?? $tr->trip_title ?? 'Trip #'.($tr->tripId ?? $tr->id ?? ''); $tripRowDate = (!empty($tr->startDate) || !empty($tr->start_date)) ? date('Y-m-d', strtotime($tr->startDate ?? $tr->start_date)) : ''; ?>
                        <tr data-row-date="<?= htmlspecialchars($tripRowDate); ?>">
                            <td><?= htmlspecialchars($title); ?></td>
                            <td><?= htmlspecialchars($tr->userId ?? $tr->user_id ?? '—'); ?></td>
                            <td><?= htmlspecialchars($start); ?></td>
                            <td><?= htmlspecialchars($end); ?></td>
                            <td><span class="status-badge status-pending"><?= htmlspecialchars($tr->status ?? '—'); ?></span></td>
                            <td><button type="button" class="btn btn-info btn-sm">View</button> <button type="button" class="btn btn-primary btn-sm btn-edit">Edit</button></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
        </div>
        <!-- Driver & Guide Management (heading + tabs + content) -->
        <div class="dashboard-content" id="driver-guide-management">
            <h1 class="section-heading">Driver &amp; Guide Management</h1>
            <div class="content-tabs" data-section="driver-guide-management">
                <button type="button" class="tab active" data-subtab="drivers">Drivers</button>
                <button type="button" class="tab" data-subtab="guides">Guides</button>
                <button type="button" class="tab" data-subtab="commission">Commission Table</button>
            </div>
            <div class="section-panel active" id="dg-sub-drivers">
            <div class="card">
                <div class="card-header"><h2>Drivers</h2></div>
                <div class="table-toolbar">
                    <input type="text" class="search-input" placeholder="Search drivers..." id="search-drivers">
                    <select id="filter-drivers-status"><option value="">All statuses</option><option value="active">Active</option><option value="inactive">Inactive</option></select>
                    <select id="filter-drivers-revenue"><option value="">By revenue</option><option value="high">High to low</option><option value="low">Low to high</option></select>
                </div>
                <div class="table-wrap">
                <table class="data-table" id="table-drivers">
                    <thead><tr><th>ID</th><th>Name</th><th>Daily rate</th><th>Hourly rate</th><th>Total revenue (last month)</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php $drivers = $drivers ?? []; if (empty($drivers)): ?>
                        <tr><td colspan="7" style="text-align:center;padding:30px;">No drivers found.</td></tr>
                        <?php else: foreach ($drivers as $d): $dStatus = strtolower($d->status ?? $d->is_active ?? 'active'); $dRevenue = (float)($d->total_revenue ?? 0); ?>
                        <tr data-dg-status="<?= htmlspecialchars($dStatus); ?>" data-revenue="<?= $dRevenue; ?>">
                            <td><?= htmlspecialchars($d->userID ?? $d->id ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($d->fullname ?? $d->name ?? '—'); ?></td>
                            <td>Rs. <?= number_format((float)($d->day_payment ?? 0), 2); ?></td>
                            <td>Rs. <?= number_format((float)($d->hourly_rate ?? 0), 2); ?></td>
                            <td>Rs. <?= number_format($dRevenue, 2); ?></td>
                            <td><span class="status-badge status-<?= $dStatus === 'active' ? 'active' : 'banned'; ?>"><?= $dStatus === 'active' ? 'Active' : 'Inactive'; ?></span></td>
                            <td><button type="button" class="btn btn-info btn-sm">View profile</button></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
            <div class="section-panel" id="dg-sub-guides">
            <div class="card">
                <div class="card-header"><h2>Guides</h2></div>
                <div class="table-toolbar">
                    <input type="text" class="search-input" placeholder="Search guides..." id="search-guides">
                    <select id="filter-guides-status"><option value="">All statuses</option><option value="active">Active</option><option value="inactive">Inactive</option></select>
                    <select id="filter-guides-revenue"><option value="">By revenue</option><option value="high">High to low</option><option value="low">Low to high</option></select>
                </div>
                <div class="table-wrap">
                <table class="data-table" id="table-guides">
                    <thead><tr><th>ID</th><th>Name</th><th>TravelSpot</th><th>Base charge</th><th>Total revenue (last month)</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php $guides = $guides ?? []; if (empty($guides)): ?>
                        <tr><td colspan="7" style="text-align:center;padding:30px;">No guides found.</td></tr>
                        <?php else: foreach ($guides as $g): $gStatus = strtolower($g->status ?? $g->is_active ?? 'active'); $gRevenue = (float)($g->total_revenue ?? 0); ?>
                        <tr data-dg-status="<?= htmlspecialchars($gStatus); ?>" data-revenue="<?= $gRevenue; ?>">
                            <td><?= htmlspecialchars($g->id ?? 'N/A'); ?></td>
                            <td><?= htmlspecialchars($g->fullname ?? $g->name ?? '—'); ?></td>
                            <td><?= htmlspecialchars($g->travel_spot ?? '—'); ?></td>
                            <td>Rs. <?= number_format((float)($g->base_charge ?? 0), 2); ?></td>
                            <td>Rs. <?= number_format($gRevenue, 2); ?></td>
                            <td><span class="status-badge status-<?= $gStatus === 'active' ? 'active' : 'banned'; ?>"><?= $gStatus === 'active' ? 'Active' : 'Inactive'; ?></span></td>
                            <td><button type="button" class="btn btn-info btn-sm">View profile</button></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
            </div>
            <div class="section-panel" id="dg-sub-commission">
            <div class="card">
                <div class="card-header"><h2>Commission Table</h2></div>
                <p class="placeholder-msg">Set commission rate per role. Save to apply changes.</p>
                <table class="data-table" id="commission-table">
                    <thead><tr><th>Role</th><th>Commission %</th><th>Action</th></tr></thead>
                    <tbody>
                        <tr data-commission-role="driver"><td>Driver</td><td><input type="number" min="0" max="100" step="0.5" value="15" id="commission-driver" class="commission-input">%</td><td><button type="button" class="btn btn-primary btn-sm btn-save-commission">Save</button></td></tr>
                        <tr data-commission-role="guide"><td>Guide</td><td><input type="number" min="0" max="100" step="0.5" value="15" id="commission-guide" class="commission-input">%</td><td><button type="button" class="btn btn-primary btn-sm btn-save-commission">Save</button></td></tr>
                    </tbody>
                </table>
            </div>
            </div>
        </div>
        <!-- Reports & Analytics (heading + tabs + content) -->
        <div class="dashboard-content" id="reports-analytics">
            <h1 class="section-heading">Reports &amp; Analytics</h1>
            <div class="content-tabs" data-section="reports-analytics">
                <button type="button" class="tab active" data-subtab="revenue">Revenue Reports</button>
                <button type="button" class="tab" data-subtab="refund">Refund Reports</button>
                <button type="button" class="tab" data-subtab="occupancy">Trip Occupancy</button>
                <button type="button" class="tab" data-subtab="performance">Driver &amp; Guide Performance</button>
            </div>
            <div class="section-panel active" id="rep-sub-revenue">
            <div class="card"><div class="card-header"><h2>Revenue Reports</h2></div><div class="chart-container" style="height:300px"><canvas id="chartRevenueReport"></canvas></div></div>
            </div>
            <div class="section-panel" id="rep-sub-refund">
            <div class="card"><div class="card-header"><h2>Refund Reports</h2></div><div class="chart-container" style="height:300px"><canvas id="chartRefundReport"></canvas></div></div>
            </div>
            <div class="section-panel" id="rep-sub-occupancy">
            <div class="card"><div class="card-header"><h2>Trip Occupancy</h2></div><div class="chart-container" style="height:300px"><canvas id="chartOccupancy"></canvas></div></div>
            </div>
            <div class="section-panel" id="rep-sub-performance">
            <div class="card"><div class="card-header"><h2>Driver &amp; Guide Performance</h2></div><div class="chart-container" style="height:300px"><canvas id="chartDriverGuide"></canvas></div></div>
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
                        <div class="chat-list" id="staffChatList">
                            <div class="chat-item active" data-staff="admin">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">Admin User</div>
                                    <div class="chat-item-time">Online</div>
                                </div>
                                <div class="chat-item-preview">Site Administrator</div>
                            </div>
                            <div class="chat-item" data-staff="alex">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">Alex Johnson</div>
                                    <div class="chat-item-time">2h ago</div>
                                </div>
                                <div class="chat-item-preview">Content Moderator</div>
                            </div>
                            <div class="chat-item" data-staff="lisa">
                                <div class="chat-item-header">
                                    <div class="chat-item-name">Lisa Chen</div>
                                    <div class="chat-item-time">1h ago</div>
                                </div>
                                <div class="chat-item-preview">Support Moderator</div>
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
                                <h3>Admin User</h3>
                                <p>Site Administrator - Online</p>
                            </div>
                        </div>
                        <div class="chat-messages" id="staffChatMessages">
                            <div class="message admin">
                                Hi Michael! How are the Q1 financial reports coming along?
                                <div class="message-time">10 minutes ago</div>
                            </div>
                            <div class="message moderator">
                                Almost done! I'll have the revenue and commission reports ready by end of day.
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
                <form>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" value="michael_rodriguez">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" value="michael@travel.com">
                    </div>
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" value="Michael Rodriguez">
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select id="role">
                            <option value="business">Business Manager</option>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Tab Navigation (main sidebar)
        function switchTab(tabId) {
            if (!tabId) return;
            document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
            document.querySelectorAll('.dashboard-content').forEach(c => c.classList.remove('active'));
            const link = document.querySelector('[data-tab="' + tabId + '"]');
            if (link) link.classList.add('active');
            const content = document.getElementById(tabId);
            if (content) content.classList.add('active');
            if (tabId === 'internal-chat') setTimeout(initInternalChat, 100);
        }
        document.querySelectorAll('.sidebar-menu a[data-tab]').forEach(link => {
            link.addEventListener('click', function(e) { e.preventDefault(); switchTab(this.getAttribute('data-tab')); });
        });
        // Sidebar user dropdown
        var sidebarUserContainer = document.getElementById('sidebarUserContainer');
        var sidebarUserDropdown = document.getElementById('sidebarUserDropdown');
        var sidebarProfileSettingsBtn = document.getElementById('sidebarProfileSettingsBtn');
        var sidebarMyProfileBtn = document.getElementById('sidebarMyProfileBtn');
        var sidebarLogoutBtn = document.getElementById('sidebarLogoutBtn');
        if (sidebarUserContainer && sidebarUserDropdown) {
            function toggleSidebarDropdown() {
                var isShown = sidebarUserDropdown.classList.contains('show');
                sidebarUserDropdown.classList.toggle('show', !isShown);
                sidebarUserContainer.setAttribute('aria-expanded', !isShown);
            }
            function closeSidebarDropdown() {
                sidebarUserDropdown.classList.remove('show');
                sidebarUserContainer.setAttribute('aria-expanded', 'false');
            }
            sidebarUserContainer.addEventListener('click', function(e) { e.stopPropagation(); toggleSidebarDropdown(); });
            sidebarUserContainer.addEventListener('keydown', function(e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleSidebarDropdown(); } });
            document.addEventListener('click', closeSidebarDropdown);
            document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeSidebarDropdown(); });
            sidebarUserDropdown.addEventListener('click', function(e) { e.stopPropagation(); });
            if (sidebarProfileSettingsBtn) sidebarProfileSettingsBtn.addEventListener('click', function(e) { e.preventDefault(); closeSidebarDropdown(); switchTab('profile'); });
            if (sidebarMyProfileBtn) sidebarMyProfileBtn.addEventListener('click', function(e) { e.preventDefault(); closeSidebarDropdown(); switchTab('profile'); });
            if (sidebarLogoutBtn) sidebarLogoutBtn.addEventListener('click', function(e) { if (!confirm('Are you sure you want to log out?')) e.preventDefault(); });
        }
        // Content sub-tabs (Refund Requests | Payouts | Transactions, etc.)
        var sectionPrefix = { 'transaction-management': 'tx-sub-', 'trip-management': 'trip-sub-', 'driver-guide-management': 'dg-sub-', 'reports-analytics': 'rep-sub-' };
        document.querySelectorAll('.content-tabs').forEach(function(tabBar) {
            tabBar.querySelectorAll('.tab').forEach(function(tab) {
                tab.addEventListener('click', function() {
                    var section = tabBar.closest('.dashboard-content');
                    if (!section) return;
                    var sectionId = section.id;
                    var subtab = this.getAttribute('data-subtab');
                    var prefix = sectionPrefix[sectionId] || '';
                    tabBar.querySelectorAll('.tab').forEach(function(t) { t.classList.remove('active'); });
                    this.classList.add('active');
                    section.querySelectorAll('.section-panel').forEach(function(panel) {
                        panel.classList.toggle('active', panel.id === prefix + subtab);
                    });
                });
            });
        });
        // Date filter buttons (Today | Last 7 Days | Custom Date) — filter table rows by date
        function getDateRange(sectionId, dateFilter) {
            if (dateFilter === 'all') return null;
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            var from, to;
            if (dateFilter === 'today') {
                from = new Date(today);
                to = new Date(today);
                to.setHours(23, 59, 59, 999);
            } else if (dateFilter === 'last7') {
                to = new Date(today);
                to.setHours(23, 59, 59, 999);
                from = new Date(today);
                from.setDate(from.getDate() - 6);
            } else {
                var fromEl = document.getElementById('custom-from-' + sectionId);
                var toEl = document.getElementById('custom-to-' + sectionId);
                if (fromEl && toEl && fromEl.value && toEl.value) {
                    from = new Date(fromEl.value);
                    to = new Date(toEl.value);
                    from.setHours(0, 0, 0, 0);
                    to.setHours(23, 59, 59, 999);
                } else {
                    from = new Date(today);
                    from.setDate(from.getDate() - 30);
                    to = new Date(today);
                }
            }
            return { from: from, to: to };
        }
        function applyDateFilter(sectionId) {
            var bar = document.querySelector('.date-filters[data-section="' + sectionId + '"]');
            if (!bar) return;
            var activeBtn = bar.querySelector('.date-btn.active');
            var dateFilter = (activeBtn && activeBtn.getAttribute('data-datefilter')) || 'today';
            var range = getDateRange(sectionId, dateFilter);
            var section = document.getElementById(sectionId);
            if (!section) return;
            section.querySelectorAll('.section-panel').forEach(function(panel) {
                panel.querySelectorAll('tbody tr[data-row-date]').forEach(function(row) {
                    if (dateFilter === 'all' || !range) {
                        row.style.display = '';
                        return;
                    }
                    var rowDateStr = row.getAttribute('data-row-date') || '';
                    if (!rowDateStr) {
                        row.style.display = '';
                        return;
                    }
                    var rowDate = new Date(rowDateStr);
                    rowDate.setHours(0, 0, 0, 0);
                    var show = rowDate >= range.from && rowDate <= range.to;
                    row.style.display = show ? '' : 'none';
                });
            });
        }
        document.querySelectorAll('.date-filters').forEach(function(bar) {
            var sectionId = bar.getAttribute('data-section');
            var customRange = document.getElementById('custom-date-range-' + sectionId);
            var customFrom = document.getElementById('custom-from-' + sectionId);
            var customTo = document.getElementById('custom-to-' + sectionId);
            bar.querySelectorAll('.date-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    bar.querySelectorAll('.date-btn').forEach(function(b) { b.classList.remove('active'); });
                    this.classList.add('active');
                    if (this.getAttribute('data-datefilter') === 'custom') {
                        if (customRange) customRange.style.display = 'inline-flex';
                    } else {
                        if (customRange) customRange.style.display = 'none';
                    }
                    applyDateFilter(sectionId);
                });
            });
            if (customFrom) customFrom.addEventListener('change', function() { applyDateFilter(sectionId); });
            if (customTo) customTo.addEventListener('change', function() { applyDateFilter(sectionId); });
        });
        document.querySelectorAll('.date-filters').forEach(function(bar) {
            var sectionId = bar.getAttribute('data-section');
            applyDateFilter(sectionId);
        });
        // Driver & Guide Management: status filter (no date filters)
        function applyDriverGuideFilters() {
            var driversStatus = (document.getElementById('filter-drivers-status') || {}).value || '';
            var guidesStatus = (document.getElementById('filter-guides-status') || {}).value || '';
            document.querySelectorAll('#dg-sub-drivers tbody tr[data-dg-status]').forEach(function(row) {
                var statusMatch = !driversStatus || (row.getAttribute('data-dg-status') || '').trim().toLowerCase() === driversStatus.trim().toLowerCase();
                row.style.display = statusMatch ? '' : 'none';
            });
            document.querySelectorAll('#dg-sub-guides tbody tr[data-dg-status]').forEach(function(row) {
                var statusMatch = !guidesStatus || (row.getAttribute('data-dg-status') || '').trim().toLowerCase() === guidesStatus.trim().toLowerCase();
                row.style.display = statusMatch ? '' : 'none';
            });
        }
        function sortDriversByRevenue(order) {
            var tbody = document.querySelector('#table-drivers tbody');
            if (!tbody || !order) return;
            var rows = [].slice.call(tbody.querySelectorAll('tr[data-revenue]'));
            rows.sort(function(a, b) {
                var ra = parseFloat(a.getAttribute('data-revenue')) || 0, rb = parseFloat(b.getAttribute('data-revenue')) || 0;
                return order === 'high' ? rb - ra : ra - rb;
            });
            rows.forEach(function(r) { tbody.appendChild(r); });
        }
        function sortGuidesByRevenue(order) {
            var tbody = document.querySelector('#table-guides tbody');
            if (!tbody || !order) return;
            var rows = [].slice.call(tbody.querySelectorAll('tr[data-revenue]'));
            rows.sort(function(a, b) {
                var ra = parseFloat(a.getAttribute('data-revenue')) || 0, rb = parseFloat(b.getAttribute('data-revenue')) || 0;
                return order === 'high' ? rb - ra : ra - rb;
            });
            rows.forEach(function(r) { tbody.appendChild(r); });
        }
        var filterDriversStatusEl = document.getElementById('filter-drivers-status');
        if (filterDriversStatusEl) filterDriversStatusEl.addEventListener('change', applyDriverGuideFilters);
        var filterGuidesStatusEl = document.getElementById('filter-guides-status');
        if (filterGuidesStatusEl) filterGuidesStatusEl.addEventListener('change', applyDriverGuideFilters);
        var filterDriversRevenueEl = document.getElementById('filter-drivers-revenue');
        if (filterDriversRevenueEl) filterDriversRevenueEl.addEventListener('change', function() { sortDriversByRevenue(this.value || ''); });
        var filterGuidesRevenueEl = document.getElementById('filter-guides-revenue');
        if (filterGuidesRevenueEl) filterGuidesRevenueEl.addEventListener('change', function() { sortGuidesByRevenue(this.value || ''); });
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
                if (this.textContent.includes('Reject') || this.textContent.includes('Delete')) {
                    if (confirm('Are you sure you want to perform this action?')) {
                        alert('Action completed successfully!');
                    }
                }
            });
        });
        document.querySelectorAll('.btn-success, .btn-warning, .btn-payout, .btn-commission, .btn-report').forEach(btn => {
            btn.addEventListener('click', function() {
                alert('Action completed successfully!');
            });
        });
        // Internal Chat Functionality - SAFE VERSION
        let currentChat = 'admin';
        const chatData = {
            admin: {
                name: "Admin User",
                role: "Site Administrator",
                status: "Online",
                messages: [
                    { sender: "admin", text: "Hi Michael! How are the Q1 financial reports coming along?", time: "10 minutes ago" },
                    { sender: "moderator", text: "Almost done! I'll have the revenue and commission reports ready by end of day.", time: "5 minutes ago" }
                ]
            },
            alex: {
                name: "Alex Johnson",
                role: "Content Moderator",
                status: "Last seen 2h ago",
                messages: [
                    { sender: "staff", text: "We need to discuss the content guidelines for financial disclosures.", time: "2 hours ago" },
                    { sender: "moderator", text: "Let's schedule a meeting for tomorrow at 2 PM.", time: "1 hour ago" }
                ]
            },
            lisa: {
                name: "Lisa Chen",
                role: "Support Moderator",
                status: "Last seen 1h ago",
                messages: [
                    { sender: "staff", text: "We have several refund requests that need your approval.", time: "1 hour ago" },
                    { sender: "moderator", text: "I'll review them right away. Can you send me the details?", time: "30 minutes ago" }
                ]
            },
            emma: {
                name: "Emma Wilson",
                role: "Operations Manager",
                status: "Last seen 3d ago",
                messages: [
                    { sender: "staff", text: "The driver onboarding process needs financial review.", time: "3 days ago" },
                    { sender: "moderator", text: "I'll look into the payout structure and get back to you.", time: "2 days ago" }
                ]
            }
        };
        function loadChat(staffId) {
            const chat = chatData[staffId];
            if (!chat) return;
            // Safely update header elements
            const nameEl = document.querySelector('.chat-header-info h3');
            const statusEl = document.querySelector('.chat-header-info p');
            const messagesContainer = document.getElementById('staffChatMessages');
            if (nameEl) nameEl.textContent = chat.name;
            if (statusEl) statusEl.textContent = `${chat.role} - ${chat.status}`;
            // Update chat list active state
            document.querySelectorAll('.chat-item').forEach(item => {
                item.classList.remove('active');
            });
            const activeItem = document.querySelector(`[data-staff="${staffId}"]`);
            if (activeItem) activeItem.classList.add('active');
            // Load messages only if container exists
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
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }
        // Initialize chat only when Internal Chat tab is opened
        function initInternalChat() {
            const staffChatList = document.getElementById('staffChatList');
            const staffMessageInput = document.getElementById('staffMessageInput');
            const staffSendButton = document.getElementById('staffSendButton');
            if (!staffChatList || !staffMessageInput || !staffSendButton) return; // Not on chat tab
            // Set up event listeners only once
            if (!staffChatList.dataset.initialized) {
                staffChatList.addEventListener('click', function(e) {
                    const chatItem = e.target.closest('.chat-item');
                    if (chatItem) {
                        const staffId = chatItem.getAttribute('data-staff');
                        if (staffId) {
                            currentChat = staffId;
                            loadChat(staffId);
                        }
                    }
                });
                staffSendButton.addEventListener('click', sendMessage);
                staffMessageInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') sendMessage();
                });
                staffChatList.dataset.initialized = 'true';
            }
            // Load default chat
            loadChat('admin');
        }
        function sendMessage() {
            const messageInput = document.getElementById('staffMessageInput');
            if (!messageInput) return;
            const messageText = messageInput.value.trim();
            if (!messageText) return;
            const currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            chatData[currentChat].messages.push({
                sender: "moderator",
                text: messageText,
                time: currentTime
            });
            loadChat(currentChat);
            messageInput.value = '';
        }
        // Confirmation modal for Approve / Reject / Edit
        (function() {
            var overlay = document.getElementById('confirmModal');
            var titleEl = document.getElementById('modalTitle');
            var msgEl = document.getElementById('modalMessage');
            var cancelBtn = document.getElementById('modalCancel');
            var confirmBtn = document.getElementById('modalConfirm');
            var confirmCallback = null;
            if (cancelBtn) cancelBtn.addEventListener('click', function() { overlay.classList.remove('active'); });
            if (confirmBtn) confirmBtn.addEventListener('click', function() { if (confirmCallback) confirmCallback(); overlay.classList.remove('active'); });
            window.showConfirmModal = function(title, message, onConfirm) {
                if (titleEl) titleEl.textContent = title || 'Confirm';
                if (msgEl) msgEl.textContent = message || 'Are you sure?';
                confirmCallback = onConfirm;
                overlay.classList.add('active');
            };
        })();
        document.querySelectorAll('.btn-approve, .btn-reject').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = this.getAttribute('data-id');
                var isApprove = this.classList.contains('btn-approve');
                window.showConfirmModal(isApprove ? 'Approve Refund' : 'Reject Refund', 'Are you sure you want to ' + (isApprove ? 'approve' : 'reject') + ' this refund request?', function() { alert('Action completed. (Connect to backend to persist.)'); });
            });
        });
        // Charts (dashboard + reports)
        if (typeof Chart !== 'undefined') {
            var revenueCtx = document.getElementById('chartRevenue');
            if (revenueCtx) {
                new Chart(revenueCtx.getContext('2d'), { type: 'line', data: { labels: ['Jan','Feb','Mar','Apr','May','Jun'], datasets: [{ label: 'Revenue', data: [30, 45, 38, 52, 48, 60], borderColor: '#006A71', tension: 0.3, fill: true }] }, options: { responsive: true, maintainAspectRatio: false } });
            }
            var pieCtx = document.getElementById('chartPie');
            if (pieCtx) {
                new Chart(pieCtx.getContext('2d'), { type: 'doughnut', data: { labels: ['Driver','Guide','Refund'], datasets: [{ data: [40, 35, 25], backgroundColor: ['#006A71','#4CAF50','#f44336'] }] }, options: { responsive: true, maintainAspectRatio: false } });
            }
            ['chartRevenueReport','chartRefundReport','chartOccupancy','chartDriverGuide'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) new Chart(el.getContext('2d'), { type: 'bar', data: { labels: ['A','B','C','D','E'], datasets: [{ label: 'Value', data: [12, 19, 8, 15, 10], backgroundColor: '#006A71' }] }, options: { responsive: true, maintainAspectRatio: false } });
            });
        }
        // Transaction Management: category tabs (legacy section - hidden)
        document.querySelectorAll('#transaction-management .tx-cat').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelectorAll('#transaction-management .tx-cat').forEach(function(a) { a.classList.remove('active'); });
                this.classList.add('active');
                var section = this.getAttribute('data-section');
                document.querySelectorAll('#transaction-management .tx-section-card').forEach(function(card) {
                    card.classList.toggle('active', card.getAttribute('data-section') === section);
                });
                var cardRefunds = document.getElementById('tx-section-refunds');
                var cardPayouts = document.getElementById('tx-section-payouts');
                if (cardRefunds) cardRefunds.closest('.card').style.display = section === 'refunds' ? '' : 'none';
                if (cardPayouts) cardPayouts.closest('.card').style.display = section === 'payouts' ? '' : 'none';
                applyTxFilter();
            });
        });
        // Transaction Management: filter by name
        document.querySelectorAll('#transaction-management .tx-filter').forEach(function(span) {
            span.addEventListener('click', function() {
                document.querySelectorAll('#transaction-management .tx-filter').forEach(function(s) { s.classList.remove('active'); });
                this.classList.add('active');
                applyTxFilter();
            });
        });
        function applyTxFilter() {
            var status = (document.querySelector('#transaction-management .tx-filter.active') || {}).getAttribute('data-filter-status') || '';
            document.querySelectorAll('#transaction-management .tx-section-card.active tr[data-filter-status]').forEach(function(row) {
                var rowStatus = (row.getAttribute('data-filter-status') || '').toLowerCase();
                row.style.display = (status === '' || rowStatus === status) ? '' : 'none';
            });
            applyTransactionServiceTypeFilter();
            applyPayoutFilters();
        }
        // Transaction Dashboard: filter by service type
        var transactionServiceTypeEl = document.getElementById('transactionServiceTypeFilter');
        if (transactionServiceTypeEl) {
            transactionServiceTypeEl.addEventListener('change', applyTransactionServiceTypeFilter);
        }
        function applyTransactionServiceTypeFilter() {
            var select = document.getElementById('transactionServiceTypeFilter');
            if (!select) return;
            var value = (select.value || '').trim();
            var status = (document.querySelector('#transaction-management .tx-filter.active') || {}).getAttribute('data-filter-status') || '';
            var rows = document.querySelectorAll('#tx-section-transactions tr[data-service-type]');
            rows.forEach(function(row) {
                var rowType = (row.getAttribute('data-service-type') || '').trim();
                var rowStatus = (row.getAttribute('data-filter-status') || '').toLowerCase();
                var typeMatch = value === '' || rowType === value;
                var statusMatch = status === '' || rowStatus === status;
                row.style.display = (typeMatch && statusMatch) ? '' : 'none';
            });
        }
        // Refund Requests: filter by status (All / Pending / Approved / Rejected)
        var refundStatusEl = document.getElementById('filter-refund-status');
        if (refundStatusEl) {
            refundStatusEl.addEventListener('change', applyRefundStatusFilter);
        }
        function applyRefundStatusFilter() {
            var select = document.getElementById('filter-refund-status');
            if (!select) return;
            var value = (select.value || '').trim().toLowerCase();
            var rows = document.querySelectorAll('#tx-sub-refunds tbody tr[data-filter-status]');
            rows.forEach(function(row) {
                var rowStatus = (row.getAttribute('data-filter-status') || '').toLowerCase();
                var match = value === '' || value === rowStatus || (value === 'approved' && (rowStatus === 'approved' || rowStatus === 'processed'));
                row.style.display = match ? '' : 'none';
            });
        }
        // Payout Management: filter by service type (All / Driver / Guide)
        var payoutServiceTypeEl = document.getElementById('filter-payout-type');
        if (payoutServiceTypeEl) {
            payoutServiceTypeEl.addEventListener('change', applyPayoutFilters);
        }
        var payoutStatusEl = document.getElementById('filter-payout-status');
        if (payoutStatusEl) {
            payoutStatusEl.addEventListener('change', applyPayoutFilters);
        }
        function applyPayoutFilters() {
            var typeSelect = document.getElementById('filter-payout-type');
            var statusSelect = document.getElementById('filter-payout-status');
            if (!typeSelect || !statusSelect) return;
            var typeValue = (typeSelect.value || '').trim().toLowerCase();
            var statusValue = (statusSelect.value || '').trim().toLowerCase();
            var rows = document.querySelectorAll('#tx-sub-payouts tbody tr[data-service-type]');
            rows.forEach(function(row) {
                var rowType = (row.getAttribute('data-service-type') || '').trim().toLowerCase();
                var rowStatus = (row.getAttribute('data-payout-status') || '').trim().toLowerCase();
                var typeMatch = typeValue === '' || rowType === typeValue;
                var statusMatch = statusValue === '' || rowStatus === statusValue;
                row.style.display = (typeMatch && statusMatch) ? '' : 'none';
            });
        }
        // Commission Table: save commission rates (connect backend to persist)
        document.querySelectorAll('.btn-save-commission').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var row = this.closest('tr');
                var role = row ? row.getAttribute('data-commission-role') : '';
                var input = document.getElementById('commission-' + role);
                var value = input ? input.value : '';
                if (role && value !== '') {
                    // TODO: POST to backend e.g. /BuisManager/updateCommission { role: role, rate: value }
                    alert('Commission for ' + role + ' set to ' + value + '%. Connect backend to save.');
                }
            });
        });
        // Table search: generic filter by text
        document.querySelectorAll('.table-toolbar .search-input').forEach(function(input) {
            input.addEventListener('input', function() {
                var table = this.closest('.card').querySelector('.data-table');
                if (!table) return;
                var q = this.value.trim().toLowerCase();
                var rows = table.querySelectorAll('tbody tr');
                rows.forEach(function(row) {
                    var text = row.textContent.replace(/\s+/g, ' ').toLowerCase();
                    row.style.display = (q === '' || text.indexOf(q) !== -1) ? '' : 'none';
                });
            });
        });
        // Header action buttons (Notifications, Messages)
        var notificationsBtn = document.getElementById('notificationsBtn');
        var messagesBtn = document.getElementById('messagesBtn');
        if (notificationsBtn) notificationsBtn.addEventListener('click', function(e) { e.preventDefault(); alert('Notifications'); });
        if (messagesBtn) messagesBtn.addEventListener('click', function(e) { e.preventDefault(); alert('Messages'); });
        // Initialize dashboard
        switchTab('dashboard');
    </script>
</body>
</html>
