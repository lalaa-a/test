<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if guides table exists
    $stmt = $pdo->query('SHOW TABLES LIKE "guides"');
    if ($stmt->rowCount() > 0) {
        echo "guides table exists\n";
        $stmt = $pdo->query('DESCRIBE guides');
        echo "guides table structure:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['Field'] . ' - ' . $row['Type'] . "\n";
        }
    } else {
        echo "guides table does not exist\n";
    }
    echo "\n";

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>