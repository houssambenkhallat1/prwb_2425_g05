<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?= $web_root ?>"/>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="css/styles.css"/>
    <link rel="stylesheet" href="css/correct-errors-box.css">
    <title>Correct errors</title>
</head>
<body>
    <div class="correct-box">
        <div class="icon">
            <i class="bi bi-sign-stop-fill"></i>
            <h2>Are you sure ?</h2>
            <hr class="divider">
        </div>
        <div class="message">
            <p>You must correct all errors before submitting the form.</p>
        </div>
        <div class="d-flex">
            <a href="instance/update/<?= $instance->get_id() ?>/<?= $question->get_idx() ?>/<?= $search_form ?>" class="btn btn-primary ms-auto">Ok</a>
        </div>
    </div>
</body>
</html>