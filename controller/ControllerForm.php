<?php

require_once 'model/Form.php';
require_once 'model/User.php';
require_once 'model/FormColor.php'; // AJOUTEZ CETTE LIGNE
require_once 'model/Instance.php';
require_once 'model/Question.php';
require_once 'utils/EncodeUtils.php';
class ControllerForm extends Controller
{
    public function index(): void
    {
        $this->redirect("form", "list");
    }

    public function list(): void {
        $user = $this->get_user_or_redirect();
        $search_term = $_POST['search'] ?? '';
        $selected_colors = $_POST['colors'] ?? [];

        // Validation des couleurs
        $valid_colors = [];
        foreach ($selected_colors as $color) {
            try {
                $valid_colors[] = FormColor::from($color)->value;
            } catch (ValueError $e) {
                // Ignore invalid colors
            }
        }

        if (!empty($search_term) || !empty($valid_colors)) {
            $forms = Form::search_forms_with_colors($user->get_id(),
                !empty($search_term) ? "%$search_term%" : '',
                $valid_colors
            );
        } else {
            $forms = Form::get_forms($user->get_id());
        }

        (new View("forms"))->show([
            "user" => $user,
            "forms" => $forms,
            "search_term" => $search_term,
            "selected_colors" => $selected_colors,
            "all_colors" => FormColor::getAllColors()
        ]);
    }
//    public function decode_search_key(): void {
//        header('Content-Type: application/json');
//        $encoded_search = $_POST['encoded_search_key'] ?? null;
//        if ($encoded_search) {
//            echo json_encode([
//                'decoded_search_key' =>  EncodeUtils::url_safe_decode($encoded_search)
//            ]);
//        }
//        exit;
//    }

    public function search_json_forms(): void {
        header('Content-Type: application/json');
        $user = $this->get_user_or_false();

        if (!$user) {
            echo json_encode([
                'error' => [
                    'code' => 403,
                    'message' => 'Forbidden',
                ]
            ]);
            exit;
        }

        try {
            // Récupération des paramètres
            $search_query = isset($_POST['search_key']) ? trim($_POST['search_key']) : '';
            $colors = $_POST['colors'] ?? [];

            // Validation des couleurs
            $valid_colors = [];
            foreach ($colors as $color) {
                try {
                    $valid_colors[] = FormColor::from($color)->value;
                } catch (ValueError $e) {
                    // Ignore invalid colors
                }
            }

            $response = [
                'allowed_forms_ids' => [],
                'encoded_search_key' => EncodeUtils::url_safe_encode([
                    'search' => $search_query,
                    'colors' => $valid_colors
                ])
            ];

            if (!empty($search_query) || !empty($valid_colors)) {
                $response['allowed_forms_ids'] = Form::get_forms_by_search_and_colors($user, $search_query, $valid_colors);
            } else {
                // Si pas de critères, renvoyer tous les formulaires accessibles
                $all_forms = Form::get_forms($user->get_id());
                $response['allowed_forms_ids'] = array_map(fn($form) => $form->get_id(), $all_forms);
            }

            echo json_encode($response);
            exit;

        } catch (Exception $e) {
            error_log("Error in search_json_forms: " . $e->getMessage());
            echo json_encode([
                'error' => [
                    'code' => 500,
                    'message' => 'Internal server error: ' . $e->getMessage()
                ]
            ]);
            exit;
        }
    }
    // Nouvelle méthode pour décoder les critères de recherche
    public function decode_search_key(): void {
        header('Content-Type: application/json');

        try {
            $encoded_search = $_POST['encoded_search_key'] ?? null;
            if ($encoded_search) {
                $decoded = EncodeUtils::url_safe_decode($encoded_search);
                echo json_encode([
                    'decoded_search_key' => $decoded['search'] ?? '',
                    'decoded_colors' => $decoded['colors'] ?? []
                ]);
            } else {
                echo json_encode([
                    'decoded_search_key' => '',
                    'decoded_colors' => []
                ]);
            }
            exit;
        } catch (Exception $e) {
            error_log("Error in decode_search_key: " . $e->getMessage());
            echo json_encode([
                'decoded_search_key' => '',
                'decoded_colors' => []
            ]);
            exit;
        }
    }

    public function manage(): void
    {
        $user = $this->get_user_or_false();
        if(!$user || $user->is_guest() ) {
            $msg = 'doesnotexist..';
            $this->redirect('main', 'error',$msg);
        }

        // Valider que param1 est un entier
        if (!isset($_GET["param1"]) || !ctype_digit($_GET["param1"])) {
            $this->redirect("main", "error", "invalid_form_id_format");
            return;
        }
        $form_id = (int)$_GET["param1"];

        $form = Form::get_form($form_id);
        if (!$form || !$form->is_editor($user->get_id())) {
            $this->redirect("main", "error", "form_not_found_or_unauthorized");
            return;
        }

        $instances = Instance::get_instances($form_id);
        $questions = Question::get_questions($form->get_id());
        $search_form = isset($_GET['param2']) ? '/'.$_GET['param2'] : '';

        (new View("manage"))->show(["questions" => $questions, "form" => $form, "instances" => $instances, "search_form" => $search_form]);
    }

    public function delete(): void
    {
        $user = $this->get_user_or_false();
        if(!$user || $user->is_guest()){
            $this->redirect('main', 'error','permission_denied');
            return;
        }
        if (!isset($_POST['form_id'] ) || !ctype_digit($_POST['form_id'])) {
            $this->redirect("main", "error", "invalid_form_id_on_delete");
            return;
        }
        $search_form = $_GET['param1'] ?? '';

        // Vérifier si le form_id est fourni
        $form_id = $_POST['form_id'] ?? null;
        if (!$form_id) {
            $this->redirect("form","list", $search_form);
        } else {
            $form = Form::get_form($form_id);
            if (!$form || $form->get_owner_id() !== $user->get_id()) {
            $this->redirect("main", "error", "form_not_found_or_unauthorized_delete");
            return;
            }
            $form->delete();
            $this->redirect("form", "list", $search_form);
        }
    }

    public function delete_confirmation(): void
    {
        $user = $this->get_user_or_false();
        if(!$user || $user->is_guest()){
            $this->redirect('main', 'error','operation_forbidden_for_guest');
            return;
        }
        if (!isset($_POST['form_id'] ) || !ctype_digit($_POST['form_id'])) {
            $this->redirect("main", "error", "invalid_form_id_on_delete");
            return;
        }
        // Vérifier si le form_id est fourni
        $form_id = $_POST['form_id'] ?? null;
        $form = $user->get_form($form_id);
        if (!$form || $form->get_owner_id() !== $user->get_id()) {
            $this->redirect("main", "error", "cannot_confirm_delete_unauthorized");
            return;
        }
        $search_form = $_GET['param1'] ?? '';
        if ($form != null) {
            $this->redirect("form", "delete_form", $form_id, $search_form);
        } else {
            $this->redirect('form', 'list', $search_form);
        }
    }

    public function delete_form(): void
    {
        $user = $this->get_user_or_false();
        if(!$user || $user->is_guest()){
            $this->redirect('main', 'error','page_forbidden_for_guest');
            return;
        }
        if (!isset($_GET['param1']) || !ctype_digit($_GET['param1'])) {
            $this->redirect('main', 'error', 'invalid_id_for_delete_page');
            return;
        }
        $form_id = (int)$_GET['param1'];
        $form = $user->get_form($form_id);
        $search_form = $_GET['param2'] ?? '';
        if (!$form || $form->get_owner_id() !== $user->get_id()) {
            $this->redirect('main', 'error', 'unauthorized_view_delete_page'); //$search_form
            return;
        }
        (new View("delete_form"))->show(["form" => $form, "search_form" => $search_form]);
    }

    /**
     * Affiche le formulaire de création de formulaire
     * @return void
     * @throws Exception
     */
    public function create_or_update(): void
    {
        $user = $this->get_user_or_false();
        if(!$user || $user->is_guest()){
            $this->redirect('main', 'error','page_forbidden_for_guest');
            return;
        }
        $is_edit_mode = false;
        $errors = [];
        $form = null;
        $param2 = $_GET['param2'] ?? '';
        $param3 = $_GET['param3'] ?? '';

        $msg = '';
        $search_form = '';


        if (in_array($param2, ['updated', 'saved'], true)) {
            $msg = $param2;
            $search_form = $param3;
        } elseif (in_array($param3, ['updated', 'saved'], true)) {
            $msg = $param3;
            $search_form = $param2;
        } else {
            $search_form = $param2;
        }

        //var_dump("Message reçu: " . $msg . " | Search form: " . $search_form, $param2, $param3);


        if (isset($_GET["param1"])) {
            if (!ctype_digit($_GET["param1"])) {
                $this->redirect("main", "error", "invalid_form_id_for_edit");
                return;
            }

            $form_id = (int)$_GET["param1"];
            $form = Form::get_form($form_id);
            if (!$form && $form_id > 0) { // ID valide mais form non trouvé
                $this->redirect("main", "error", "form_to_edit_not_found");
                return;
            }
            if ($form && $form->get_owner_id() !== $user->get_id() && !$form->is_editor($user->get_id())) {
                $this->redirect("main", "error", "unauthorized_form_edit");
                return;
            }
            if ($form) { // Si le formulaire existe et droits OK
                $is_edit_mode = true;
                // Vérifier si on peut éditer (pas d'instances)
                if (count(Instance::get_instances($form->get_id())) > 0) {
                    // Rediriger vers manage avec un message d'erreur, ou afficher une erreur sur la page d'édition
                    $this->redirect("main", "error", "This_form_cannot_be_edited_because_it_already_has_responses");
                    // On pourrait vouloir afficher la page en lecture seule, ou rediriger.
                    // Pour l'instant, on continue et la vue create_or_update_form devra gérer l'affichage des erreurs.
                }
            }
        }

        if (($_SERVER['REQUEST_METHOD'] === 'POST')) {
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? null;
            $public = isset($_POST['public']);

            if ($is_edit_mode && $form && $form->get_id() === $form_id) {
                if (count(Instance::get_instances($form->get_id())) > 0) {
                    $this->redirect("main", "error", "This_form_cannot_be_edited_because_it_already_has_responses");
                    // Ne pas tenter de sauvegarder si erreurs
                } else {
                    $form->set_title($title);
                    $form->set_description($description);
                    $form->set_public($public);
                }
            } else {
                $form = new Form(0, $title, $description, $user->get_id(), $public);
                $is_edit_mode = false;
            }
            $errors = $form->validate();

            if (empty($errors)) {
                if ($form->get_id() != null) {
                    $form = $form->update();
                    if ($form) {
                        if(!empty($search_form)){
                            $this->redirect("form","create_or_update", $form->get_id(), $search_form, "updated");
                        }else{
                            $this->redirect("form","create_or_update", $form->get_id(), "updated");
                        }

                    }
                } else {
                    $form = $form->persist();
                    if ($form) {

                            $this->redirect("form","create_or_update", $form->get_id(), "saved");

                    }

                }

            }
        }

        (new View("create_or_update_form"))->show(["errors" => $errors, "form" => $form, "search_form" => $search_form, "msg" => $msg]);
    }

    public function switch_form_state(): void {
        $user = $this->get_user_or_false();
        if(!$user || $user->is_guest()){
            $this->redirect('main', 'error','does_not_exist..');
        }
        $search_form = $_GET['param1'] ?? '';
        if (isset($_POST['formId'])) {
            if (!ctype_digit($_POST['formId'])) {
                $this->redirect("main", "error", "invalid_formid_for_switch");
                return;
            }
            $formId = (int)$_POST['formId'];
            $form = Form::get_form($formId);

            if (!$form || $form->get_owner_id() !== $user->get_id()) {
                $this->redirect("main", "error", "unauthorized_switch_state"); //$search_form
                return;
            }
            $new_public_state = false;
            if (isset($_POST['is_public'])) {
                // Convertir la valeur de 'is_public' en booléen
                if (is_string($_POST['is_public'])) {
                    $new_public_state = strtolower($_POST['is_public']) === 'true' || $_POST['is_public'] === '1';
                } else {
                    $new_public_state = (bool)$_POST['is_public']; // Pour 1/0
                }
            }
            $form->set_public($new_public_state);
            $form->update();
            $this->redirect("form", "manage", $form->get_id(), $search_form);
        } else {
            $this->redirect('main', 'error','does_not_exist..'); //$search_form
        }
    }
    public function search_service() {
        $user = $this->get_user_or_false();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $query = isset($_POST['query']) ? trim($_POST['query']) : '';
        $forms = $query ?
            Form::search_forms($user->get_id(), "%$query%") :
            Form::get_forms($user->get_id());

        header('Content-Type: application/json');
        echo json_encode(array_map(function($form) use ($user) {
            $latestInstance = $form->get_last_instance();
            return [
                'id' => $form->get_id(),
                'title' => $form->get_title(),
                'description' => $form->get_description(),
                'owner_name' => $form->get_owner_name(),
                'is_public' => $form->is_public(),
                'has_questions' => $form->has_question(),
                'is_editor' => $form->is_editor($user->get_id()),
                'started_time' => $latestInstance ? $latestInstance->start($latestInstance->get_started()) : '',
                'process' => $form->get_last_instance_status()
            ];
        }, $forms));
    }

}