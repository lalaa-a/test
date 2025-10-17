<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load configuration
require_once '../config.php';

// Load core classes
require_once '../core/App.php';
require_once '../core/Controller.php';
require_once '../core/Database.php';

// Initialize the application
$app = new App();