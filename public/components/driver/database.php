<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'sri_lanka_travel';

try {
    // Create connection using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get trending drivers
function getTrendingDrivers($limit = null) {
    global $pdo;
    
    $sql = "SELECT * FROM drivers WHERE is_trending = 1 ORDER BY rating DESC, total_reviews DESC";
    if ($limit !== null) {
        $sql .= " LIMIT :limit";
    }
    
    $stmt = $pdo->prepare($sql);
    if ($limit !== null) {
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    }
    $stmt->execute();
    
    return $stmt->fetchAll();
}

// Function to get all drivers
function getAllDrivers($limit = null) {
    global $pdo;
    
    $sql = "SELECT * FROM drivers WHERE is_licensed = 1 ORDER BY rating DESC";
    if ($limit !== null) {
        $sql .= " LIMIT :limit";
    }
    
    $stmt = $pdo->prepare($sql);
    if ($limit !== null) {
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    }
    $stmt->execute();
    
    return $stmt->fetchAll();
}

// Function to get reviewed drivers
function getReviewedDrivers($limit = 4) {
    global $pdo;
    
    $sql = "SELECT * FROM drivers WHERE total_reviews > 50 ORDER BY total_reviews DESC LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}

// Function to get tourist drivers
function getTouristDrivers($limit = 4) {
    global $pdo;
    
    $sql = "SELECT * FROM drivers WHERE is_tourist_guide = 1 ORDER BY rating DESC LIMIT :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll();
}
?>
