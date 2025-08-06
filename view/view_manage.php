<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/view-form.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-ui-touch-punch@0.2.3/jquery.ui.touch-punch.min.js"></script>
    <!-- Important: Charger Bootstrap JavaScript complet via cette seule ligne plutôt que de charger séparément popper.js et bootstrap.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <title>question</title>
</head>
<body>
<div class="container-sm login-container">
    <div class="row">
        <div class="col-12">
            <div class="vh-auto p-2">
                <div>
                    <h4>Form "<?= $form->get_title() ?>" by <?= $form->get_owner_name() ?></h4>
                </div>
                <div>
                    <p><?= $form->get_description() ?></p>
                </div>
                <form action="form/switch_form_state<?= $search_form ?>" method="post" class="my-3">
                    <input type="hidden" name="formId" value="<?= $form->get_id() ?>">
                    <input type="hidden" name="is_public" value="<?= $form->is_public() ? '0' : '1' ?>">

                    <button type="submit" class="switch-button <?= $form->is_public() ? 'switch-on' : 'switch-off' ?>">
                        <span class="switch-toggle <?= $form->is_public() ? 'right' : 'left' ?>"></span>
                    </button>
                </form>

                <div><h4>Questions</h4></div>
                <?php if (isset($questions) && is_array($questions) && count($questions) > 0): ?>
                    <div class="questions-container">
                        <?php  foreach ($questions as $question): ?>
                            <div class="form-container rounded mb-3" data-id="<?= $question->get_id() ?>">
                                <div class="card text-bg-dark">
                                    <div class="card-body d-flex justify-content-between align-items-start">
                                        <!-- Partie de gauche : titre, description, etc. -->
                                        <div>
                                            <h5 class="card-title mb-2">
                                                <?= htmlspecialchars($question->get_title()) ?>
                                            </h5>
                                            <p class="card-subtitle text-secondary mb-2">
                                                <?= htmlspecialchars($question->get_description() ?? "No description available") ?>
                                            </p>
                                            <p class="text-secondary mb-0">
                                                Type: <?= htmlspecialchars($question->get_type()->value ?? "N/A") ?>
                                            </p>
                                            <p class="text-secondary mb-0 fst-italic">
                                                Required: <?= $question->is_required() ? 'true' : 'false' ?>
                                            </p>
                                        </div>

                                        <!-- Partie de droite : flèches + icônes d'action -->
                                        <?php if (empty($instances)): ?>
                                            <div class="text-end">
                                                <i class="bi bi-grip-vertical handle text-secondary fs-5"></i>
                                                <div class="mb-2">
                                                    <?php if(Configuration::get("disable_js")): ?>
                                                        <form method="POST" action="question/up">
                                                            <input  type="hidden" name="question_id" value="<?= $question->get_id() ?>">
                                                            <button type="submit"  class="btn btn-sm btn-outline-secondary "

                                                                <?= $question->get_idx() === 1 ? 'disabled' : '' ?>>
                                                                <i class="bi bi-arrow-up"></i>
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="question/down">
                                                            <input  type="hidden" name="question_id" value="<?= $question->get_id() ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary "

                                                                <?= $question->get_idx() === $question->get_last_question_idx() ? 'disabled' : '' ?>>
                                                                <i class="bi bi-arrow-down"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <button class="btn btn-sm btn-up <?= $question->get_idx() === 1 ? 'btn-outline-dark' : 'btn-outline-secondary' ?>"
                                                                value="<?= $question->get_id() ?>"
                                                            <?= $question->get_idx() === 1 ? 'disabled' : '' ?>>
                                                            <i class="bi bi-arrow-up"></i>
                                                        </button>

                                                        <button class="btn btn-sm btn-down <?= $question->get_idx() ===  $question->get_last_question_idx() ? 'btn-outline-dark' : 'btn-outline-secondary' ?>"
                                                                value="<?= $question->get_id() ?>"
                                                            <?= $question->get_idx() ===  $question->get_last_question_idx() ? 'disabled' : '' ?>>
                                                            <i class="bi bi-arrow-down"></i>
                                                        </button>
                                                    <?php endif; ?>

                                                </div>
                                                <div>
                                                    <!-- Formulaire POST pour l'édition -->
                                                    <!-- Bouton Edit -->
                                                    <?php if (Configuration::get("disable_js")): ?>
                                                        <form method="POST" action="question/edit" style="display: inline;">
                                                            <input type="hidden" name="question_id" value="<?= $question->get_id() ?>">
                                                            <button type="submit" class="btn btn-link p-0 text-decoration-none border-0">
                                                                <i class="bi bi-send text-success fs-5"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <form action="question/edit<?= $search_form ?>" method="POST" style="display: inline;" class="me-2">
                                                            <input type="hidden" name="question_id" value="<?= $question->get_id() ?>">
                                                            <button type="submit" class="btn btn-link p-0 text-decoration-none">
                                                                <i class="bi bi-pencil text-primary fs-5"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <!-- Formulaire POST pour la suppression -->
                                                    <!-- Bouton Delete -->
                                                    <?php if (Configuration::get("disable_js")): ?>
                                                        <a href="question/confirm_delete/<?= $question->get_id() ?>/<?= $form->get_id() ?>"
                                                           class="btn btn-danger btn-sm">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <button type="button"
                                                                class="btn btn-danger btn-sm delete-question"
                                                                data-question-id="<?= $question->get_id() ?>"
                                                                data-form-id="<?= $form->get_id() ?>"
                                                                data-question-title="<?= htmlspecialchars($question->get_title()) ?>">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    </div>
    <!-- Modal de confirmation -->
    <div class="modal fade" id="deleteQuestionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the question: <strong id="questionTitle"></strong>?</p>
                    <p class="text-danger">This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
            </div>
        </div>
    </div
</div>
    <!-- Charger d'abord le script de manage.js pour les fonctionnalités de base -->
    <script src="lib/js/manage.js"></script>
    <!-- Puis ajouter le script spécifique pour la gestion de la modale de suppression -->

</body>
</html>