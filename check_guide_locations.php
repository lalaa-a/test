<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if guide 17 has locations
    $stmt = $pdo->prepare("SELECT * FROM guide_locations WHERE guideId = ? AND isActive = 1");
    $stmt->execute([17]);
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Guide 17 locations:\n";
    print_r($locations);

    // Check trip_events for travelSpotId
    $stmt = $pdo->prepare("SELECT eventId, travelSpotId FROM trip_events WHERE tripId = ?");
    $stmt->execute([1]); // Assuming trip ID 1, change if needed
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\nTrip events:\n";
    print_r($events);

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>