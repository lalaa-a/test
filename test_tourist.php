<?php
// Test tourist page routing
echo "<h1>Testing Tourist Page Access</h1>";
echo "<p>Current URL: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Server Name: " . $_SERVER['SERVER_NAME'] . "</p>";

// Test direct inclusion
echo "<h2>Direct View Test:</h2>";
define('APP_ROOT', dirname(__FILE__) . '/app');
require_once 'config.php';

// Check if tourist view exists
$touristView = APP_ROOT . '/views/driver/tourist.php';
echo "<p>Tourist view file exists: " . (file_exists($touristView) ? 'YES' : 'NO') . "</p>";
echo "<p>Tourist view path: " . $touristView . "</p>";

echo "<hr>";
echo "<a href='/test/driver/tourist'>→ Go to Tourist Drivers Page</a><br>";
echo "<a href='/test/public/driver/tourist'>→ Go to Tourist Drivers Page (via public)</a><br>";
echo "<a href='direct_access.html'>→ Go to Direct Access Menu</a>";
?>
