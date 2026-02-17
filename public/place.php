<?php
// Simple launcher for place detail pages used in local dev.
define('APP_ROOT', dirname(__DIR__));
// Basic bootstrap — if your project uses a different bootloader adjust accordingly
if (file_exists(APP_ROOT.'/app/bootloader.php')) {
    include APP_ROOT.'/app/bootloader.php';
}

// Render the place detail view
include APP_ROOT.'/app/views/Explore/destinations/placeDetail.php';
