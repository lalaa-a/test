<?php

    // Database configuration and connection logic
    // Database constants
    // These constants should match your database configuration
    // Update these values according to your database setup
    define("DB_HOST", "localhost");
    define("DB_USER", "root");
    define("DB_PASSWORD","");
    define("DB_NAME","tripingoo");

    define('APP_ROOT', dirname(__FILE__,2));

    // URL Root
    define('URL_ROOT', 'http://localhost/test');

    // URL Root
    define('IMG_ROOT', 'http://localhost/test/public/img');

    //Upload root
    define('UP_ROOT', 'http://localhost/test/public/uploads');

    // Website Name
    define('SITE_NAME', 'test');

    // ROOT_PATH real root
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));

    // Email Configuration
    define('SMTP_HOST', 'smtp.gmail.com');
    define('SMTP_USERNAME', 'lwravishan@gmail.com'); // Replace with your email
    define('SMTP_PASSWORD', 'qfmq spro uelu gerd'); // Replace with your app password
    define('SMTP_PORT', 587);
    define('SMTP_ENCRYPTION', 'tls'); // 'tls' or 'ssl'
    define('FROM_EMAIL', 'lwravishan@gmail.com'); // Must match SMTP_USERNAME for Gmail
    define('FROM_NAME', 'Tripingoo');
    define('SMTP_DEBUG', 0); // Set to 2 for detailed debugging
?>