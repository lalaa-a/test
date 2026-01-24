<?php

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Send an email using PHPMailer
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $htmlBody HTML body content
 * @param string $altBody Plain text alternative body (optional)
 * @param string $fromEmail Sender email (optional, uses FROM_EMAIL constant by default)
 * @param string $fromName Sender name (optional, uses FROM_NAME constant by default)
 * @return array ['success' => bool, 'message' => string]
 */
function sendEmail($to, $subject, $htmlBody, $altBody = '', $fromEmail = null, $fromName = null) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = SMTP_PORT;

        // Recipients
        $mail->setFrom($fromEmail ?? FROM_EMAIL, $fromName ?? FROM_NAME);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        
        if (!empty($altBody)) {
            $mail->AltBody = $altBody;
        }

        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Email sent successfully'
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to send email. Error: ' . $mail->ErrorInfo
        ];
    }
}

/**
 * Send OTP verification email
 * 
 * @param string $to Recipient email address
 * @param string $otp The OTP code to send
 * @return array ['success' => bool, 'message' => string]
 */
function sendOTPEmail($to, $otp) {
    $subject = 'Email Verification - Tripingoo';
    
    $htmlBody = "
    <html>
    <head>
        <title>Email Verification</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #006a71; color: white; padding: 20px; text-align: center; }
            .content { padding: 30px 20px; background-color: #f9f9f9; }
            .otp-code { font-size: 32px; font-weight: bold; color: #006a71; letter-spacing: 5px; text-align: center; margin: 20px 0; }
            .footer { background-color: #006a71; color: white; padding: 15px; text-align: center; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to Tripingoo!</h1>
            </div>
            <div class='content'>
                <h2>Email Verification</h2>
                <p>Your verification code is:</p>
                <div class='otp-code'>{$otp}</div>
                <p>This code will expire in 10 minutes.</p>
                <p>If you didn't request this code, please ignore this email.</p>
            </div>
            <div class='footer'>
                <p>Best regards,<br>Tripingoo Team</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $altBody = "Welcome to Tripingoo!\n\nYour verification code is: {$otp}\n\nThis code will expire in 10 minutes.\n\nIf you didn't request this code, please ignore this email.\n\nBest regards,\nTripingoo Team";
    
    return sendEmail($to, $subject, $htmlBody, $altBody);
}
