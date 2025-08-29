<?php
global $phpmailer;
require 'config/init.php';


try {
    $result = $phpmailer->addAddress('yshabanei@gmail.com', 'yazdan');
    $phpmailer->Subject = 'test gi d khomeni';
    $phpmailer->Body = 'salam gi d khomeni';
    $result = $phpmailer->send();
    var_dump($result);
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: $phpmailer->ErrorInfo";
}