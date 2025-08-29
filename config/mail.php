<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$phpmailer = new PHPMailer();
$phpmailer->isSMTP();
$phpmailer->Host = 'sandbox.smtp.mailtrap.io';
$phpmailer->SMTPAuth = true;
$phpmailer->Port = 2525;
$phpmailer->Username = 'c6eeac0e3d59cb';
$phpmailer->Password = '****55e3';
try {
    $phpmailer->setFrom('auth@7auth.mg', '7auth Project');
    $phpmailer->isHTML(true);
} catch (Exception $e) {

}
