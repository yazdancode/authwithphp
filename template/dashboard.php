<?php
// چون auth.php این فایل رو include می‌کنه، session و دیتابیس قبلاً فعال هستن

// چک کن کاربر لاگین کرده یا نه
if (empty($_SESSION['email'])) {
    header("Location: auth.php?action=login");
    exit;
}

// چک کن کاربر وریفای شده یا نه
if (empty($_SESSION['verified']) || $_SESSION['verified'] !== true) {
    header("Location: auth.php?action=verify");
    exit;
}

// می‌تونیم اطلاعات کاربر رو نمایش بدیم
$email = htmlspecialchars($_SESSION['email']);
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>داشبورد</title>
</head>
<body>
<h1>🎉 خوش آمدی <?= $email ?></h1>
<p>شما با موفقیت وارد سیستم شدید.</p>

<nav>
    <a href="auth.php?action=profile">پروفایل</a> |
    <a href="logout.php">خروج</a>
</nav>
</body>
</html>
