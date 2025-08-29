<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure PHPMailer is installed via Composer

try {
    // Create a new PHPMailer instance with exceptions enabled
    $phpmailer = new PHPMailer(true);

    // Enable SMTP debugging (0 = off, 1 = client messages, 2 = client + server messages)
    $phpmailer->SMTPDebug = 2;
    $phpmailer->Debugoutput = 'html';

    // SMTP configuration
    $phpmailer->isSMTP();
    $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = '70df718e43f248';
    $phpmailer->Password = '52baaae7b1d66c';
    $phpmailer->Port = 2525; // Mailtrap port
    $phpmailer->SMTPSecure = 'tls'; // ensure TLS is used
    $phpmailer->AuthType = 'LOGIN'; // force LOGIN auth

    // Sender info
    $phpmailer->setFrom('auth@7auth.mg', '7auth Project');
    $phpmailer->isHTML(true); // email format

    // Recipient
    $phpmailer->addAddress('yshabanei@gmail.com', 'yazdan');

    // Email subject & body
    $phpmailer->Subject = 'Test Email';
    $phpmailer->Body    = 'Salam, in yek test ast.';

    // Send email
    if ($phpmailer->send()) {
        echo 'Email sent successfully!';
    } else {
        echo 'Email could not be sent. Error: ' . $phpmailer->ErrorInfo;
    }

} catch (Exception $e) {
    echo "Message could not be sent. Exception: " . $e->getMessage();
}
