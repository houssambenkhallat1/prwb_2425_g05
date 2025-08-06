<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?= $web_root ?>"/>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="css/styles.css"/>
    <link rel="stylesheet" href="css/confirmation-box.css">
    <title>Confirmation Box</title>
</head>
<body>
    <div class="confirmation-box">
        <div class="icon">
            <i class="bi bi-question-lg"></i>
            <hr class="divider">
        </div>

        <div class="message">
            <p>You have already answered this form.</p>
            <p>You can view your submission or submit again.</p>
            <p>What would you like to do?</p>
        </div>

        <div>
            <!-- READ ONLY -->
            <a href="instance/read/<?= $instance->get_id() ?>" class="btn btn-success">View submission</a>
            <!-- CREATE A NEW INSTANCE -->
            <form action="instance/create" method="POST" style="display: inline;">
                <button type="submit" name="form_id" value="<?= $instance->get_form() ?>" class="btn btn-danger ">Submit again</button>
            </form>
            <!-- REDIRECT TO FORMS -->
            <a href="form" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</body>
</html>
