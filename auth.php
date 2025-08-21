<?php

require 'config/init.php';

$page = $_GET['action'] ?? 'login';

switch ($page) {
    case 'register':
        include 'template/register.php';
        break;
    case 'forgot':
        include 'template/forgot.php';
        break;
    case 'verify':
        include 'template/verify.php';
        break;
    default:
        include 'template/login.php';
}
