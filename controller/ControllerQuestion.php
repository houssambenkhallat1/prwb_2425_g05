<?php
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'framework/Model.php';
require_once 'model/QuestionType.php';
require_once 'model/Question.php';
require_once 'model/Form.php';
require_once 'model/User.php';

class ControllerQuestion extends Controller {

    public function index(): void {
        if (!$this->user_logged()) {
            $this->redirect();
        }

        $form_id = $_GET['param1'] ?? null;
        $search_form = $_GET['param2'] ?? '';

        if (!$form_id) {
            $this->redirect("form", "list", $search_form);
        }

        (new View("question"))->show([
            'question_types' => QuestionType::cases(),
            'form_id' => $form_id,
            'search_form' => $search_form
        ]);
    }

    // Add this method to your ControllerQuestion class
    public function check_title(): void {
        if (!$this->user_logged()) {
            $this->send_json_response(['error' => 'Not authenticated'], 401);
            return;
        }

        // Autoriser uniquement les requêtes POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->send_json_response(['error' => 'Method not allowed'], 405);
            return;
        }

        // Lire les données POST brutes
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // Valider les données
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->send_json_response(['error' => 'Invalid JSON format'], 400);
            return;
        }

        $title = trim($data['title'] ?? '');
        $form_id = $data['form_id'] ?? null;
        $question_id = $data['question_id'] ?? null;

        // Vérification des paramètres obligatoires
        if (empty($title) || empty($form_id)) {
            $this->send_json_response([
                'error' => 'Missing parameters',
                'received' => $data
            ], 400);
            return;
        }

        // Conversion des types
        $form_id = (int)$form_id;
        $question_id = $question_id ? (int)$question_id : null;

        // Vérification de l'unicité
        try {
            $exists = Question::is_title_unique($form_id, $title, $question_id);
            $this->send_json_response(['unique' => $exists]);
        } catch (Exception $e) {
            error_log("Check title error: " . $e->getMessage());
            $this->send_json_response(['error' => 'Database error'], 500);
        }
    }

// Helper method to send JSON responses
    private function send_json_response($data, $status_code = 200): void {
        header('Content-Type: application/json');
        http_response_code($status_code);
        echo json_encode($data);
        exit;
    }

    public function update_question(): void
    {
        $user = $this->get_user_or_false();
        if(!$user || $user->is_guest()) {
            $this->redirect('main', 'error','guest_cannot_add_question');
        }
        if (!isset($_POST['id']) || !ctype_digit($_POST['id'])) {
            $this->redirect('main', 'error', 'form_id_missing_for_add_question');
            return;
        }
        $errors = [];
        $isAjax = $this->is_ajax_request();
        $search_form = $_GET['param1'] ?? '';
        $question_id = $_POST['id'];
        $form_id = $_POST['form_id']??null;
        $question = Question::get_question($question_id);
        if (!$form_id) {
            $form_id = $question->get_form();
        }
        $form = Form::get_form($form_id);
        if (!$form || !$form->is_editor($user->get_id())) {
            $this->redirect('main', 'error', 'unauthorized_add_question_to_form');
            return;
        }
        if (count(Instance::get_instances($form->get_id())) > 0) {
            $this->redirect('form', 'manage', (string)$form->get_id(), 'form_has_instances_cant_add_q');
            return;
        }
        // Mise à jour de la question existante
        $question->set_title($_POST['title'] ?? '');
        $question->set_description($_POST['description'] ?? '');

        // Validation sécurisée du type
        $typeValue = $_POST['type'] ?? '';
        if (empty($typeValue)) {
            $errors['type'] = 'Type is required';
        } else {
            $type = QuestionType::tryFrom($typeValue);
            if ($type === null) {
                $errors['type'] = 'Invalid type selected';
            } else {
                $question->set_type($type);
            }
        }

        $question->set_required(isset($_POST['required']));
        if(empty($errors)){
            $result = $question->update();
            if (!is_array($result)) {
                if ($isAjax) {
                    $this->send_json_response([
                        'success' => true,
                        'form_id' => $form_id
                    ]);
                }else {
                    $this->redirect("form", "manage", $form_id, $search_form);//correction search form et msg saved
                }
                return;
            }
            $errors = array_merge($errors, $result);
        }
        (new View("question"))->show([
            "question_types" => QuestionType::cases(),
            "form_id" => $form_id,
            "question" => $question,
            "isEdit" => true,
            "search_form" => $search_form,
            "errors" => $errors

        ]);

    }

    public function save_question(): void
    {
        $user = $this->get_user_or_false();
        if(!$user || $user->is_guest()) {
            $this->redirect('main', 'error','guest_cannot_add_question');
        }
        if (!isset($_POST['form_id']) || !ctype_digit($_POST['form_id'])) {
            $this->redirect('main', 'error', 'form_id_missing_for_add_question');
            return;
        }
        $errors = [];
        $isAjax = $this->is_ajax_request();
        $form_id = $_POST['form_id'];
        $search_form = $_GET['param1'] ?? '';

        $form = Form::get_form($form_id);
        if (!$form || !$form->is_editor($user->get_id())) {
            $this->redirect('main', 'error', 'unauthorized_add_question_to_form');
            return;
        }
        if (count(Instance::get_instances($form->get_id())) > 0) {
            $this->redirect('form', 'manage', (string)$form->get_id(), 'form_has_instances_cant_add_q');
            return;
        }
        // Création d'une nouvelle question
        $questions = Question::get_questions($form_id);
        $idx = $questions ? Question::get_next_available_idx($form_id) : 1;

        // Validation sécurisée du type pour l'ajout aussi
        $typeValue = $_POST['type'] ?? '';
        if (empty($typeValue)) {
            $errors['type'] = 'Type is required';
        } else {
            $type = QuestionType::tryFrom($typeValue);
            if ($type === null) {
                $errors['type'] = 'Invalid type selected';
            } else {
                $question = new Question(
                    0,
                    (int)$form_id,
                    $idx,
                    $_POST['title'] ?? '',
                    $_POST['description'] ?? '',
                    $type,
                    isset($_POST['required'])
                );
                if (empty($errors)) {
                    $result = $question->persist();

                    if (!is_array($result)) {
                        if ($isAjax) {
                            $this->send_json_response([
                                'success' => true,
                                'form_id' => $form_id
                            ]);
                        } else {
                            $this->redirect("form", "manage", $form_id, $search_form);//correction search form et msg saved
                        }
                        return;
                    }
                    $errors = array_merge($errors, $result);
                } else {

                    if ($isAjax) {
                        $this->send_json_response([
                            'success' => false,
                            'errors' => $errors
                        ]);
                    } else {
                        (new View("question"))->show([
                            "question_types" => QuestionType::cases(),
                            "form_id" => $form_id,
                            "question" => $question,
                            "isEdit" => true,
                            "search_form" => $search_form,
                            "errors" => $errors

                        ]);//corection envoie l'errors
                    }
                }

            }
        }




    }

    public function add(): void {

        $user = $this->get_user_or_false();
        if(!$user || $user->is_guest()) {
            $this->redirect('main', 'error','guest_cannot_add_question');
        }
        if (!isset($_POST['form_id']) || !ctype_digit($_POST['form_id'])) {
            $this->redirect('main', 'error', 'form_id_missing_for_add_question');
            return;
        }
        $form_id = $_POST['form_id'];
        $search_form = $_GET['param1'] ?? '';

         $this->redirect('question', 'add_question', $form_id, $search_form);
    }
    public function add_question(): void {
        $user = $this->get_user_or_false();
        if(!$user || $user->is_guest()) {
            $this->redirect('main', 'error','guest_cannot_add_question');
        }
        if (!isset($_GET['param1']) || !ctype_digit($_GET['param1'])) {
            $this->redirect('main', 'error', 'form_id_missing_for_add_question');
            return;
        }
        $form_id = $_GET['param1'];
        $search_form = $_GET['param2'] ?? '';

        (new View("question"))->show([
            "question_types" => QuestionType::cases(),
            "form_id" => $form_id,
            "isEdit" => false,
            "search_form" => $search_form
        ]);


    }

    public function edit(): void {
        $user = $this->get_user_or_false();
        $search_form = $_GET['param1'] ?? '';
        if(!$user || $user->is_guest()) {
            $this->redirect('main', 'error','guest_cannot_edit_question');
        }
        if (!isset($_POST['question_id']) || !ctype_digit($_POST['question_id'])) {
                $this->redirect('main', 'error', 'form_id_missing_for_add_question');
                return;
        }
        $question_id =  $_POST['question_id'] ;
        $this->redirect("question", "edit_question", $question_id, $search_form);
    }
    public function edit_question(): void
    {
        $user = $this->get_user_or_false();
        if(!$user || $user->is_guest()) {
            $this->redirect('main', 'error','guest_cannot_edit_question');
        }
        $isAjax = $this->is_ajax_request();
        if (!isset($_GET['param1']) || !ctype_digit($_GET['param1'])) {
            $this->redirect('main', 'error', 'form_id_missing_for_add_question');
            return;
        }
        $question_id = $_GET['param1'];
        $search_form = $_GET['param2'] ?? '';
        $isEdit = $question_id !== null;
        $question = Question::get_question($question_id);
        if (!$question) {
            if ($isAjax) {
                $this->send_json_response(['error' => 'Question not found'], 404);
            } else {
                $this->redirect("form", "list", $search_form);
            }
            return;
        }
        $form = Form::get_form($question->get_form());
        if (!$form || !$form->is_editor($user->get_id())) {
            $this->redirect('main', 'error', 'unauthorized_add_question_to_form');
            return;
        }
        if (count(Instance::get_instances($form->get_id())) > 0) {
            $this->redirect('form', 'manage', (string)$form->get_id(), 'form_has_instances_cant_add_q');
            return;
        }
        $form_id = $question->get_form();
        (new View("question"))->show([
            "question_types" => QuestionType::cases(),
            "form_id" => $form_id,
            "question" => $question,
            "isEdit" => $isEdit,
            "search_form" => $search_form
        ]);
    }
    public function confirm_delete(): void {
        $user = $this->get_user_or_redirect();

        // Récupération des paramètres
        $question_id = $_GET['param1'] ?? null;
        $form_id = $_GET['param2'] ?? null;

        // Validation des paramètres
        if (!$question_id || !$form_id || !ctype_digit($question_id) || !ctype_digit($form_id)) {
            $this->redirect('main', 'error', 'missing_parameters');
        }

        $question = Question::get_question((int)$question_id);
        $form = Form::get_form((int)$form_id);

        // Vérification de l'existence et des droits
        if (!$question || !$form) {
            $this->redirect('main', 'error', 'does_not_exist');
        }

        if ($form->get_owner_id() !== $user->get_id()) {
            $this->redirect('main', 'error', 'unauthorized');
        }

        // Affichage de la vue de confirmation
        (new View("question_confirm_delete"))->show([
            "question" => $question,
            "form_id" => $form->get_id()
        ]);
    }

    public function delete(): void {
        $user = $this->get_user_or_redirect();
        $isAjax = $this->is_ajax_request();
        try {
        // Récupération des paramètres
        if ($isAjax) {
            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON data");
            }
            $id = $input['question_id'] ?? null;
            $form_id = $input['form_id'] ?? null;
        } else {
            $id = $_POST['question_id'] ?? null;
            $form_id = $_POST['form_id'] ?? null;
        }

        // Validation des paramètres
        if (!$id || !$form_id || !is_numeric($id) || !is_numeric($form_id)) {
            if ($isAjax) {
                $this->send_json_response(['error' => 'Invalid parameters'], 400);
            } else {
                $this->redirect('main', 'error', 'missing_parameters');
            }
            return;
        }

        // Conversion des types
        $id = (int)$id;
        $form_id = (int)$form_id;


            $question = Question::get_question($id);
            $form = Form::get_form($form_id);

            // Vérifications d'existence et d'autorisation
            if (!$question || !$form) {
                throw new Exception("Question or form not found");
            }

            if ($form->get_owner_id() !== $user->get_id()) {
                throw new Exception("Unauthorized operation");
            }

            // Suppression effective
            $success = $question->delete();
            if (!$success) {
                throw new Exception("Failed to delete question");
            }

            // Réponse
            if ($isAjax) {
                $this->send_json_response(['success' => true]);
            } else {
                $this->redirect("form", "manage", $form_id);
            }

        } catch (Exception $e) {
            error_log("Delete question error: " . $e->getMessage()); // Log pour débogage

            if ($isAjax) {
                $this->send_json_response([
                    'success' => false,
                    'error' => $e->getMessage()
                ], 400);
            } else {
                $this->redirect("form", "manage", $form_id ?? 0, "delete_error");
            }
        }
    }



    public function up(): void
    {
        $user=$this->get_user_or_false();
        if(!$user || $user->is_guest()){
            $this->redirect('main', 'error','guest_cannot_delete_question');
        }
        $id = $_POST['question_id'] ?? null;
        if (!$id) {
            $this->redirect("main", "error", "question_id_missing");
        }

        $question = Question::get_question((int)$id);
        if (!$question) {
            $this->redirect("main", "error", "question_id_missing");
        }

        // Si elle est déjà au premier rang (idx=1), on ne la monte pas
        if ($question->get_idx() <= 1) {
            $this->redirect("form", "manage/" . $question->get_form());
        }

        // Récupérer la question directement au-dessus
        $idxAbove = $question->get_idx() - 1;
        $questionAbove = Question::get_question_by_idx($question->get_form(), $idxAbove);
        if ($questionAbove) {
            // 1) Mémoriser leurs idx
            $idxA = $questionAbove->get_idx();
            $idxB = $question->get_idx();

            // 2) Donner un idx temporaire à questionAbove
            $questionAbove->set_idx(-1);
            $questionAbove->update_idx();

            // 3) Attribuer l’idx de questionAbove à question
            $question->set_idx($idxA);
            $question->update_idx();

            // 4) Attribuer l’idx initial de question à questionAbove
            $questionAbove->set_idx($idxB);
            $questionAbove->update_idx();
        }

        if ($this->is_ajax_request()) { // Vérifie si c'est une requête AJAX
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        } else {
            $this->redirect("form", "manage/" . $question->get_form());
        }
    }
    private function is_ajax_request(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    public function reorder(): void {
        header('Content-Type: application/json');

        try {
            if (!$this->is_ajax_request()) {
                throw new Exception("Requête non-AJAX");
            }

            $input = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("JSON invalide : " . json_last_error_msg());
            }

            if (empty($input['order']) || !is_array($input['order'])) {
                throw new Exception("Format de données invalide");
            }

            // Récupérer la première question pour connaître le form_id
            $firstQuestion = Question::get_question((int)$input['order'][0]);
            if (!$firstQuestion) {
                throw new Exception("Première question introuvable");
            }
            $formId = $firstQuestion->get_form();

            Model::getPDO()->beginTransaction();

            // ÉTAPE 1: Assigner des indices temporaires négatifs à toutes les questions
            // Cela évite les conflits avec la contrainte d'unicité (form, idx)
            foreach ($input['order'] as $questionId) {
                $question = Question::get_question((int)$questionId);
                if (!$question) {
                    throw new Exception("Question $questionId introuvable");
                }

                // Indice temporaire négatif unique
                $tempIdx = -1 - (int)$questionId;
                $question->set_idx($tempIdx);

                if (!$question->update_idx()) {
                    throw new Exception("Échec mise à jour index temporaire");
                }
            }

            // ÉTAPE 2: Assigner les indices définitifs dans l'ordre souhaité
            foreach ($input['order'] as $position => $questionId) {
                $question = Question::get_question((int)$questionId);

                // Indice définitif positif (1, 2, 3, ...)
                $question->set_idx($position + 1);

                if (!$question->update_idx()) {
                    throw new Exception("Échec mise à jour index définitif");
                }
            }

            Model::getPDO()->commit();

            echo json_encode([
                'status' => 'success',
                'new_order' => array_map(function($id) {
                    $q = Question::get_question((int)$id);
                    return ['id' => $id, 'idx' => $q->get_idx()];
                }, $input['order'])
            ]);

        } catch (Exception $e) {
            if (Model::getPDO()->inTransaction()) {
                Model::getPDO()->rollBack();
            }
            http_response_code(500);
            echo json_encode([
                'error' => $e->getMessage()
            ]);
            error_log("ERREUR REORDER: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
        exit;
    }
    public function down(): void
    {
        $user=$this->get_user_or_false();
        if(!$user || $user->is_guest()){
            $this->redirect('main', 'error','guest_cannot_delete_question');
        }

        $id = $_POST['question_id'] ?? null;
        if (!$id) {
            $this->redirect('main', 'error','does_not_exist');
        }

        $question = Question::get_question((int)$id);
        if (!$question) {
            $this->redirect('main', 'error','does_not_exist');
        }

        // Récupérer l'idx le plus grand pour éviter de dépasser la limite
        $lastIdx = $question->get_last_question_idx();
        if ($question->get_idx() >= $lastIdx) {
            $this->redirect("form", "manage/" . $question->get_form());
        }

        // Récupérer la question directement en-dessous
        $idxBelow = $question->get_idx() + 1;
        $questionBelow = Question::get_question_by_form_and_idx($question->get_form(), $idxBelow);
        if ($questionBelow) {
            // 1) Mémoriser leurs idx
            $idxA = $question->get_idx();
            $idxB = $questionBelow->get_idx();

            // 2) Donner un idx temporaire à question
            $question->set_idx(-1);
            $question->update_idx();

            // 3) Attribuer l’idx de question à questionBelow
            $questionBelow->set_idx($idxA);
            $questionBelow->update_idx();

            // 4) Attribuer l’idx initial de questionBelow à question
            $question->set_idx($idxB);
            $question->update_idx();
        }


        if ($this->is_ajax_request()) { // Vérifie si c'est une requête AJAX
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        } else {
            $this->redirect("form", "manage/" . $question->get_form());
        }
    }
}