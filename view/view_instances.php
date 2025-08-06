    <!doctype html>
    <html lang="fr">
    <head>
        <base href="<?= $web_root ?>"/>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css"/>
        <!-- Bootstrap Icons (facultatif) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
              crossorigin="anonymous">
        <link rel="stylesheet" href="css/styles.css">
        <title>Instances soumises</title>
    </head>
    <body class="bg-dark text-light">
    <!-- Retour / Titre principal -->
    <div class="container-sm login-container">
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex align-items-center p-2 bg-gradient">
                    <a href="form/manage/<?= $form->get_id() ?><?= $search_form?>" class="text-white text-decoration-none me-2">
                        <i class="bi bi-arrow-left text-primary fs-4"></i>
                    </a>
                </div>

                <!-- Contenu principal -->
                <div class="container py-4">
                    <h4 class="mb-4">
                        <?php echo 'Submitted instance(s) of form " ' . $form->get_title() . '"' ?>
                    </h4>

                    <!-- Inverser l'ordre chronologique en plaçant la plus récente en premier -->
                    <form action="instance/delete_confirmation_box<?= $search_form ?>" method="post">
                        <?php foreach ($instances as $instance): ?>
                            <!-- Instance 1 -->
                            <div class=" py-4 mb-3 p-3 rounded bg-secondary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-1 fw-bold"><?= $instance->get_started()->format('d M Y H:i:s') ?></p>
                                        <p class="mb-1">Answered by <?= $instance->get_owner_name() ?></p>
                                        <a href="instance/read/<?= $instance->get_id() ?>"  class="text-decoration-underline text-light">
                                            Review
                                        </a>
                                    </div>
                                    <!-- Case à cocher pour sélectionner -->
                                    <div>
                                        <input type="checkbox" name="selected_instances[]" value="<?= $instance->get_id() ?>">
                                    </div>
                                    <input type="hidden" name="form_id" value="<?= $form->get_id() ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Bouton de suppression -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-danger">Delete selected</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>