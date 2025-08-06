<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?= $web_root ?>"/>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="css/styles.css"/>
    <title>Delete question</title>
</head>
<body>
<div class="container">
    <div class="confirmation-box bg-dark p-4 rounded mt-5">
        <h2 class="text-danger mb-4"><i class="bi bi-exclamation-triangle"></i> Confirm Deletion</h2>

        <p>Are you sure you want to delete the question:</p>
        <p class="lead">"<?= $question->get_title() ?>"</p>
        <p class="text-danger"><strong>This action cannot be undone!</strong></p>

        <form action="question/delete" method="post" class="d-inline">
            <input type="hidden" name="question_id" value="<?= $question->get_id() ?>">
            <input type="hidden" name="form_id" value="<?= $form_id ?>">
            <button type="submit" class="btn btn-danger">Delete Permanently</button>
        </form>

        <a href="form/manage/<?= $form_id ?>" class="btn btn-secondary ms-2">Cancel</a>
    </div>
</div>
</body>
</html>