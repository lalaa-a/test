<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check vehicle types in driver_accept_trips
    $stmt = $pdo->query("SELECT vehicleType, COUNT(*) as count FROM driver_accept_trips WHERE vehicleType IS NOT NULL AND vehicleType != '' GROUP BY vehicleType ORDER BY count DESC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Vehicle types in driver_accept_trips:\n";
    foreach ($results as $row) {
        echo $row['vehicleType'] . ": " . $row['count'] . "\n";
    }

    // Check driver ownership
    $stmt2 = $pdo->query("SELECT driverId, COUNT(*) as vehicleCount FROM vehicles GROUP BY driverId HAVING vehicleCount > 0 ORDER BY vehicleCount DESC LIMIT 10");
    $drivers = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nDriver vehicle counts:\n";
    foreach ($drivers as $driver) {
        echo "Driver " . $driver['driverId'] . ": " . $driver['vehicleCount'] . " vehicles\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>