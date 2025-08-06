<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <title>signup</title>
</head>
<body>
<div class="container-sm login-container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                <h1 class="mt-4 mb-5">&#127856; Djote Forms</h1>
            </div>
            <div class="form-container rounded p-2">
                <div class="d-flex justify-content-center">
                    <h2>Sign up</h2>
                </div>
                <hr class="custom-light-grey">
                <div>
                    <form method="POST" action="Main/signup">
                        <!-- username -->
                        <div class="input-group mb-3">
                                <span class="input-group-text" >
                                    <i class="bi bi-person"></i>
                                </span>
                            <input type="text" name="email" required class="form-control " placeholder="Email" value="<?php echo $email; ?>" aria-label="Username" >
                        </div>

                        <!-- fullname -->
                        <div class="input-group mb-3">
                                <span class="input-group-text" >
                                    <i class="bi bi-person"></i>
                                </span>
                            <input type="text" name="fullName" required class="form-control " placeholder="Full Name" value="<?php echo $fullName; ?>" aria-label="Username" >
                        </div>

                        <!-- password -->
                        <div class="input-group mb-3">
                                <span id="password" class="input-group-text text-dark">
                                    <i class="bi bi-key"></i>
                                </span>
                            <input type="password" name="password" required class="form-control" placeholder="Password" aria-label="Password" >
                        </div>

                        <!-- Confirm your password -->
                        <div class="input-group mb-3">
                                <span  class="input-group-text text-dark">
                                    <i class="bi bi-key"></i>
                                </span>
                            <input type="password" name="passwordConfirmed" required class="form-control" placeholder="Confirm your password" aria-label="Password" >
                        </div>

                        <button type="submit" class="btn btn-login btn-primary ">Signup</button>
                        <a href="<?= $web_root ?>main/login"
                           class="btn w-100 p-2 btn-outline-danger">Cancel</a>
                    </form>
                    <?php if (count($errors) != 0): ?>
                        <div class='errors'>
                            <br><br><p>Please correct the following error(s) :</p>
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
</div>
</body>
</html>
