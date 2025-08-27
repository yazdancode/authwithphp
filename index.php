<?php
require __DIR__ . '/config/init.php';
$url = $_GET['url'] ?? 'login';
$url = trim($url, '/');
$file = __DIR__ . "/template/{$url}.php";

if (file_exists($file)) {
    require $file;
} else {
    require __DIR__ . '/template/not-found.php';
<<<<<<< HEAD
}
=======
}
>>>>>>> 9afd6b6 (changes repository github and version php)
