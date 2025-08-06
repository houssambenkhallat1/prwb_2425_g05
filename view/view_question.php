<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <title> Question</title>
</head>
<body>
<div class="container-sm login-container">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex align-items-center p-2 bg-gradient">
                <a href="form/manage/<?= $form_id ?>/<?= $search_form ?>" class="text-white text-decoration-none me-2">
                    <i class="bi bi-arrow-left text-primary fs-4"></i>
                </a>
                <div class="ms-auto">
                    <label for="submitButton" style="cursor: pointer;">
                        <i class="bi bi-clipboard text-primary fs-4"></i>
                    </label>
                </div>
            </div>
            <div class="vh-100 form-container rounded p-2">
                <form action="question/<?= $isEdit ? 'update_question/'  : 'save_question/' ?><?= $search_form ?>" method="POST">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $question->get_id() ?>">
                        <input type="hidden" name="form_id" value="<?= $form_id ?>">
                    <?php endif; ?>

                    <?php if (!$isEdit): ?>
                        <input type="hidden" name="form_id" value="<?= $form_id ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                               id="title" name="title"
                               value="<?= $isEdit ? $question->get_title() : ($title ?? '') ?>" required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback"><?= $errors['title'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                                  id="description" name="description"
                                  rows="4"><?= $isEdit ? $question->get_description() : ($description ?? '') ?></textarea>
                        <?php if (isset($errors['description'])): ?>
                            <div class="invalid-feedback"><?= $errors['description'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select class="form-select <?= isset($errors['type']) ? 'is-invalid' : '' ?>"
                                name="type" id="type" required>
                            <option value="" disabled <?= !$isEdit && !isset($selectedType) ? 'selected' : '' ?>>
                                --select a type--
                            </option>
                            <?php foreach($question_types as $type): ?>
                                <option value="<?= $type->value ?>"
                                    <?= ($isEdit && $question->get_type() === $type) ||
                                    (!$isEdit && isset($selectedType) && $selectedType === $type->value) ? 'selected' : '' ?>>
                                    <?= $type->value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['type'])): ?>
                            <div class="invalid-feedback"><?= $errors['type'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="required" name="required"
                                <?= ($isEdit && $question->is_required()) ||
                                (!$isEdit && isset($required) && $required) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="required">
                                Required question
                            </label>
                        </div>
                    </div>

                    <input type="submit" id="submitButton" hidden>
                </form>
                <?php
                if (!empty($messages)) {
                    switch($messages) {
                        case 'updated':
                            echo '<div class="alert alert-success">Formulaire mis à jour avec succès</div>';
                            break;
                        case 'saved':
                            echo '<div class="alert alert-success">Formulaire créé avec succès</div>';
                            break;
                    }
                }
                ?>

            </div>
        </div>
    </div>
</div>

<!-- Add validation script -->
<script src="lib/js/question.js"></script>
</body>
</html>