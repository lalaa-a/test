<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Business Manager Dashboard</title>
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
            overflow-y: auto; /* 👈 ENABLES SCROLLING IN SIDEBAR */
        }
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 20px;
            text-align: center;
        }
        .sidebar-header h2 {
            font-size: 1.4rem;
            font-weight: 600;
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
        /* Analytics Charts Placeholder */
        .chart-container {
            height: 300px;
            background: #f8f9fa;
            border: 2px dashed #e9ecef;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #888;
            margin-bottom: 20px;
        }
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-header h2,
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
            <h2>Business Manager</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" class="active" data-tab="dashboard"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
            <li><a href="#" data-tab="transactions"><i class="fas fa-file-invoice-dollar"></i> <span>Transactions</span></a></li>
            <li><a href="#" data-tab="refunds"><i class="fas fa-hand-holding-usd"></i> <span>Refunds & Adjustments</span></a></li>
            <li><a href="#" data-tab="payouts"><i class="fas fa-money-bill-wave"></i> <span>Payout Management</span></a></li>
            <li><a href="#" data-tab="commissions"><i class="fas fa-percentage"></i> <span>Commission Rates</span></a></li>
            <li><a href="#" data-tab="financial-reports"><i class="fas fa-chart-line"></i> <span>Financial Reports</span></a></li>
            <li><a href="#" data-tab="analytics"><i class="fas fa-chart-bar"></i> <span>Analytics Dashboard</span></a></li>
            <li><a href="#" data-tab="sales-reports"><i class="fas fa-shopping-cart"></i> <span>Sales Performance</span></a></li>
            <li><a href="#" data-tab="customer-analysis"><i class="fas fa-users"></i> <span>Customer Behavior</span></a></li>
            <li><a href="#" data-tab="planned-trips"><i class="fas fa-calendar-check"></i> <span>Planned Trips</span></a></li>
            <li><a href="#" data-tab="internal-chat"><i class="fas fa-comments"></i> <span>Internal Chat</span></a></li>
            <li><a href="#" data-tab="profile"><i class="fas fa-user-cog"></i> <span>Profile Settings</span></a></li>
        </ul>
    </div>
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Business Manager Dashboard</h1>
            <div class="user-info">
                <?php
                $user = getLoggedInUser();
                $firstInitial = !empty($user['fullname']) ? strtoupper(substr($user['fullname'], 0, 1)) : 'B';
                ?>
                <div class="user-avatar"><?= $firstInitial ?></div>
                <span><?= htmlspecialchars($user['fullname'] ?? 'Business Manager') ?></span>
                <button class="logout-btn" onclick="window.location.href='<?php echo URL_ROOT; ?>/user/logout'">Logout</button>
            </div>
        </div>
        <!-- Dashboard Content -->
        <div class="dashboard-content active" id="dashboard">
            <!-- Stats Overview -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon revenue">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-number">$247,890</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon payouts">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-number">$89,450</div>
                    <div class="stat-label">Pending Payouts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon refunds">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="stat-number">$12,340</div>
                    <div class="stat-label">Refund Requests</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon commissions">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="stat-number">15%</div>
                    <div class="stat-label">Platform Commission</div>
                </div>
            </div>
            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h2>Recent Financial Activity</h2>
                </div>
                <div style="display: grid; gap: 15px;">
                    <div style="padding: 15px; border-left: 4px solid var(--primary); background: #f8f9fa; border-radius: 0 8px 8px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <strong>New Refund Request</strong>
                            <small>2 hours ago</small>
                        </div>
                        <p>Sarah Wilson requested cancellation of itinerary ITN-005 due to personal emergency.</p>
                    </div>
                    <div style="padding: 15px; border-left: 4px solid #4CAF50; background: #f8f9fa; border-radius: 0 8px 8px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <strong>Payout Processed</strong>
                            <small>5 hours ago</small>
                        </div>
                        <p>Successfully processed $2,450 in payouts to guides and drivers for completed services.</p>
                    </div>
                    <div style="padding: 15px; border-left: 4px solid #2196F3; background: #f8f9fa; border-radius: 0 8px 8px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <strong>Commission Updated</strong>
                            <small>1 day ago</small>
                        </div>
                        <p>Updated platform commission rate for guide services from 12% to 15%.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Transactions -->
        <div class="dashboard-content" id="transactions">
            <div class="card">
                <div class="card-header">
                    <h2>Transaction Dashboard</h2>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>TXN-001</td>
                            <td>John Doe</td>
                            <td>$1,200</td>
                            <td>Itinerary Booking</td>
                            <td>2024-01-15</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                            <td>
                                <button class="btn btn-info btn-sm">View Details</button>
                            </td>
                        </tr>
                        <tr>
                            <td>TXN-002</td>
                            <td>Jane Smith</td>
                            <td>$800</td>
                            <td>Guide Service</td>
                            <td>2024-01-14</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                            <td>
                                <button class="btn btn-info btn-sm">View Details</button>
                            </td>
                        </tr>
                        <tr>
                            <td>TXN-003</td>
                            <td>Mike Johnson</td>
                            <td>$450</td>
                            <td>Driver Service</td>
                            <td>2024-01-13</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-warning btn-sm">Process</button>
                            </td>
                        </tr>
                        <tr>
                            <td>TXN-004</td>
                            <td>Sarah Wilson</td>
                            <td>$2,500</td>
                            <td>Itinerary Booking</td>
                            <td>2024-01-12</td>
                            <td><span class="status-badge status-pending">Cancellation Request</span></td>
                            <td>
                                <button class="btn btn-refund btn-sm">Process Refund</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Refunds & Adjustments -->
        <div class="dashboard-content" id="refunds">
            <div class="card">
                <div class="card-header">
                    <h2>Process Refunds & Adjustments</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>User</th>
                            <th>Itinerary</th>
                            <th>Amount</th>
                            <th>Reason</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>REF-001</td>
                            <td>Sarah Wilson</td>
                            <td>ITN-005</td>
                            <td>$2,500</td>
                            <td>Personal emergency</td>
                            <td>1 day ago</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-success btn-sm">Approve Full</button>
                                <button class="btn btn-warning btn-sm">Partial Refund</button>
                                <button class="btn btn-danger btn-sm">Reject</button>
                            </td>
                        </tr>
                        <tr>
                            <td>REF-002</td>
                            <td>Robert Davis</td>
                            <td>ITN-008</td>
                            <td>$1,800</td>
                            <td>Poor service quality</td>
                            <td>2 days ago</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-success btn-sm">Approve Full</button>
                                <button class="btn btn-warning btn-sm">Partial Refund</button>
                                <button class="btn btn-danger btn-sm">Reject</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Payout Management -->
        <div class="dashboard-content" id="payouts">
            <div class="card">
                <div class="card-header">
                    <h2>Payout Management</h2>
                </div>
                <div class="form-group">
                    <label>Filter by Service Type</label>
                    <select>
                        <option>All Services</option>
                        <option>Guide Services</option>
                        <option>Driver Services</option>
                        <option>Package Bookings</option>
                    </select>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Provider</th>
                            <th>Service Type</th>
                            <th>Earnings</th>
                            <th>Commission</th>
                            <th>Net Payout</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sarah Wilson</td>
                            <td>Guide Service</td>
                            <td>$800</td>
                            <td>$120 (15%)</td>
                            <td>$680</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-payout btn-sm">Process Payout</button>
                            </td>
                        </tr>
                        <tr>
                            <td>David Brown</td>
                            <td>Driver Service</td>
                            <td>$450</td>
                            <td>$67.50 (15%)</td>
                            <td>$382.50</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <button class="btn btn-payout btn-sm">Process Payout</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Jane Smith</td>
                            <td>Guide Service</td>
                            <td>$1,200</td>
                            <td>$180 (15%)</td>
                            <td>$1,020</td>
                            <td><span class="status-badge status-completed">Completed</span></td>
                            <td>
                                <button class="btn btn-info btn-sm">View Details</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="form-actions" style="margin-top: 20px;">
                    <button class="btn btn-payout">Process Selected Payouts</button>
                    <button class="btn btn-primary">Generate Payout Report</button>
                </div>
            </div>
        </div>
        <!-- Commission Rates -->
        <div class="dashboard-content" id="commissions">
            <div class="card">
                <div class="card-header">
                    <h2>Set & Adjust Commission Rates</h2>
                </div>
                <div class="form-group">
                    <label>Service Type</label>
                    <select id="serviceType">
                        <option value="itinerary">Itinerary Bookings</option>
                        <option value="guide">Guide Services</option>
                        <option value="driver">Driver Services</option>
                        <option value="package">Travel Packages</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Current Commission Rate</label>
                    <input type="number" id="currentRate" value="15" min="0" max="100" step="0.1">%
                </div>
                <div class="form-group">
                    <label>New Commission Rate</label>
                    <input type="number" id="newRate" value="15" min="0" max="100" step="0.1">%
                </div>
                <div class="form-group">
                    <label>Effective Date</label>
                    <input type="date" id="effectiveDate">
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea id="commissionNotes" rows="3" placeholder="Enter reason for commission change..."></textarea>
                </div>
                <div class="form-actions">
                    <button class="btn btn-commission">Update Commission Rate</button>
                    <button class="btn btn-secondary">Cancel</button>
                </div>
            </div>
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h2>Commission History</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Service Type</th>
                            <th>Old Rate</th>
                            <th>New Rate</th>
                            <th>Effective Date</th>
                            <th>Changed By</th>
                            <th>Date Changed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Guide Services</td>
                            <td>12%</td>
                            <td>15%</td>
                            <td>2024-01-15</td>
                            <td>Michael Rodriguez</td>
                            <td>2024-01-10</td>
                        </tr>
                        <tr>
                            <td>Itinerary Bookings</td>
                            <td>10%</td>
                            <td>12%</td>
                            <td>2024-01-01</td>
                            <td>Admin User</td>
                            <td>2023-12-28</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Financial Reports -->
        <div class="dashboard-content" id="financial-reports">
            <div class="card">
                <div class="card-header">
                    <h2>Financial Reporting</h2>
                </div>
                <div class="form-group">
                    <label>Report Type</label>
                    <select id="reportType">
                        <option>Revenue Report</option>
                        <option>Tax Report</option>
                        <option>Commission Report</option>
                        <option>Profit/Loss Statement</option>
                        <option>Monthly Summary</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date Range</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="date" id="startDate" style="flex: 1;">
                        <input type="date" id="endDate" style="flex: 1;">
                    </div>
                </div>
                <div class="form-group">
                    <label>Format</label>
                    <select id="reportFormat">
                        <option>PDF</option>
                        <option>Excel</option>
                        <option>CSV</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button class="btn btn-report">Generate Report</button>
                    <button class="btn btn-primary">View Sample</button>
                </div>
            </div>
            <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h2>Recent Reports</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Report Name</th>
                            <th>Type</th>
                            <th>Date Generated</th>
                            <th>Period</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>January 2024 Revenue Report</td>
                            <td>Revenue Report</td>
                            <td>2024-01-15</td>
                            <td>Jan 1 - Jan 15, 2024</td>
                            <td>
                                <button class="btn btn-info btn-sm">View</button>
                                <button class="btn btn-primary btn-sm">Download</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Q4 2023 Profit/Loss</td>
                            <td>Profit/Loss Statement</td>
                            <td>2024-01-10</td>
                            <td>Oct 1 - Dec 31, 2023</td>
                            <td>
                                <button class="btn btn-info btn-sm">View</button>
                                <button class="btn btn-primary btn-sm">Download</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Analytics Dashboard -->
        <div class="dashboard-content" id="analytics">
            <div class="card">
                <div class="card-header">
                    <h2>Analytics Dashboard</h2>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-number">$41,315</div>
                        <div class="stat-label">Monthly Recurring Revenue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon payouts">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number">$89</div>
                        <div class="stat-label">Customer Acquisition Cost</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon refunds">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-number">$1,247</div>
                        <div class="stat-label">Avg. Booking Value</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon commissions">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-number">24%</div>
                        <div class="stat-label">Conversion Rate</div>
                    </div>
                </div>
                <div class="chart-container">
                    <i class="fas fa-chart-bar"></i> Revenue Trend Chart (Last 12 Months)
                </div>
                <div class="chart-container">
                    <i class="fas fa-chart-pie"></i> Service Distribution Chart
                </div>
            </div>
        </div>
        <!-- Sales Performance -->
        <div class="dashboard-content" id="sales-reports">
            <div class="card">
                <div class="card-header">
                    <h2>Sales Performance Reports</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Destination</th>
                            <th>Itinerary Type</th>
                            <th>Bookings</th>
                            <th>Revenue</th>
                            <th>Profit Margin</th>
                            <th>Popularity Rank</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Paris, France</td>
                            <td>City Tour</td>
                            <td>47</td>
                            <td>$56,400</td>
                            <td>32%</td>
                            <td>1</td>
                        </tr>
                        <tr>
                            <td>Rome, Italy</td>
                            <td>Historical Tour</td>
                            <td>38</td>
                            <td>$45,600</td>
                            <td>28%</td>
                            <td>2</td>
                        </tr>
                        <tr>
                            <td>Barcelona, Spain</td>
                            <td>Architecture Tour</td>
                            <td>32</td>
                            <td>$38,400</td>
                            <td>35%</td>
                            <td>3</td>
                        </tr>
                        <tr>
                            <td>Amsterdam, Netherlands</td>
                            <td>Bike Tour</td>
                            <td>29</td>
                            <td>$34,800</td>
                            <td>41%</td>
                            <td>4</td>
                        </tr>
                    </tbody>
                </table>
                <div class="chart-container" style="margin-top: 20px;">
                    <i class="fas fa-chart-line"></i> Top 10 Destinations by Revenue
                </div>
            </div>
        </div>
        <!-- Customer Behavior -->
        <div class="dashboard-content" id="customer-analysis">
            <div class="card">
                <div class="card-header">
                    <h2>Customer Behavior Analysis</h2>
                </div>
                <div class="chart-container">
                    <i class="fas fa-funnel-dollar"></i> Booking Funnel Analysis
                </div>
                <div class="chart-container">
                    <i class="fas fa-clock"></i> Average Time to Book Analysis
                </div>
                <div class="chart-container">
                    <i class="fas fa-mobile-alt"></i> Device Usage & Platform Performance
                </div>
                <div style="margin-top: 20px;">
                    <h3 style="color: var(--primary); margin-bottom: 15px;">Key Insights</h3>
                    <ul style="padding-left: 20px; line-height: 1.6;">
                        <li>73% of users abandon booking process at payment step</li>
                        <li>Mobile users have 45% higher conversion rate than desktop</li>
                        <li>Average booking time is 8.2 minutes from first search</li>
                        <li>Users who view 3+ itineraries are 67% more likely to book</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- Planned Trips -->
        <div class="dashboard-content" id="planned-trips">
            <div class="card">
                <div class="card-header">
                    <h2>View Planned Trips Analytics</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Itinerary</th>
                            <th>Traveller</th>
                            <th>Destination</th>
                            <th>Travel Date</th>
                            <th>Duration</th>
                            <th>Expected Revenue</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ITN-012</td>
                            <td>Emma Garcia</td>
                            <td>Paris, France</td>
                            <td>2024-02-15</td>
                            <td>7 days</td>
                            <td>$2,800</td>
                            <td><span class="status-badge status-active">Confirmed</span></td>
                        </tr>
                        <tr>
                            <td>ITN-013</td>
                            <td>James Wilson</td>
                            <td>Rome, Italy</td>
                            <td>2024-02-20</td>
                            <td>5 days</td>
                            <td>$1,950</td>
                            <td><span class="status-badge status-active">Confirmed</span></td>
                        </tr>
                        <tr>
                            <td>ITN-014</td>
                            <td>Lisa Chen</td>
                            <td>Barcelona, Spain</td>
                            <td>2024-03-01</td>
                            <td>6 days</td>
                            <td>$2,400</td>
                            <td><span class="status-badge status-active">Confirmed</span></td>
                        </tr>
                        <tr>
                            <td>ITN-015</td>
                            <td>Robert Davis</td>
                            <td>Amsterdam, Netherlands</td>
                            <td>2024-03-10</td>
                            <td>4 days</td>
                            <td>$1,680</td>
                            <td><span class="status-badge status-pending">Payment Pending</span></td>
                        </tr>
                    </tbody>
                </table>
                <div class="stats-grid" style="margin-top: 20px;">
                    <div class="stat-card">
                        <div class="stat-icon revenue">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-number">24</div>
                        <div class="stat-label">Trips Next 30 Days</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon payouts">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-number">$58,430</div>
                        <div class="stat-label">Forecasted Revenue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon refunds">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number">18</div>
                        <div class="stat-label">Unique Travellers</div>
                    </div>
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
    <script>
        // Tab Navigation
        function switchTab(tabId) {
            // Remove active class from all links and content
            document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
            document.querySelectorAll('.dashboard-content').forEach(content => content.classList.remove('active'));
            // Add active class to clicked link
            document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
            // Show corresponding content
            document.getElementById(tabId).classList.add('active');
            // Update header title
            const headerTitle = document.querySelector('.header h1');
            const titles = {
                'dashboard': 'Business Manager Dashboard',
                'transactions': 'Transaction Dashboard',
                'refunds': 'Refunds & Adjustments',
                'payouts': 'Payout Management',
                'commissions': 'Commission Rates',
                'financial-reports': 'Financial Reporting',
                'analytics': 'Analytics Dashboard',
                'sales-reports': 'Sales Performance Reports',
                'customer-analysis': 'Customer Behavior Analysis',
                'planned-trips': 'Planned Trips Analytics',
                'internal-chat': 'Internal Chat',
                'profile': 'Profile Settings'
            };
            headerTitle.textContent = titles[tabId];
        }
        // Add event listeners to sidebar links
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tabId = this.getAttribute('data-tab');
                switchTab(tabId);
            });
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
        // Enhanced Tab Switching with Chat Initialization
        function switchTab(tabId) {
            document.querySelectorAll('.sidebar-menu a').forEach(a => a.classList.remove('active'));
            document.querySelectorAll('.dashboard-content').forEach(content => content.classList.remove('active'));
            const link = document.querySelector(`[data-tab="${tabId}"]`);
            const content = document.getElementById(tabId);
            if (link) link.classList.add('active');
            if (content) content.classList.add('active');
            const headerTitle = document.querySelector('.header h1');
            const titles = {
                'dashboard': 'Business Manager Dashboard',
                'transactions': 'Transaction Dashboard',
                'refunds': 'Refunds & Adjustments',
                'payouts': 'Payout Management',
                'commissions': 'Commission Rates',
                'financial-reports': 'Financial Reporting',
                'analytics': 'Analytics Dashboard',
                'sales-reports': 'Sales Performance Reports',
                'customer-analysis': 'Customer Behavior Analysis',
                'planned-trips': 'Planned Trips Analytics',
                'internal-chat': 'Internal Chat',
                'profile': 'Profile Settings'
            };
            if (headerTitle) {
                headerTitle.textContent = titles[tabId] || 'Business Manager Dashboard';
            }
            // Initialize chat only when needed
            if (tabId === 'internal-chat') {
                setTimeout(initInternalChat, 100); // Ensure DOM is ready
            }
        }
        // Attach tab click handlers
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tabId = this.getAttribute('data-tab');
                switchTab(tabId);
            });
        });
        // Initialize dashboard
        switchTab('dashboard');
    </script>
</body>
</html>