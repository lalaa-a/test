<?php
require_once 'app/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Test the query
    $query = "SELECT
                u.id as guideId,
                u.id as userId,
                u.fullname as firstName,
                '' as lastName,
                u.email,
                pd.bio as experience,
                pd.languages,
                '' as specialties,
                0 as hourlyRate,
                CASE WHEN u.verified = 1 THEN 'active' ELSE 'inactive' END as status,
                u.created_at as createdAt
              FROM users u
              LEFT JOIN profile_details pd ON u.id = pd.userId
              WHERE u.account_type = 'guide'
              AND u.verified = 1
              ORDER BY u.id ASC";

    $stmt = $pdo->query($query);
    $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Query executed successfully. Found " . count($guides) . " guides:\n";
    foreach ($guides as $guide) {
        echo "ID: " . $guide['guideId'] . ", Name: " . $guide['firstName'] . ", Email: " . $guide['email'] . "\n";
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>