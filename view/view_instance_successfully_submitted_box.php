<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?= $web_root ?>"/>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="css/successfully-submitted-box.css">
    <link rel="stylesheet" href="css/styles.css"/>
    <title>Notification successfully submitted</title>
</head>
<body>
<div class="successfully-box">
    <div class="icon">
        <i class="bi bi-check2-all"></i>
        <hr class="divider">
    </div>
    <div class="message">
        <p>The form has been successfully submitted</p>
    </div>
    <div class="d-flex">
        <a href="form/list/<?= $search_form ?>" class="btn btn-primary ms-auto">Ok</a>
    </div>
</div>
</body>
</html>