<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>صفحه تأیید - 7Learn Auth</title>
    <link rel="stylesheet" href="<?= assets('css/style.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.2.0/mdb.min.css" rel="stylesheet" />
    <style>
        .background-radial-gradient {
            background-color: hsl(218, 41%, 15%);
            background-image: radial-gradient(650px circle at 0% 0%,
            hsl(218, 41%, 35%) 15%,
            hsl(218, 41%, 30%) 35%,
            hsl(218, 41%, 20%) 75%,
            hsl(218, 41%, 19%) 80%,
            transparent 100%),
            radial-gradient(1250px circle at 100% 100%,
                    hsl(218, 41%, 45%) 15%,
                    hsl(218, 41%, 30%) 35%,
                    hsl(218, 41%, 20%) 75%,
                    hsl(218, 41%, 19%) 80%,
                    transparent 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .bg-glass {
            background-color: hsla(0, 0%, 100%, 0.9) !important;
            backdrop-filter: saturate(200%) blur(25px);
        }

        #radius-shape-1 {
            height: 220px;
            width: 220px;
            top: -60px;
            left: -130px;
            background: radial-gradient(#44006b, #ad1fff);
            overflow: hidden;
        }

        #radius-shape-2 {
            border-radius: 38% 62% 63% 37% / 70% 33% 67% 30%;
            bottom: -60px;
            right: -110px;
            width: 300px;
            height: 300px;
            background: radial-gradient(#44006b, #ad1fff);
            overflow: hidden;
        }

        .text-fa {
            text-align: right;
            direction: rtl;
        }
    </style>
</head>
<body>
<section class="background-radial-gradient overflow-hidden">
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
        <div class="row gx-lg-5 align-items-center mb-5 justify-content-center">
            <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                <h1 class="my-5 display-5 fw-bold ls-tight text-fa" style="color: hsl(218, 81%, 95%)">
                    سامانه احراز هویت 7Learn <br />
                    <span style="color: hsl(218, 81%, 75%)">صفحه تأیید کد</span>
                </h1>

                <?php if (!empty($_SESSION['success'])) : ?>
                    <div class="alert alert-success" role="alert">
                        <?= $_SESSION['success'] ?>
                        <?php unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_SESSION['error'])) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $_SESSION['error'] ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_SESSION['email'])) : ?>
                    <div class="alert alert-info text-fa" role="alert">
                        <i class="fas fa-envelope me-2"></i>
                        کد تأیید به ایمیل <strong><?= $_SESSION['email'] ?></strong> ارسال شد.
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card bg-glass">
                    <div class="card-body px-4 py-5 px-md-5">
                        <form action="<?= site_url('auth.php?action=verify') ?>" method="post">
                            <!-- Token input -->
                            <div class="form-outline mb-4">
                                <input type="text" name="code" id="code" class="form-control text-center" required />
                                <label class="form-label" for="code">کد تأیید شش رقمی</label>
                            </div>

                            <!-- Submit button -->
                            <button type="submit" name="verify_code" class="btn btn-primary btn-block mb-4 w-100">
                                <i class="fas fa-check-circle me-2"></i> تأیید کد
                            </button>

                            <div class="text-center">
                                <p>کد را دریافت نکرده‌اید؟</p>
                                <button type="submit" name="resend_token" class="btn btn-outline-primary">
                                    <i class="fas fa-paper-plane me-2"></i> ارسال مجدد کد
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.2.0/mdb.min.js"></script>
<script>
    // فوکوس خودکار روی فیلد کد تأیید
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('code').focus();
    });

    // محدود کردن ورودی به عدد و حداکثر ۶ کاراکتر
    document.getElementById('code').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 6) {
            this.value = this.value.slice(0, 6);
        }
    });
</script>
</body>
</html>