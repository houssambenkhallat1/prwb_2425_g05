
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?= $web_root ?>"/>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="css/cancel-submission-box.css">
    <link rel="stylesheet" href="css/styles.css"/>
    <title>Cancel submission</title>
</head>
<body>
    <div class="cancellation-box">
        <div class="icon">
            <i class="bi bi-x-circle"></i>
            <h1>Are you sure?</h1>
            <hr class="divider">
        </div>
        <div class="message">
            <p>Do you really want to cancel your submission ?</p>
            <p>This process cannot be undone.</p>
        </div>
        <div class="buttons">
            <a href="instance/update/<?= $instance->get_id() ?>/1/<?= $search_form ?>" class="btn btn-secondary">Cancel</a>
            <a href="instance/cancel/<?= $instance->get_id()?>/<?= $search_form ?>" class="btn btn-danger">Confirm</a>
        </div>
    </div>
</body>
</html>