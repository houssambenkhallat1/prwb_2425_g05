<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <title>change_password</title>
</head>
<body>
<div class="container-sm login-container">


    <div class="row">
        <div class="col-12">
            <!-- Header with back button -->
            <div class="d-flex align-items-center mb-4">
                <a href="main/settings" class="text-white text-decoration-none">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h4 class="mb-0 ms-3">change_password</h4>
                <div class="ms-auto">
                    <label for="submitButton" style="cursor: pointer;">
                        <i class="bi bi-floppy-fill text-primary fs-4"></i>
                    </label>

                </div>

            </div>

            <hr class="custom-light-grey">
            <br>

            <div>
                <form action="main/change_password" method="post">
                    <input type="password" class="form-control m-2 " name="password" placeholder="password actuel" "><br>
                    <input type="password" class="form-control m-2" name="nouveau_password"
                           placeholder="nouveau password  ">
                    <input type="password" class="form-control m-2" name="Confirme_nouveau_password"
                           placeholder="Confirme nouveau password" ">
                    <input type="submit" id="submitButton" hidden>
                </form>
            </div>

            <div>
                <?php
                if (!empty($msg)) {
                    echo $msg;
                }
                ?>
                <?php if (count($errors) != 0): ?>
                    <div class='errors'>
                        <br><br>
                        <p>Please correct the following error(s) :</p>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
