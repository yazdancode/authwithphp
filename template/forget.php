<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>بازیابی رمز عبور</title>
    <link rel="stylesheet" href="assets/css/forget.css"> 
</head>
<body>
    <div class="container">
        <h2>فراموشی رمز عبور</h2>
        <form action="process/forgot_process.php" method="post">
            <label for="email">ایمیل خود را وارد کنید:</label>
            <input type="email" name="email" id="email" required>

            <button type="submit">ارسال لینک بازیابی</button>
        </form>
        <p><a href="index.php">بازگشت به ورود</a></p>
    </div>
</body>
</html>
