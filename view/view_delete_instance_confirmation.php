<!DOCTYPE html>
<html lang="en">
<head>
    <base href="<?= $web_root ?>"/>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/confirmation-box.css">
    <title>Delete instance confirmation box</title>
</head>
<body>
    <div class="confirmation-box">
        <div class="d-flex flex-column justify-content-center">
            <div>
                <i class="icon bi bi-trash-fill text-danger"></i> <!-- Icône plus grande et rouge -->
            </div>
            <h3 class="text-danger">Are you sure ?</h3>
            <hr class="divider">
            <p>Do you really want to delete selected instance(s) and all dependencies ?</p>
            <p>This process cannot be undone.</p>
            <a href="instance/get_instances/<?=$form->get_id() ?><?= $search_form ?>" class="btn btn-secondary mb-2">Cancel</a>

            <form method="POST" action="instance/delete<?= $search_form ?>" class="d-block">
                <input type="hidden" name="form_id" value="<?=$form->get_id()?>">
                <?php foreach ($selected as $id): ?>
                    <input type="hidden" name="selected[]" value="<?= htmlspecialchars($id->get_id()) ?>">
                <?php endforeach; ?>
                <button type="submit" class="btn btn-danger w-100"> Delete </button>
            </form>
        </div>
    </div>
</body>
</html>
