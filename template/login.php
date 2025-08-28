<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>صفحه ورود</title>
    <link rel="stylesheet" href="<?= assets('css/style.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.2.0/mdb.min.css" rel="stylesheet"/>
</head>
<body>
<section class="background-radial-gradient overflow-hidden">
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
        <div class="row gx-lg-5 align-items-center mb-5 justify-content-center">
            <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
                    سیستم احراز هویت 7Learn <br/>
                    <span style="color: hsl(218, 81%, 75%)">صفحه ورود</span>
                </h1>

                <!-- نمایش پیام موفقیت -->
                <?php if (!empty($_SESSION['success'])) : ?>
                    <h4 class="mb-4 opacity-70 text-success">
                        <?= $_SESSION['success'] ?>
                    </h4>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- نمایش پیام خطا -->
                <?php if (!empty($_SESSION['error'])) : ?>
                    <h3 class="text-danger">خطا:</h3>
                    <h4 class="mb-4 opacity-70 text-danger">
                        <?= $_SESSION['error'] ?>
                    </h4>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            </div>

            <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card bg-glass">
                    <div class="card-body px-4 py-5 px-md-5">
                        <form action="<?= site_url('auth.php?action=login') ?>" method="post">
                            <!-- ایمیل -->
                            <div class="form-outline mb-4">
                                <input type="email" name="email" id="email" class="form-control" required />
                                <label class="form-label" for="email">ایمیل</label>
                            </div>

                            <!-- دکمه ورود -->
                            <button type="submit" class="btn btn-primary btn-block mb-4">
                                ورود
                            </button>
                            <hr>

                            <!-- لینک ثبت نام -->
                            <div class="row mt-4">
                                <div class="col-6">
                                    <p class="text-start fs-5">اگر هنوز ثبت‌نام نکرده‌اید:</p>
                                </div>
                                <div class="col-6 d-flex justify-content-end">
                                    <a href="<?= site_url('auth.php?action=register') ?>" class="btn btn-success btn-small mb-4">
                                        ثبت نام
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.2.0/mdb.min.js"></script>
</body>
</html>
