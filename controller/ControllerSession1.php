<?php
require_once 'model/Form.php';
require_once 'model/User.php';
require_once 'model/Question.php';
require_once 'model/Answer.php';
require_once 'model/Instance.php';

class ControllerSession1 extends Controller {

    // Méthode affichant la page d'accueil de session1
    public function index(): void {
        // Récupère l'utilisateur connecté, ou false s'il n'existe pas
        $user = $this->get_user_or_false();

        // Si pas d'utilisateur ou si c'est un "guest", redirige vers une page d'accès refusé
        if (!$user || $user->is_guest()) {
            $this->redirect("main", "error", "access_denied!");
        }

        // Si on sélectionne un formulaire en POST
        // On fait un post-redirect-get et donc une redirection sur cette même action
        // la différence est que l'on va récupérer le form_id non pas en POST mais en GET
        // via $GET['param1']

        // Si le formulaire est soumis en POST avec un form_id valide
        if (isset($_POST['form_id']) && !empty($_POST['form_id'])) {
            $post_form_id = $_POST['form_id'];
            // Redirection (post-redirect-get) vers cette même action, en transmettant form_id en GET
            $this->redirect('session1', 'index', $post_form_id);
        }

        // Récupère tous les formulaires publics de la base
        $public_forms = Form::get_public_forms();
        $instances = null;

        // Récupère form_id passé en GET lors d'un PRG
        $form_id = $_GET['param1'] ?? null;
        if ($form_id) {
            // Récupération du formulaire correspondant
            $form = Form::get_form($form_id);
            // Si le formulaire n'existe pas, affiche une erreur
            if (empty($form)) {
                $this->redirect('main', 'error', 'no_form_found');
            }
            // Récupération de toutes les instances (réponses) liées au formulaire
            $instances = $form->get_instances();
        }

        // Charge la vue "session1" avec les données nécessaires
        (new View("session1"))->show([
            'public_forms' => $public_forms,
            'instances' => $instances,
            'selected_form_id' => $form_id,
            'user' => $user
        ]);
    }

    // Méthode AJAX pour transférer une instance vers l'utilisateur "guest" (id 6)
    public function move_to_guest() {
        // Vérification de la présence de instance_id en POST
        if (!isset($_POST['instance_id'])) {
            http_response_code(400); // Bad Request
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Missing instance_id']);
            return;
        }

        $instance_id = $_POST['instance_id'];
        $instance = Instance::get_instance($instance_id);

        // Si l'instance n'existe pas, erreur 404
        if (!$instance) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Instance not found']);
            return;
        }

        // Assigne l'instance au compte guest (id = 6)
        $instance->set_user(6);
        $instance->update_owner();

        // Réponse JSON pour confirmer l'opération
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'id' => $instance->get_id(),
            'name' => $instance->get_owner_name()
        ]);
    }

    // Méthode AJAX pour assigner une instance à l'utilisateur connecté
    public function move_to_logged_user() {
        // Vérification que instance_id est présent en POST
        if (!isset($_POST['instance_id'])) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Missing instance_id']);
            return;
        }
        $instance_id = $_POST['instance_id'];
        $instance = Instance::get_instance($instance_id);

        // Si l'instance n'existe pas, renvoie 404
        if (!$instance) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Instance not found']);
            return;
        }

        // Récupère l'utilisateur actuellement connecté
        $user = $this->get_user_or_false();
        if (!$user) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'User not found']);
            return;
        }

        // Assigne l'instance à l'utilisateur en cours
        $instance->set_user($user->get_id());
        $instance->update_owner();

        // Réponse JSON confirmant l'opération
        http_response_code(200);
        header('Content-type: application/json');
        echo json_encode([
            'success' => true,
            'id' => $instance->get_id(),
            'name' => $instance->get_owner_name()
        ]);
    }
}