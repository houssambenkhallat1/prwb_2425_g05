<?php
// La variable $error sera passée par le contrôleur (ControllerMain::error())
// ou par Tools::abort() qui appelle cette vue.
$error_message = isset($error) && is_string($error) ? htmlspecialchars($error) : "An unexpected error occurred.";
$error_title = "Error";

// Tu peux personnaliser le titre en fonction de certains codes d'erreur si tu le souhaites
// Par exemple, si $error est un code comme "unauthorized_access", tu pourrais mettre un titre plus spécifique.
if (isset($error_code) && $error_code === "404") {
    $error_title = "Page Not Found";
    $error_message = "The page you are looking for does not exist or has been moved.";
} elseif (isset($error_code) && $error_code === "403") {
    $error_title = "Access Denied";
    $error_message = "You do not have permission to access this page or perform this action.";
}

// Lien de retour par défaut
$back_link = "main/index"; // Page de login si non connecté, ou liste des formulaires si connecté
$back_link_text = "Go to Homepage";

// Optionnellement, si le contrôleur peut passer un lien de retour spécifique
if (isset($custom_back_link) && isset($custom_back_link_text)) {
    $back_link = $custom_back_link;
    $back_link_text = $custom_back_link_text;
} elseif (isset($_SESSION['user'])) {
    $back_link = "form/index"; // Si connecté, retourne à la liste des formulaires
    $back_link_text = "Back to Forms";
}

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?= $web_root ?>"/>
    <!-- Utilise tes chemins CSS locaux si Bootstrap est téléchargé -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="css/styles.css"/> <!-- Ton fichier CSS principal -->
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa; /* Un fond neutre */
            padding: 1rem;
        }
        .error-box {
            background-color: #fff;
            padding: 2rem 2.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .error-box .icon {
            font-size: 3rem; /* Ajuste la taille de l'icône */
            color: #dc3545; /* Rouge pour erreur */
            margin-bottom: 0.5rem;
        }
        .error-box .error-title {
            font-size: 1.75rem;
            font-weight: 500;
            color: #343a40;
            margin-bottom: 1rem;
        }
        .error-box .message {
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 1rem;
            line-height: 1.6;
        }
        .error-box .btn {
            margin-top: 0.5rem;
            padding: 0.5rem 1.5rem;
        }
        .error-box hr.divider {
            border: 0;
            height: 1px;
            background-image: linear-gradient(to right, rgba(0,0,0,0), rgba(0,0,0,0.15), rgba(0,0,0,0));
            margin: 1.5rem 0;
        }
    </style>
    <title><?= $error_title ?> - <?= Configuration::get('app_name', 'Form App') ?></title>
</head>
<body>
<div class="error-box">
    <div class="icon">
        <!-- Choisis une icône appropriée pour les erreurs -->
        <i class="bi bi-exclamation-octagon-fill"></i> <!-- Alternative: bi-emoji-frown, bi-bug-fill -->
    </div>
    <h2 class="error-title"><?= $error_title ?></h2>
    <hr class="divider">
    <div class="message">
        <p><?= $error_message ?></p>
        <?php if (Configuration::is_dev() && isset($exception_details)): // Afficher plus de détails en mode dev ?>
            <p class="text-muted small mt-3"><strong>Developer Details:</strong><br><?= nl2br(htmlspecialchars($exception_details)) ?></p>
        <?php endif; ?>
    </div>

    <div>
        <a href="<?= $back_link ?>" class="btn btn-primary">
            <i class="bi bi-house-door-fill"></i> <?= $back_link_text ?>
        </a>
        <!-- Optionnel: un bouton pour revenir à la page précédente de l'historique du navigateur (ne respecte pas la consigne de gérer le retour explicitement) -->
        <!-- <button onclick="window.history.back();" class="btn btn-secondary">Go Back</button> -->
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>