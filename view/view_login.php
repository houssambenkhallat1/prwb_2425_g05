<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <title>Login</title>
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
                    <h2>Sign in</h2>
                </div>
                <hr class="custom-light-grey">
                <div>
                    <form method="POST" action="Main/login">
                        <!-- username -->
                        <div class="input-group mb-3">
                                <span class="input-group-text" id="username">
                                    <i class="bi bi-person"></i>
                                </span>
                            <input type="text" name="email" required class="form-control " placeholder="Username"
                                   aria-label="Username" >
                        </div>

                        <!-- password -->
                        <div class="input-group mb-3">
                                <span id="password" class="text-dark input-group-text">
                                    <i class="bi bi-key"></i>
                                </span>
                            <input type="password" name="password" required class="form-control" placeholder="Password"
                                   aria-label="Password">
                        </div>

                        <button type="submit" class="btn btn-login btn-primary ">Login</button>
                        <a href="<?= $web_root ?>main/guest"
                           class="btn btn-guest btn-secondary">Continue as guest</a>

                    </form>
                    <div>
                        <?php if (isset($errors) && count($errors) != 0): ?>
                            <div class='alert alert-danger'>
                                <br><br><p>Please correct the following error(s) :</p>
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-center mt-3 mb-2">
                        <a href="main/signup">New here? Click here to subscribe</a>
                    </div>
                    <hr class="custom-light-grey">

                    <div class="d-flex flex-column align-items-center py-3">
                        <h4 class="text-warning">For Debug Purpose</h4>
                        <span class="text-white">
                                Login as <a href="<?= $web_root ?>main/loginAsBepenelle"
                                            class="text-white fw-bold text-decoration-none">bepenelle@epfc.eu</a>
                            </span>
                        <span class="text-white">
                                Login as <a href="<?= $web_root ?>main/loginAsBoverhaegen"
                                            class="text-white fw-bold text-decoration-none">boverhaegen@epfc.eu</a>
                            </span>
                        <span class="text-white">
                                Login as <a href="<?= $web_root ?>main/loginAsMamichel"
                                            class="text-white fw-bold text-decoration-none">mamichel@epfc.eu</a>
                            </span>
                        <span class="text-white">
                                Login as <a href="<?= $web_root ?>main/loginAsxapigeolet"
                                            class="text-white fw-bold text-decoration-none">xapigeolet@epfc.eu</a>
                            </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
