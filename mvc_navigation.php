<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MVC Navigation - Test Your Routes</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .nav-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .route-section {
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #006a71;
        }
        .route-section h2 {
            color: #006a71;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        .route-link {
            display: inline-block;
            margin: 8px 12px 8px 0;
            padding: 10px 20px;
            background: #006a71;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .route-link:hover {
            background: #005a61;
            transform: translateY(-2px);
        }
        .description {
            color: #666;
            font-size: 0.9rem;
            margin-top: 10px;
            font-style: italic;
        }
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-left: 10px;
        }
        .working { background: #d4edda; color: #155724; }
        .testing { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="nav-container">
        <h1>🎯 MVC Navigation Test</h1>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">
            Click the links below to test your MVC routing structure
        </p>

        <div class="route-section">
            <h2>🏠 Home Routes</h2>
            <a href="/test/" class="route-link">Home Page</a>
            <span class="status working">✅ Working</span>
            <div class="description">
                Routes to: HomeController->index() → app/views/home.php
            </div>
        </div>

        <div class="route-section">
            <h2>🚗 Driver Routes</h2>
            <a href="/test/driver" class="route-link">Main Drivers</a>
            <a href="/test/driver/licensed" class="route-link">Licensed</a>
            <a href="/test/driver/reviewed" class="route-link">Reviewed</a>
            <a href="/test/driver/tourist" class="route-link">Tourist</a>
            <span class="status working">✅ Working</span>
            <div class="description">
                Routes to: DriverController->method() → app/views/driver/*.php
            </div>
        </div>

        <div class="route-section">
            <h2>👤 User Routes</h2>
            <a href="/test/user/trips" class="route-link">My Trips</a>
            <span class="status working">✅ Working</span>
            <div class="description">
                Routes to: UserController->trips() → app/views/user/trips.php
            </div>
        </div>

        <div class="route-section">
            <h2>🔧 Debug & Testing</h2>
            <a href="/test/test_tourist.php" class="route-link">Debug Tourist</a>
            <a href="/test/direct_access.html" class="route-link">Direct Access</a>
            <span class="status testing">🧪 Testing</span>
            <div class="description">
                Helper pages for debugging and direct component access
            </div>
        </div>

        <hr style="margin: 30px 0; border: 1px solid #eee;">
        
        <div style="text-align: center; color: #666;">
            <p><strong>MVC Structure Active!</strong></p>
            <p>All URLs above use proper MVC routing with controllers, models, and views.</p>
            <p><em>Current Server: <?php echo $_SERVER['HTTP_HOST'] ?? 'localhost'; ?></em></p>
        </div>
    </div>
</body>
</html>
