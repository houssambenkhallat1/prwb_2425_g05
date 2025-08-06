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
    <title>Edit profile</title>
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
                <h4 class="mb-0 ms-3">Edit profile</h4>
                <div class="ms-auto">
                    <label for="submitButton" style="cursor: pointer;">
                        <i class="bi bi-floppy-fill text-primary fs-4"></i>
                    </label>
                </div>
            </div>

            <hr class="custom-light-grey">

            <div>
                <form action="main/edit_profile" method="POST">
                    <label for="fname" class="mt-4 mb-2">Full Name:</label>
                    <input id="fname" type="text" class="form-control" name="fname"
                           value="<?= $user->get_full_name() ?>"><br>
                    <label for="email" class="mt-4 mb-2">Email:</label>
                    <input id="email" type="email" class="form-control " name="email" value="<?= $user->get_email() ?>">
                    <input type="submit" id="submitButton" hidden>
                </form>
            </div>

            <div>
                <ul class="text-success"><br>
                    <?php if (!(empty($msg))) {
                        echo '<li>' . $msg . '</li>';
                    } ?>
                </ul>
                <?php if (count($errors) != 0): ?>
                    <div class='text-danger'>
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
