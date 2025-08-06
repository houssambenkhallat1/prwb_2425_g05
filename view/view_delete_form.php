<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="css/cancel-submission-box.css">
    <link rel="stylesheet" href="css/styles.css"/>
    <title>Delete instance confirmation box</title>
</head>
<body>
<div class="cancellation-box">
    <div class="row-cols-lg-1">
        <div>
            <i class="icon bi bi-trash-fill text-danger"></i> <!-- Icône plus grande et rouge -->
        </div>
        <div>
            <h3 class="text-danger">Are you sure ?</h3>
            <hr class="divider">
        </div>
        <div class="p">
            <p>Do you really want to delete this form and all dependencies ?</p>
            <p>This process cannot be undone.</p>
            <a href="form/manage/<?= $form->get_id() ?>/<?= $search_form ?>" class="btn btn-secondary mb-2 w-100">Cancel</a>
            <form method="POST" action="form/delete/<?= $search_form ?>">
                <input type="hidden" name="form_id" value="<?=$form->get_id()?>">
                <button type="submit" class="btn btn-danger w-100">Delete</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
