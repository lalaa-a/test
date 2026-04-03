<?php
// Email Test Script
// Run this from browser: http://localhost/test/test_email.php

require_once 'vendor/autoload.php';
require_once 'app/config/config.php';
require_once 'app/helpers/mail_helper.php';

// Test email address (change this to your email for testing)
$testEmail = 'lwravishan@gmail.com'; // Change this to your test email
$testOTP = '123456';

echo "<h2>Email Configuration Test</h2>";
echo "<h3>Current Configuration:</h3>";
echo "SMTP Host: " . SMTP_HOST . "<br>";
echo "SMTP Username: " . SMTP_USERNAME . "<br>";
echo "SMTP Port: " . SMTP_PORT . "<br>";
echo "SMTP Encryption: " . SMTP_ENCRYPTION . "<br>";
echo "From Email: " . FROM_EMAIL . "<br>";
echo "From Name: " . FROM_NAME . "<br><br>";

echo "<h3>Testing Email Send...</h3>";

$result = sendOTPEmail($testEmail, $testOTP);

if ($result['success']) {
    echo "<div style='color: green; padding: 10px; border: 1px solid green; background: #e6ffe6;'>";
    echo "✅ SUCCESS: " . $result['message'] . "<br>";
    echo "Test OTP email sent to: " . $testEmail;
    echo "</div>";
} else {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; background: #ffe6e6;'>";
    echo "❌ FAILED: " . $result['message'] . "<br>";
    if (isset($result['debug_error'])) {
        echo "Debug Info: " . $result['debug_error'];
    }
    echo "</div>";
}

echo "<br><h3>Troubleshooting Tips:</h3>";
echo "<ul>";
echo "<li>Check if your Gmail App Password is correct</li>";
echo "<li>Verify 2-Factor Authentication is enabled on your Gmail</li>";
echo "<li>Check spam/junk folder in your email</li>";
echo "<li>Make sure 'Less secure app access' is disabled (use App Password instead)</li>";
echo "<li>Check PHP error logs for detailed errors</li>";
echo "</ul>";

echo "<br><h3>Recent PHP Errors (last 10 lines):</h3>";
echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
$errorLog = 'C:\wamp64\logs\php_error.log';
if (file_exists($errorLog)) {
    $lines = file($errorLog);
    $recentLines = array_slice($lines, -10);
    echo htmlspecialchars(implode('', $recentLines));
} else {
    echo "Error log not found at: " . $errorLog;
}
echo "</pre>";
?>