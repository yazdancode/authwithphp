<!DOCTYPE html>
<html lang="en" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Page</title>
    <link rel="stylesheet" href="<?= assets('css/style.css') ?>">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/4.2.0/mdb.min.css" rel="stylesheet" />
</head>
<body>
<section class="background-radial-gradient overflow-hidden">
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
        <div class="row gx-lg-5 align-items-center mb-5 justify-content-center">
            <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
                    7Learn Auth <br />
                    <span style="color: hsl(218, 81%, 75%)">Verify Page</span>
                </h1>

                <?php if (!empty($_SESSION['success'])) : ?>
                    <h4 class="mb-4 opacity-70 text-success">
                        <?= $_SESSION['success'] ?>
                    </h4>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['error'])) : ?>
                    <h3 class="text-danger">Fix this error and try again:</h3>
                    <h4 class="mb-4 opacity-70 text-danger">
                        <?= $_SESSION['error'] ?>
                    </h4>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
<!---->
<!--                --><?php //if (!empty($_SESSION['email'])) : ?>
<!--                    <p>ایمیل شما: --><?php //= $_SESSION['email'] ?><!--</p>-->
<!--                --><?php //endif; ?>
            </div>

            <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card bg-glass">
                    <div class="card-body px-4 py-5 px-md-5">
                        <form action="<?= site_url('auth.php?action=verify') ?>" method="post">
                            <!-- Token input -->
                            <div class="form-outline mb-4">
                                <input type="text" name="token" id="token" class="form-control" />
                                <label class="form-label" for="token">Enter Token</label>
                            </div>
                            <!-- Submit button -->
                            <button type="submit" class="btn btn-primary btn-block mb-4">
                                Submit
                            </button>
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
