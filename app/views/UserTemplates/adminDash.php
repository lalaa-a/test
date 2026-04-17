<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=monitor_heart" />
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@400;600;700&family=Roboto:wght@400;600&family=Poppins:wght@400&family=Inter:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARS40V0wUMA2Y3wKorMNNof1eD6wixViE&loading=async" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
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
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
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
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
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

        /* Admin Profile Settings */
        .profile-settings-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 23, 42, 0.45);
            z-index: 10002;
        }

        .profile-settings-modal.show {
            display: flex;
        }

        .profile-settings-panel {
            width: min(960px, 100%);
            max-height: 92vh;
            overflow-y: auto;
            background: white;
            border-radius: 20px;
            border: 1px solid #e9ecef;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
        }

        .profile-settings-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding: 26px 30px 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .profile-settings-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
            margin-bottom: 6px;
        }

        .profile-settings-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .profile-settings-close {
            width: 42px;
            height: 42px;
            border: 1px solid #dbe4ea;
            background: white;
            color: var(--primary);
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.05rem;
            transition: all 0.2s ease;
        }

        .profile-settings-close:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .profile-settings-top {
            display: flex;
            align-items: center;
            gap: 24px;
            padding: 26px 30px;
            background: linear-gradient(135deg, #006A71 0%, #0b8a92 100%);
            color: white;
        }

        .profile-settings-avatar {
            width: 108px;
            height: 108px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.9);
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.18);
        }

        .profile-settings-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }

        .profile-settings-avatar span {
            font-size: 2.6rem;
            font-weight: 700;
            letter-spacing: 0.04em;
        }

        .profile-settings-top h3 {
            font-size: 1.6rem;
            margin-bottom: 6px;
        }

        .profile-settings-role {
            opacity: 0.92;
            margin-bottom: 14px;
            text-transform: capitalize;
        }

        .profile-settings-photo-input {
            display: none;
        }

        .profile-settings-photo-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.55);
            background: rgba(255, 255, 255, 0.14);
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .profile-settings-photo-btn:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        .profile-settings-form {
            padding: 28px 30px 32px;
        }

        .profile-settings-feedback {
            display: none;
            margin-bottom: 20px;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 0.95rem;
            border: 1px solid transparent;
        }

        .profile-settings-feedback.show {
            display: block;
        }

        .profile-settings-feedback.success {
            background: #e8f8ef;
            color: #176b3a;
            border-color: #b8e2c7;
        }

        .profile-settings-feedback.error {
            background: #fff1f2;
            color: #b42318;
            border-color: #fecdd3;
        }

        .profile-settings-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px 20px;
        }

        .profile-settings-group.full {
            grid-column: 1 / -1;
        }

        .profile-settings-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-size: 0.92rem;
            font-weight: 600;
        }

        .profile-settings-group input,
        .profile-settings-group select,
        .profile-settings-group textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d7e0e6;
            border-radius: 12px;
            background: #fbfdfe;
            color: var(--text-color);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .profile-settings-group input:focus,
        .profile-settings-group select:focus,
        .profile-settings-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 106, 113, 0.12);
            background: white;
        }

        .profile-settings-group textarea {
            min-height: 110px;
            resize: vertical;
        }

        .profile-settings-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 28px;
        }

        .btn-secondary {
            background: #e8eef1;
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: #d9e5e9;
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

            .profile-settings-modal {
                padding: 16px;
            }

            .profile-settings-header,
            .profile-settings-top,
            .profile-settings-form {
                padding-left: 20px;
                padding-right: 20px;
            }

            .profile-settings-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-settings-grid {
                grid-template-columns: 1fr;
            }

            .profile-settings-group.full {
                grid-column: auto;
            }

            .profile-settings-actions {
                flex-direction: column-reverse;
            }

            .profile-settings-actions .btn {
                width: 100%;
            }
        }
    </style>

</head>

<body>
    <?php
    $tabId = $tabId ?? 'dashboard';
    $loadingContent = $loadingContent ?? [
        'html' => '<div class="card"><div class="card-header"><h2>Admin Dashboard</h2></div><p>The admin shell is ready.</p></div>',
        'css' => null,
        'js' => null
    ];
    ?>
    <?php
    $adminUser = getLoggedInUser();
    $adminProfilePhoto = !empty($adminUser['profile_photo']) ? URL_ROOT . '/public/uploads/' . $adminUser['profile_photo'] : '';
    $adminDisplayName = $adminUser['fullname'] ?? 'Admin';
    $adminInitial = strtoupper(substr($adminDisplayName, 0, 1));
    ?>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div>
                <img src="<?php echo IMG_ROOT . '/logo/logo design 1(2).png' ?>" alt="Logo" class="sidebar-logo">
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="<?php echo URL_ROOT . '/admin/dashboard' ?>" class="active" data-tab="dashboard"><i class="fa-solid fa-gauge-high"></i><span>Dashboard</span></a></li>
            <li><a href="<?php echo URL_ROOT . '/admin/moderator' ?>" data-tab="moderator"><i class="fa-solid fa-user-tie"></i><span>Moderator Info</span></a></li>
            <li><a href="<?php echo URL_ROOT . '/admin/userInfo' ?>" data-tab="userInfo"><i class="fa-solid fa-users"></i><span>User Info</span></a></li>
            <li><a href="<?php echo URL_ROOT . '/admin/tripInfo' ?>" data-tab="tripInfo"><i class="fa-solid fa-suitcase-rolling"></i></i><span>Trip Info</span></a></li>
            <li><a href="<?php echo URL_ROOT . '/admin/verification' ?>" data-tab="verification"><i class="fa-solid fa-circle-check"></i> <span>Verification</span></a></li>
            <li><a href="<?php echo URL_ROOT . '/admin/content' ?>" data-tab="content"><i class="fa-solid fa-folder-plus"></i> <span>Content</span></a></li>
            <li><a href="<?php echo URL_ROOT . '/admin/oversight' ?>" data-tab="oversight"><i class="fa-solid fa-eye"></i> <span>Oversight</span></a></li>
            <li><a href="<?php echo URL_ROOT . '/admin/transaction' ?>" data-tab="transaction"><i class="fas fa-money-bill-transfer"></i> <span>Transaction</span></a></li>
            <li><a href="<?php echo URL_ROOT . '/admin/analytics' ?>" data-tab="analytics"><i class="fa-solid fa-chart-line"></i><span>Analytics</span></a></li>
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
                    <span class="sidebar-user-role">Admin</span>
                </div>
                <i class="fas fa-chevron-up sidebar-dropdown-icon"></i>
                <div class="sidebar-dropdown-menu" id="sidebarUserDropdown">
                    <a href="#" class="sidebar-dropdown-item" id="sidebarProfileSettingsBtn">
                        <i class="fas fa-cog"></i> Profile Settings
                    </a>
                    <a href="<?php echo URL_ROOT . '/admin' ?>" class="sidebar-dropdown-item">
                        <i class="fas fa-house"></i> Dashboard Home
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
            <h1 id="adminWelcomeMessage">Hello <?php echo htmlspecialchars($adminDisplayName) . ' Welcome Back!' ?></h1>

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

        <div id="adminProfileSettingsModal" class="profile-settings-modal" aria-hidden="true">
            <div class="profile-settings-panel" role="dialog" aria-modal="true" aria-labelledby="adminProfileSettingsTitle">
                <div class="profile-settings-header">
                    <div>
                        <h2 id="adminProfileSettingsTitle">Profile Settings</h2>
                        <p>Update your admin account details using the same dashboard style.</p>
                    </div>
                    <button type="button" class="profile-settings-close" id="closeAdminProfileSettingsBtn" aria-label="Close profile settings">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="profile-settings-top">
                    <div class="profile-settings-avatar">
                        <img
                            id="profileSettingsPhotoPreview"
                            src="<?php echo htmlspecialchars($adminProfilePhoto ?: URL_ROOT . '/public/img/default-avatar.png'); ?>"
                            alt="Admin profile photo">
                        <span id="profileSettingsPhotoInitial"><?php echo htmlspecialchars($adminInitial); ?></span>
                    </div>
                    <div>
                        <h3 id="profileSettingsDisplayName"><?php echo htmlspecialchars($adminDisplayName); ?></h3>
                        <p class="profile-settings-role"><?php echo htmlspecialchars($adminUser['account_type'] ?? 'admin'); ?></p>
                        <label for="profileSettingsPhotoInput" class="profile-settings-photo-btn">
                            <i class="fas fa-camera"></i>
                            Change Photo
                        </label>
                        <input type="file" id="profileSettingsPhotoInput" class="profile-settings-photo-input" accept="image/png,image/jpeg,image/jpg">
                    </div>
                </div>

                <form id="adminProfileSettingsForm" class="profile-settings-form">
                    <div id="adminProfileSettingsFeedback" class="profile-settings-feedback" aria-live="polite"></div>

                    <div class="profile-settings-grid">
                        <div class="profile-settings-group">
                            <label for="adminProfileFullname">Full Name</label>
                            <input type="text" id="adminProfileFullname" name="fullname" value="<?php echo htmlspecialchars($adminUser['fullname'] ?? ''); ?>" required>
                        </div>

                        <div class="profile-settings-group">
                            <label for="adminProfileEmail">Email Address</label>
                            <input type="email" id="adminProfileEmail" name="email" value="<?php echo htmlspecialchars($adminUser['email'] ?? ''); ?>" required>
                        </div>

                        <div class="profile-settings-group">
                            <label for="adminProfilePhone">Phone Number</label>
                            <input type="text" id="adminProfilePhone" name="phone" value="<?php echo htmlspecialchars($adminUser['phone'] ?? ''); ?>" required>
                        </div>

                        <div class="profile-settings-group">
                            <label for="adminProfileSecondaryPhone">Secondary Phone</label>
                            <input type="text" id="adminProfileSecondaryPhone" name="secondary_phone" value="<?php echo htmlspecialchars($adminUser['secondary_phone'] ?? ''); ?>">
                        </div>

                        <div class="profile-settings-group">
                            <label for="adminProfileLanguage">Language</label>
                            <select id="adminProfileLanguage" name="language" required>
                                <option value="">Select language</option>
                                <option value="English" <?php echo (($adminUser['language'] ?? '') === 'English') ? 'selected' : ''; ?>>English</option>
                                <option value="Sinhala" <?php echo (($adminUser['language'] ?? '') === 'Sinhala') ? 'selected' : ''; ?>>Sinhala</option>
                                <option value="Tamil" <?php echo (($adminUser['language'] ?? '') === 'Tamil') ? 'selected' : ''; ?>>Tamil</option>
                                <option value="Other" <?php echo (($adminUser['language'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="profile-settings-group">
                            <label for="adminProfileGender">Gender</label>
                            <select id="adminProfileGender" name="gender" required>
                                <option value="">Select gender</option>
                                <option value="Male" <?php echo (($adminUser['gender'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (($adminUser['gender'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo (($adminUser['gender'] ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                                <option value="Prefer not to say" <?php echo (($adminUser['gender'] ?? '') === 'Prefer not to say') ? 'selected' : ''; ?>>Prefer not to say</option>
                            </select>
                        </div>

                        <div class="profile-settings-group">
                            <label for="adminProfileDob">Date of Birth</label>
                            <input type="date" id="adminProfileDob" name="dob" value="<?php echo htmlspecialchars($adminUser['dob'] ?? ''); ?>" required>
                        </div>

                        <div class="profile-settings-group full">
                            <label for="adminProfileAddress">Address</label>
                            <textarea id="adminProfileAddress" name="address" required><?php echo htmlspecialchars($adminUser['address'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="profile-settings-actions">
                        <button type="button" class="btn btn-secondary" id="cancelAdminProfileSettingsBtn">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveAdminProfileSettingsBtn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            let isLoggedIn = false;
            let currentUserName = <?php echo json_encode($adminDisplayName) ?>;
            const loggedInState = <?php echo isLoggedIn() ? 'true' : 'false' ?>;

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
            const adminWelcomeMessage = document.getElementById('adminWelcomeMessage');
            const adminProfileSettingsModal = document.getElementById('adminProfileSettingsModal');
            const closeAdminProfileSettingsBtn = document.getElementById('closeAdminProfileSettingsBtn');
            const cancelAdminProfileSettingsBtn = document.getElementById('cancelAdminProfileSettingsBtn');
            const adminProfileSettingsForm = document.getElementById('adminProfileSettingsForm');
            const adminProfileSettingsFeedback = document.getElementById('adminProfileSettingsFeedback');
            const saveAdminProfileSettingsBtn = document.getElementById('saveAdminProfileSettingsBtn');
            const profileSettingsDisplayName = document.getElementById('profileSettingsDisplayName');
            const profileSettingsPhotoInput = document.getElementById('profileSettingsPhotoInput');
            const profileSettingsPhotoPreview = document.getElementById('profileSettingsPhotoPreview');
            const profileSettingsPhotoInitial = document.getElementById('profileSettingsPhotoInitial');

            let encodedData;
            let userProfilePhoto = '';

            document.addEventListener('DOMContentLoaded', function() {

                encodedData = <?php echo json_encode($loadingContent) ?>;
                const tabId = <?php echo json_encode($tabId) ?>;
                // Expose logged-in user's profile photo URL to JS (empty string if none)
                userProfilePhoto = '<?php echo !empty(getLoggedInUser()["profile_photo"]) ? URL_ROOT . "/public/uploads/" . getLoggedInUser()["profile_photo"] : "" ?>';

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

                    cleanupPreviousAssets(tabId);

                    // Inject HTML
                    tabElement.innerHTML = data.html;

                    if (data.css) {
                        appendCSS(data.css, tabId)
                    }

                    if (data.js) {
                        appendJS(data.js, tabId);
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

                if (currentTabId.startsWith("subtab")) {
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
                console.log('this exists ', existingLink);

                console.log("adding id " + tabId);

                if (existingLink) {
                    existingLink.remove();
                    console.log("existing css ", existingLink);
                }

                // Create new link element
                const linkElement = document.createElement('link');
                linkElement.rel = 'stylesheet';
                linkElement.type = 'text/css';
                linkElement.href = url + (url.includes('?') ? '&' : '?') + 'v=' + Date.now();
                linkElement.setAttribute('data-tab', tabId);
                document.head.appendChild(linkElement);

            }

            function appendJS(url, tabId) {

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
                window.location.href = '<?php echo URL_ROOT . '/User/login' ?>';
            });

            //Register button
            sidebarSignupBtn.addEventListener('click', function() {
                window.location.href = '<?php echo URL_ROOT . '/User/register' ?>';
            });

            // Logout
            sidebarLogoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('🔒 Are you sure you want to log out?')) {
                    window.location.href = '<?php echo URL_ROOT . '/User/logout' ?>';
                }
            });

            sidebarProfileSettingsBtn.addEventListener('click', function(e) {
                e.preventDefault();
                openAdminProfileSettingsModal();
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
            if (loggedInState) {
                simulateNotifications();
            }




            //To update the username and profile displaying
            function updateUI() {

                if (loggedInState) {
                    sidebarAuthContainer.style.display = 'none';
                    sidebarUserContainer.style.display = 'flex';

                    // If a profile photo URL is available, render the image inside the avatar container
                    if (userProfilePhoto && userProfilePhoto.trim() !== '') {
                        sidebarUserAvatar.innerHTML = `<img src="${userProfilePhoto}" alt="Avatar" style="width:38px;height:38px;border-radius:50%;object-fit:cover;">`;
                    } else {
                        const initial = currentUserName.charAt(0).toUpperCase();
                        sidebarUserAvatar.textContent = initial;
                    }

                    sidebarUserName.textContent = currentUserName;
                    if (adminWelcomeMessage) {
                        adminWelcomeMessage.textContent = `Hello ${currentUserName} Welcome Back!`;
                    }
                } else {
                    sidebarAuthContainer.style.display = 'flex';
                    sidebarUserContainer.style.display = 'none';
                    closeDropdown();
                }
            }

            function setProfileSettingsFeedback(message, type) {
                if (!adminProfileSettingsFeedback) {
                    return;
                }

                adminProfileSettingsFeedback.textContent = message;
                adminProfileSettingsFeedback.className = `profile-settings-feedback show ${type}`;
            }

            function clearProfileSettingsFeedback() {
                if (!adminProfileSettingsFeedback) {
                    return;
                }

                adminProfileSettingsFeedback.textContent = '';
                adminProfileSettingsFeedback.className = 'profile-settings-feedback';
            }

            function renderProfileSettingsAvatar() {
                const initial = (currentUserName || 'A').charAt(0).toUpperCase();

                if (profileSettingsDisplayName) {
                    profileSettingsDisplayName.textContent = currentUserName;
                }

                if (profileSettingsPhotoInitial) {
                    profileSettingsPhotoInitial.textContent = initial;
                }

                if (userProfilePhoto && userProfilePhoto.trim() !== '') {
                    profileSettingsPhotoPreview.src = userProfilePhoto;
                    profileSettingsPhotoPreview.style.display = 'block';
                    profileSettingsPhotoInitial.style.display = 'none';
                } else {
                    profileSettingsPhotoPreview.style.display = 'none';
                    profileSettingsPhotoInitial.style.display = 'block';
                }
            }

            function openAdminProfileSettingsModal() {
                clearProfileSettingsFeedback();
                renderProfileSettingsAvatar();
                adminProfileSettingsModal.classList.add('show');
                adminProfileSettingsModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                closeDropdown();
            }

            function closeAdminProfileSettingsModal() {
                adminProfileSettingsModal.classList.remove('show');
                adminProfileSettingsModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                if (profileSettingsPhotoInput) {
                    profileSettingsPhotoInput.value = '';
                }
            }

            closeAdminProfileSettingsBtn.addEventListener('click', closeAdminProfileSettingsModal);
            cancelAdminProfileSettingsBtn.addEventListener('click', closeAdminProfileSettingsModal);

            adminProfileSettingsModal.addEventListener('click', function(e) {
                if (e.target === adminProfileSettingsModal) {
                    closeAdminProfileSettingsModal();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && adminProfileSettingsModal.classList.contains('show')) {
                    closeAdminProfileSettingsModal();
                }
            });

            profileSettingsPhotoInput.addEventListener('change', async function(e) {
                const file = e.target.files[0];

                if (!file) {
                    return;
                }

                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    setProfileSettingsFeedback('Please select a JPG or PNG image.', 'error');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    setProfileSettingsFeedback('Image size must be less than 5MB.', 'error');
                    return;
                }

                const formData = new FormData();
                formData.append('profile_photo', file);

                try {
                    const response = await fetch('<?php echo URL_ROOT ?>/User/updateProfilePhoto', {
                        method: 'POST',
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        userProfilePhoto = data.new_photo_url || userProfilePhoto;
                        renderProfileSettingsAvatar();
                        updateUI();
                        setProfileSettingsFeedback('Profile photo updated successfully.', 'success');
                        showNotification('Profile photo updated successfully.', 'success');
                    } else {
                        setProfileSettingsFeedback(data.message || 'Failed to update profile photo.', 'error');
                    }
                } catch (error) {
                    console.error('Error updating profile photo:', error);
                    setProfileSettingsFeedback('An error occurred while updating the profile photo.', 'error');
                }
            });

            adminProfileSettingsForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                clearProfileSettingsFeedback();

                saveAdminProfileSettingsBtn.disabled = true;
                saveAdminProfileSettingsBtn.textContent = 'Saving...';

                const formData = new FormData(adminProfileSettingsForm);
                const payload = Object.fromEntries(formData.entries());

                try {
                    const response = await fetch('<?php echo URL_ROOT ?>/User/updateAccount', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await response.json();

                    if (data.success) {
                        currentUserName = payload.fullname;
                        updateUI();
                        renderProfileSettingsAvatar();
                        setProfileSettingsFeedback('Profile settings updated successfully.', 'success');
                        showNotification('Profile settings updated successfully.', 'success');
                    } else {
                        setProfileSettingsFeedback(data.message || 'Failed to update profile settings.', 'error');
                    }
                } catch (error) {
                    console.error('Error updating profile settings:', error);
                    setProfileSettingsFeedback('An error occurred while saving your profile settings.', 'error');
                } finally {
                    saveAdminProfileSettingsBtn.disabled = false;
                    saveAdminProfileSettingsBtn.textContent = 'Save Changes';
                }
            });


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
</body>

</html>
