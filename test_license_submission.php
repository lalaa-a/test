<?php
require_once 'app/config/config.php';
require_once 'app/libraries/Database.php';
require_once 'app/models/ProfileControllerModel.php';

try {
    $db = new Database();
    $model = new ProfileControllerModel();

    // Test data
    $testData = [
        'tLicenseNumber' => 'TEST123',
        'tLicenseExpiryDate' => '2025-12-31',
        'tLicensePhotoFront' => 'test_front.jpg',
        'tLicensePhotoBack' => 'test_back.jpg',
        'tlSubmitted' => true
    ];

    echo 'Testing submitTouristLicense with test data...' . PHP_EOL;
    $result = $model->submitTouristLicense(1, $testData); // Assuming userId 1 exists

    if ($result) {
        echo 'SUCCESS: License submission completed' . PHP_EOL;
    } else {
        echo 'FAILED: License submission failed' . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
?>