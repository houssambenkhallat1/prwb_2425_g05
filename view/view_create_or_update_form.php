<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <base href="<?= $web_root ?>"/>
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="css/styles.css"/>
    <link rel="stylesheet" href="css/view-form.css"/>
    <title>Create a form</title>
</head>
<body>
<!-- Conteneur principal -->
<div class="container-sm login-container ">
    <div class="row">
        <div>
            <!-- Barre de navigation ou header -->
            <header class="d-flex align-items-center p-2 bg-gradient">
                <a href="form/list/<?= $search_form ?>" class="text-white text-decoration-none me-2">
                    <i class="bi bi-arrow-left text-primary fs-4"></i></a>
                <!-- Espace au milieu, éventuellement un titre -->
                <div class="ms-auto text-end"><label for="submitButton" style="cursor: pointer;">
                        <i class="bi bi-clipboard text-primary fs-4"></i>
                    </label>
                </div>
            </header>

            <main class="vh-100 form-container rounded p-2">
                <form id="create-form"
                      action="form/create_or_update/<?= $form !== null ? $form->get_id() : '' ?>/<?= $search_form ?>"
                      method="POST">

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>

                        <input
                                type="text"
                                id="title"
                                name="title"
                                class="form-control
                   <?php if (!empty($errors['title'])): ?>
                     is-invalid
                   <?php endif; ?>"
                                value="<?= $form !== null ? $form->get_title() : '' ?>"
                        />

                        <?php if (!empty($errors['title'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['title'] ?>
                            </div>
                        <?php endif; ?>
                    </div>


                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>

                        <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="form-control
                   <?php // si tu veux gérer "is-invalid" pour description:
                                if (!empty($errors['description'])):
                                    echo 'is-invalid';
                                endif;
                                ?>"
                        ><?= $form !== null ? $form->get_description() : '' ?></textarea>

                        <!-- Message d’erreur pour le champ Description -->
                        <?php if (!empty($errors['description'])): ?>
                            <div class="invalid-feedback">
                                <?= $errors['description'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Case à cocher "Public form" -->
                    <div class="form-check mb-3">
                        <input
                                class="form-check-input"
                                type="checkbox"
                                value="1"
                                id="flexCheckDefault"
                                name="public"
                            <?php
                            if ($form !== null && $form->is_public()) {
                                echo 'checked';
                            }
                            ?>
                        />
                        <label class="form-check-label" for="flexCheckDefault">
                            Public form
                        </label>
                    </div>

                    <input type="submit" id="submitButton" hidden>
                </form>
                <div>
                    <?php
                    if (!empty($msg)) {
                        switch($msg) {
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
            </main>
        </div>
    </div>
</div>

</body>
</html>