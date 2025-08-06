<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <title>Settings</title>
</head>
<body>
<div class="container-sm login-container">


    <div class="row">
        <div class="col-12">
            <!-- Header with back button -->
            <div class="d-flex align-items-center mb-4">
                <a href="main" class="text-white text-decoration-none">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h4 class="mb-0 ms-3">Settings</h4>
            </div>

            <!-- User greeting -->
            <div class="mb-4">
                <h5></h5>
            </div>
            <div>
                <p>
                    <?php echo'HEY '.$user->get_full_name().'!'?>
                </p>
            </div>

            <!-- Settings options -->
            <div class="list-group bg-dark">
                <a href="main/edit_profile" class="list-group-item bg-dark text-white border-0 d-flex align-items-center py-3">
                    <i class="fas fa-user me-3"></i>
                    Edit profile
                </a>
                <a href="main/change_password" class="list-group-item bg-dark text-white border-0 d-flex align-items-center py-3">
                    <i class="fas fa-key me-3"></i>
                    Change password
                </a>
            </div>
        </div>
    </div>




</div>
</body>
</html>
