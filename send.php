<?php
global $phpmailer;
require 'config/init.php';


$emails = [
    ['email' => 'yshabanei@gmail.com', 'name' => 'yazdan'],
    ['email' => 'test@example.com', 'name' => 'Test User'],
];

foreach ($emails as $recipient) {
    try {
        $phpmailer->clearAddresses();
        $phpmailer->addAddress($recipient['email'], $recipient['name']);
        $phpmailer->Subject = 'Test Email';
        $phpmailer->Body    = 'Salam, in yek test ast.';

        $sent = $phpmailer->send();
        echo "Email to {$recipient['email']} sent: " . ($sent ? "OK" : "Failed") . "<br>";

        sleep(2); // فاصله دو ثانیه بین ایمیل‌ها
    } catch (Exception $e) {
        echo "Message could not be sent to {$recipient['email']}. Error: {$phpmailer->ErrorInfo}<br>";
    }
}

