<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    
    // 🔐 Hardcoded Gmail credentials (for testing only)
    $mail->Username   = 'roomsydorms@gmail.com';
    $mail->Password   = 'eses erfp gkbk zjag'; // Use Gmail App Password, NOT Gmail password
    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('roomsydorms@gmail.com', 'Roomsydorms');
    $mail->addAddress('fares24681234@gmail.com', 'Test User');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from PHPMailer';
    $mail->Body    = '<h1>This is a test email</h1><p>If you received this, SMTP works!</p>';
    $mail->AltBody = 'This is a test email in plain text.';

    $mail->send();
    echo '✅ Message has been sent';
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

?>
