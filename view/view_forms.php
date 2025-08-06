<!doctype html>
<html lang="fr">
<head>
    <base href="<?= $web_root ?>"/>
    <meta charset="UTF-8">
    <link rel="icon" href="data:,">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/view-forms.css"/>
    <title>Forms</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .color-badge {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 5px;
            border: 2px solid #dee2e6;
        }
        .color-filter {
            cursor: pointer;
            transition: all 0.2s;
        }
        .color-filter.active {
            transform: scale(1.2);
            border: 3px solid #000;
        }
        .colors-container {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<!-- Nav -->
<?php include('menu.html') ?>
<!-- Sidebar -->
<?php if ($user->get_role() === "guest" ? include('sidebar_guest.html') : include('sidebar.html')) ?>

<!-- Main Content -->
<div id="main-container" class="container py-4">
    <!-- Formulaire de recherche (pour version PHP sans JS) -->
    <form method="POST" action="form/list" class="mb-4" id="search-form">
        <div class="input-group mb-3">
                <span class="input-group-text" id="search-forms">
                    <i class="bi bi-search"></i>
                </span>
            <input name="search" type="text" class="form-control" placeholder="Search forms..."
                   value="<?= htmlspecialchars($search_term ?? '') ?>"
                   aria-label="search-forms" aria-describedby="search-forms">
        </div>

        <!-- Filtres couleur -->
        <div class="mb-3">
            <label class="form-label">Filter by colors:</label>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($all_colors ?? FormColor::getAllColors() as $color): ?>
                    <?php $isSelected = in_array($color->value, $selected_colors ?? []); ?>
                    <label class="color-filter <?= $isSelected ? 'active' : '' ?>"
                           data-color="<?= $color->value ?>">
                        <input type="checkbox" name="colors[]" value="<?= $color->value ?>"
                            <?= $isSelected ? 'checked' : '' ?> class="d-none">
                        <span class="color-badge <?= $color->getCssClass() ?>"
                              title="<?= $color->getDisplayName() ?>"></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Search</button>
        <a href="form/list" class="btn btn-secondary">Clear</a>
    </form>

    <div id="no-results-message" class="alert alert-info d-none">No forms found.</div>

    <div class="row">
        <?php if (isset($forms)): ?>
            <?php foreach ($forms as $form): ?>
                <?php
                $instance = $form->get_last_instance($user->get_id());
                $startedTime = "";
                $process = "";

                if ($instance !== null) {
                    $startedTime = "started: " . $instance->start($instance->get_started());
                    $process = ($instance->get_completed() !== null) ? "completed: " . $instance->start($instance->get_completed()) : "in progress...";
                }

                $form_colors = $form->get_colors();
                ?>

                <div class="form-container rounded p-3 mb-3 col-sm-12 col-lg-3 me-lg-3" data-form-id="<?= $form->get_id() ?>" data-colors="<?= htmlspecialchars(json_encode(array_map(fn($c) => $c->value, $form_colors))) ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="mb-2"><?= htmlspecialchars($form->get_title()) ?></h5>
                            <p class="text-secondary mb-2"><?= htmlspecialchars($form->get_description() ?? "No description available") ?></p>

                            <!-- Affichage des couleurs -->
                            <?php if (!empty($form_colors)): ?>
                                <div class="colors-container">
                                    <?php foreach ($form_colors as $color): ?>
                                        <span class="color-badge <?= $color->getCssClass() ?>"
                                              title="<?= $color->getDisplayName() ?>"></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <p class="text-secondary mb-2 fst-italic">
                                By: <?= htmlspecialchars($form->get_owner_name()) ?></p>
                            <p class="text-secondary mb-1"><?= htmlspecialchars($startedTime) ?></p>
                            <p class="text-secondary mb-3"><?= htmlspecialchars($process) ?></p>
                            <div>
                                <?php if ($form->has_question()): ?>
                                    <a href="instance/open_instance/<?= $form->get_id() ?>"
                                       class="btn btn-primary px-4 me-2 search-propagation">Open</a>
                                <?php endif; ?>
                                <?php if ($user->get_role() !== "guest" and $form->is_editor($user->get_id())): ?>
                                    <a href="form/manage/<?= $form->get_id() ?>" class="btn btn-secondary px-4 search-propagation">Manage</a>
                                <?php endif; ?>
                            </div>
                            <div class="d-none">
                                <?php foreach($form->get_questions() as $question): ?>
                                    <p class="question-title">
                                        <?= htmlspecialchars($question->get_title()) ?>
                                    </p>
                                    <p class="question-description">
                                        <?= htmlspecialchars($question->get_description()) ?>
                                    </p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <i class="<?= $form->is_public() ? "bi bi-eye-fill text-secondary" : "bi bi-eye-slash-fill text-secondary" ?> "></i>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="lib/js/search_form.js"></script>
</body>
</html>