<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $result = $pdo->query('SHOW TABLES LIKE "vehicle_verifications"');
    if ($result->rowCount() > 0) {
        echo "Table vehicle_verifications exists\n";
    } else {
        echo "Table vehicle_verifications does not exist\n";
    }

    // Also check what tables exist
    $tables = $pdo->query('SHOW TABLES');
    echo "All tables:\n";
    foreach ($tables as $table) {
        echo $table[0] . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>