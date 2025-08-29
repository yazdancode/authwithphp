<?php
global $phpmailer;
require 'config/init.php';


try {
    $phpmailer->addAddress('yshabanei@gmail.com', 'yazdan');
    $phpmailer->subject = 'test gi d khomeni';
    $phpmailer->body = 'salam gi d khomeni';
    $phpmailer->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: $phpmailer->ErrorInfo";
}
