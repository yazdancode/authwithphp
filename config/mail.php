<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

try {
    $phpmailer = new PHPMailer(true);
//    $phpmailer->SMTPDebug = 2;
    $phpmailer->Debugoutput = 'html';

    // SMTP configuration
    $phpmailer->isSMTP();
    $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = '70df718e43f248';
    $phpmailer->Password = '52baaae7b1d66c';
    $phpmailer->Port = 2525;
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->AuthType = 'LOGIN';

} catch (Exception $e) {
    echo "Message could not be sent. Exception: " . $e->getMessage();
}
