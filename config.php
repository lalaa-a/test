<?php
// Application Configuration

// Database Configuration  
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "sri_lanka_travel");

// Path Configuration
define('APP_ROOT', dirname(__FILE__) . '/app');
define('URL_ROOT', 'http://localhost/test');
define('SITE_NAME', 'Sri Lanka Travel');

// App Settings
define('APP_VERSION', '1.0.0');

// Helper function for redirects
function redirect($url) {
    header('Location: ' . URL_ROOT . $url);
    exit();
}
?>
