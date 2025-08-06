<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <base href="<?= $web_root ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    <link rel="stylesheet" href="css/styles.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-ui-touch-punch@0.2.3/jquery.ui.touch-punch.min.js"></script>
    <!-- Important: Charger Bootstrap JavaScript complet via cette seule ligne plutôt que de charger séparément popper.js et bootstrap.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Session 1</title>

</head>

<body>
<div class="container-sm login-container">
    <!-- Formulaire de sélection d'un formulaire public -->
    <form method="post" action="session1/index">
        <h6>Public forms :</h6>
        <select name="form_id">
            <option value="">-- Select a form --</option>
            <!-- Boucle sur les formulaires publics -->
            <?php foreach ($public_forms as $form): ?>
                <option value="<?= $form->get_id(); ?>"
                    <?= $selected_form_id == $form->get_id() ? 'selected' : '' ?>>
                    <?= $form->get_title(); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Refresh</button>
    </form>

    <!-- Si des instances sont définies -->
    <?php if (is_array($instances)): ?>
        <?php if (count($instances) === 0): ?>
            <p class="mt-5">Pas d'instances pour ce formulaire</p>
        <?php else: ?>
            <h6 class="mt-5">Instances :</h6>
            <!-- Boucle sur les instances -->
            <?php foreach ($instances as $instance): ?>
                <div class="mb-3 flex">
                    <!-- Affichage de l'instance et du propriétaire -->
                    <div id="<?= $instance->get_id() ?>">
                        Instance <?= $instance->get_id() ?> by <?= $instance->get_owner_name() ?>
                    </div>
                    <!-- Bouton pour déplacer l'instance vers "guest" -->
                    <button <?= $instance->get_user_role() == 'guest' ? 'disabled' : '' ?>
                            class="btn btn-primary btn-small btn-guest-user"
                            data-instance-id="<?= $instance->get_id() ?>">
                        Move to guest
                    </button>
                    <!-- Bouton pour déplacer vers l'utilisateur connecté -->
                    <button <?= $instance->get_user() == $user->get_id() ? 'disabled' : '' ?>
                            class="btn btn-primary btn-small btn-logged-user"
                            data-instance-id="<?= $instance->get_id() ?>">
                        Move to logged user
                    </button>
                </div>
            <?php endforeach ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<script src="lib/js/session1.js"></script>
</body>
</html>
