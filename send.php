<?php
use PHPMailer\PHPMailer\Exception;
global $phpmailer;
require 'config/init.php';


try {
    $result = $phpmailer->addAddress('yshabanei@gmail.com', 'yazdan');
} catch (Exception $e) {
    echo 'Error adding address: ' . $e->getMessage();
    $result = false;
}
