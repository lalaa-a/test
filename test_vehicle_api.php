<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Test vehicle verification query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM vehicle_verification");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Vehicle verification records: " . $result['count'] . "\n";

    // Test driver accepted trips query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM driver_accepted_trips");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Driver accepted trips records: " . $result['count'] . "\n";

    echo "Database connection successful!\n";

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>