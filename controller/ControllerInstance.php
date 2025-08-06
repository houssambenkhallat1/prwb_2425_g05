<?php
require_once 'model/Form.php';
require_once 'model/User.php';
require_once 'model/Question.php';
require_once 'model/Answer.php';
require_once 'model/Instance.php';

class ControllerInstance extends Controller {

    public function index(): void {
        $this->redirect('form', 'list');
    }

    /**
     * Manage an instance
     * @return void
     */
    public function open_instance(): void {
        $user = $this->get_user_or_false();
        if (!$user){
            $msg = 'doesnotexist..';
            $this->redirect('main', 'error',$msg);
        }

        $form_id = $this->get_form_or_redirect();
        $form = $this->retrieve_form_by_id_or_redirect($form_id, $user);
        $user_instances = $form->get_instances_by_form_and_user($user->get_id());
        $search_form = $_GET['param2'] ?? '';


        // S'il n'existe aucune instance, on en crée une, puis on redirige vers son édition (action: update).
        if (empty($user_instances) || $user->get_role() === "guest") {
            $instance = new Instance(null, $form->get_id(), $user->get_id(), new DateTime(), null);
            $instance->persist();
            $user_instances = $form->get_instances_by_form_and_user($user->get_id());
            $instance = end($user_instances);
            $this->redirect('instance', 'update', $instance->get_id(), '1', $search_form);
        }

        // S'il existe une instance mais qu'elle n'est pas complétée
        $last_instance = end($user_instances);
        if ($last_instance && !$last_instance->get_completed()) {
            $this->redirect('instance', 'update', $last_instance->get_id(), '1', $search_form);
        }

        // Une instance existe et elle est complétée, on affiche la confirmation box avec les trois boutons
        $this->redirect('instance', 'confirmation_box', $last_instance->get_id(), $search_form);
    }

    public function create(): void {
        $user = $this->get_user_or_redirect();
        $form_id = $this->get_form_or_redirect();
        $form = $this->retrieve_form_by_id_or_redirect($form_id, $user);
        $user_instances = $form->get_instances_by_form_and_user($user->get_id());

        if (!empty($user_instances)) {
            $last_instance = end($user_instances);
            if ($last_instance && $last_instance->get_completed()) {
                $instance = new Instance(null, $form->get_id(), $user->get_id(), new DateTime(), null);
                $instance->persist();
                $user_instances = $form->get_instances_by_form_and_user($user->get_id());
                $new_instance = end($user_instances);
                $this->redirect('instance', 'update', $new_instance->get_id());
            }
        }

        $this->redirect('form');
    }

    public function read(): void {
        $user = $this->get_user_or_redirect();
        $instance = $this->get_instance_or_redirect();
        $form = $user->get_form($instance->get_form());
        $question = $this->get_question_by_idx($form);
        $answer = $question->get_answer($instance->get_id());
        $count_questions = $form->get_count_questions();

        (new View("read_instance"))->show([
            "instance" => $instance,
            "count_questions" => $count_questions,
            "form" => $form,
            "question" => $question,
            "answer" => $answer,
            "user" => $user
        ]);
    }

    public function update(): void {
        $user = $this->get_user_or_redirect();
        $instance = $this->get_instance_or_redirect();
        $search_form = isset($_GET['param3']) ? $_GET['param3'] : '';

        if($instance->get_completed()) {
            $this->redirect('form', 'list', $search_form);
        }

        $form = $user->get_form($instance->get_form());
        $question = $this->get_question_by_idx($form);
        $answer = $question->get_answer($instance->get_id());
        $count_questions = $form->get_count_questions();
        $errors = [];

        if ($answer) {
            $errors = $answer->validate($question);
        }
        // Gestion du formulaire
        if (isset($_POST['answer'])) {
            $answer = $this->saveAnswer($answer, $instance, $question);

            if($question->has_next_question_idx($count_questions)) {
                $this->redirect('instance', 'update', $instance->get_id(), $question->get_idx() +1, $search_form);
            }
            // Si toute les questions sont remplis et valides, on complète le formulaire
            // Todo: get all answers by instance, check their validity
            // if there are not valid, show the correct error box with a link that can redirect to the wrong answer
            $this->redirect_if_instance_not_valid($instance, $form, $search_form);

            $instance->setCompleted(new DateTime());
            $instance->update();
            $this->redirect('instance', 'success_box', $search_form);
        }

        (new View("update_instance"))->show([
            "form" => $form,
            "instance" => $instance,
            "question" => $question,
            "count_questions" => $count_questions,
            "answer" => $answer,
            "errors" => $errors,
            "user" => $user,
            "search_form" => $search_form
        ]);
    }


    /**
     * Si une instance existe et est completed
     * @return void
     * @throws Exception
     */
    public function confirmation_box() {
        if (!isset($_GET["param1"]) || !ctype_digit($_GET["param1"])) {
            $this->redirect("main", "error", "invalid_instance_id_format_confirm_box");
            return;
        }
        $instance_id = (int)$_GET["param1"];
        $instance = Instance::get_instance($instance_id);
        if (!$instance) {
            $this->redirect("main", "error", "instance_not_found_confirm_box");
            return;
        }
        $user = $this->get_user_or_false();
        if (!$user || $user->is_guest() || $instance->get_user() !== $user->get_id()) {
            $this->redirect("main", "error", "unauthorized_view_confirmation_box");
            return;
        }
        if (!$instance->get_completed()) {
            $this->redirect("instance", "update", (string)$instance->get_id(), "instance_not_submitted");
            return;
        }

        (new View("confirmation_submission_box"))->show(["instance" => $instance]);
    }

    /**
     * Si une instance contient des erreurs
     * @return void
     * @throws Exception
     */
    public function correct_errors_box() {
        $user = $this->get_user_or_redirect();
        $instance = $this->get_instance_or_redirect();
        $form = $user->get_form($instance->get_form());
        $question = $this->get_question_by_idx($form);
        $search_form = $_GET['param3'] ?? '';

        (new View("correct_errors_box"))->show([
            "instance" => $instance,
            "question" => $question,
            "search_form" => $search_form
        ]);
    }

    /**
     * Si l'instance est correctement complétée et soumise
     * @return void
     * @throws Exception
     */
    public function success_box() {
        $search_form = $_GET['param1'] ?? '';
        (new View("instance_successfully_submitted_box"))->show(["search_form" => $search_form]);
    }

    /**
     * Affiche le message de confirmation de suppression d'instance de formulaire
     * @return void
     * @throws Exception
     */
    public function cancel_submission_box() {
        $user = $this->get_user_or_false();
        $search_form = $_GET['param2'] ?? '';
        if (isset($_GET['param1'])) {
            $instance_id = (int)$_GET['param1'];
            $instance = Instance::get_instance($instance_id);
        }
        else {
            $this->redirect('form', 'list', $search_form);
        }

        (new View("cancel_submission_box"))->show(["instance" => $instance, "search_form" => $search_form]);
    }

    /**
     * Action qui supprime une instance via une instance id
     * @return void
     */
    public function cancel(): void {
        $user = $this->get_user_or_redirect();
        $search_form = $_GET['param2'] ?? '';
        if(isset($_GET['param1']) && ctype_digit($_GET['param1'])) {
            $instance_id = $_GET['param1'];
            $instance = Instance::get_instance($instance_id);
            if ($instance) {
                $instance->delete();
            }
        }else{
            $this->redirect('main', 'error','invalid_instance_id_format');
        }

        $this->redirect('form', 'list', $search_form);
    }



    public function get_instances(): void {
        $user= $this->get_user_or_redirect();


        if(isset($_GET['param1']) && ctype_digit($_GET['param1'])) {
            //$msg = $_GET['param2'];
            $search_form = isset($_GET['param2']) ? '/'.$_GET['param2'] : '';

            $form= Form::get_form((int)$_GET['param1']);
            $instances= Instance::get_instances((int)$_GET['param1']);
            (new View("instances"))->show(['instances' => $instances,'form' => $form, "search_form" => $search_form]);
        }else{
            $this->redirect('main', 'error','permission_denied');
        }
    }
    public function delete_all(): void {
        $user= $this->get_user_or_false();
        if (!isset($_POST["form_id"]) || !ctype_digit($_POST["form_id"])) {
            $this->redirect("main", "error", "invalid_form_id_format");
            return;
        }
        $form_id = (int)$_POST["form_id"] ;
        $form = Form::get_form($form_id);
        if($form->get_owner_id() !== $user->get_id()) {
            $this->redirect("main", "error", "unauthorized_view_confirmation_box");
        }
        $instances = Instance::get_instances($form_id);
        foreach ($instances as $instance) {
            $instance->delete();
        }
        $this->redirect('form','manage',$form_id);
    }
    public function clear_all_instances(): void {
        $user= $this->get_user_or_false();
        if (!isset($_GET["param1"]) || !ctype_digit($_GET["param1"])) {
            $this->redirect("main", "error", "invalid_form_id_format");
            return;
        }
        $form_id = (int)$_GET['param1'] ;
        $form = Form::get_form($form_id);
        if($form->get_owner_id() !== $user->get_id()) {
            $this->redirect("main", "error", "unauthorized_view_confirmation_box");
        }
        (new View("clear_all_instances"))->show(['form' => $form]);
    }

    public function delete(): void {
        $user= $this->get_user_or_redirect();
        if (!isset($_POST['form_id']) || !ctype_digit($_POST['form_id'])) {
            $this->redirect("main", "error", "form_id_missing_for_delete");
            return;
        }
        $form_id = (int)$_POST['form_id'];
        $form = Form::get_form($form_id); // Vérifier que le form existe
        if (!$form || !$form->is_editor($user->get_id())) { // Seul éditeur/owner du form
            $this->redirect("main", "error", "unauthorized_to_delete_instances");
            return;
        }
        $selected_ids_str = $_POST['selected'] ?? [];
        if (empty($selected_ids_str)) {
            $this->redirect("main", "error", "no_instances_selected");
            return;
        }

        foreach ($selected_ids_str as $id_str) {
            if (ctype_digit($id_str)) {
                $inst = Instance::get_instance((int)$id_str);
                if ($inst && $inst->get_form() === $form_id) {
                    $inst->delete();
                }
            }
        }

        $search_form = $_GET['param1'] ?? '';

        $this->redirect("instance","get_instances", $form_id, $search_form);
    }

    public function delete_confirmation_box() {
        $user = $this->get_user_or_redirect();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("main", "error", "confirm_delete_box_must_be_post");
            return;
        }
        $search_form = isset($_GET['param1']) ? '/'.$_GET['param1'] : '';

        if (!isset($_POST['form_id']) || !ctype_digit($_POST['form_id'])) {
            $this->redirect("main", "error", "form_id_missing_for_del_confirm_box");
            return;
        }
        $form_id = (int)$_POST['form_id'];
        $form = Form::get_form($form_id);
        if (!$form || !$form->is_editor($user->get_id())) {
            $this->redirect("main", "error", "unauthorized_view_del_confirm_box");
            return;
        }
        $selected_ids_str = $_POST['selected_instances'] ?? [];
        if (empty($selected_ids_str)) {
            $this->redirect("instance", "get_instances", (string)$form_id, "no_selection_for_del_confirm");
            return;
        }
        $valid_instances_to_confirm = [];
        foreach ($selected_ids_str as $id_str) {
            if (ctype_digit($id_str)) {
                $inst = Instance::get_instance((int)$id_str);
                if ($inst && $inst->get_form() === $form_id) {
                    $valid_instances_to_confirm[] = $inst;
                }
            }
        }
        if(empty($valid_instances_to_confirm)) {
            $this->redirect("instance", "get_instances", (string)$form_id, "no_valid_selection_del_confirm");
            return;
        }
        (new View("delete_instance_confirmation"))->show([
            "selected" => $valid_instances_to_confirm, // Pour affichage
            "form" => $form, // L'objet Form
            "search_form" => $search_form
        ]);

    }

    private function retrieve_form_by_id_or_redirect(int $form_id, $user): Form | null {
        $forms = $user->get_forms($form_id, $user->get_id());
        $form = null;
        foreach ($forms as $current_form) {
            if($current_form->get_id() === $form_id) {
                $form = $current_form;
            }
        }
        if (!$form) {
            $this->redirect('form');
        }
        return $form;
    }

    private function get_form_or_redirect(): int|null {
        $form_id = (int) ($_POST['form_id'] ?? $_GET['param1'] ?? 0) ?: null;
        if (!$form_id || !is_numeric($form_id)) {
            $this->redirect('main', 'error', "form_id_missing_for_delete");
            exit;
        }
        return (int)$form_id;
    }

    private function get_instance_or_redirect(): Instance|null {
        $instance_id = isset($_GET['param1']) ? (int) $_GET['param1'] : null;
        $instance = Instance::get_instance($instance_id);
        if (!$instance) {
            $this->redirect('main', 'error', "instances_not_found");
        }

        return $instance;
    }

    private function get_question_by_idx(Form $form): Question|null {
        $question_idx = isset($_GET['param2']) ? (int) $_GET['param2'] : 1;
        $question = $form->get_question_by_idx($question_idx);

        if (!$question) {
            $this->redirect('form', 'list');
        }
        return $question;
    }

    private function saveAnswer(?Answer $answer, ?Instance $instance, Question $question): ?Answer {
        if ($answer) {
            $answer->set_value($_POST['answer']);
            $answer->update();
        } else {
            $answer = new Answer($instance->get_id(), $question->get_id(), $_POST['answer']);
            $answer->persist();
        }
        return $answer;
    }

    private function redirect_if_instance_not_valid(Instance $instance, Form $form, string $search_form): void {
        $answers = Answer::get_answers($instance->get_id());
        $questions = $form->get_questions();
        foreach ($answers as $answer) {
            $question = QuestionUtils::find_by_id($questions, $answer->get_question());

            if(!empty($answer->validate($question))) {
                $this->redirect('instance', 'correct_errors_box', $instance->get_id(), $question->get_idx(), $search_form);
            }
        }
    }

    public function analyze(): void {
        $user= $this->get_user_or_false();
        if(!$user || $user->get_role() === "guest") {
            $this->redirect('main', 'error','does_not_exist');
        }
        $form_id_str = $_POST['form_id'] ?? $_GET['param1'] ?? null;
        if (!$form_id_str || !ctype_digit($form_id_str)) {
            $this->redirect("main", "error", "invalid_form_id_for_analyze");
            return;
        }
        $form_id = (int)$form_id_str;
        $form = Form::get_form($form_id);
        if (!$form || !$form->is_editor($user->get_id())) { // Seul owner/editor
            $this->redirect("main", "error", "unauthorized_form_analyze");
            return;
        }
//        if (count(Instance::get_instances($form_id) )== 0) {
//            // Afficher un message dans la vue ou rediriger
//            // Pour l'instant, on continue, la vue gérera l'affichage "pas de données"
//        }

        $question_id = $_POST['question_id'] ?? null;
        $search_form = $_GET['param2'] ?? '';

        $questions = Question::get_questions($form_id);
        $statistics = []; // Init
        $total_responses = 0; // Init
        $selected_question_obj = null; // Init
        $data = ['error' => 'No data available'];
        $question_id_post_str = $_POST['question_id'] ?? null;
        if ($question_id_post_str && ctype_digit($question_id_post_str)) {
            $question_id = (int)$question_id_post_str;
            $temp_selected_question = Question::get_question($question_id);
            // Valider que la question sélectionnée appartient bien au formulaire actuel
            if ($temp_selected_question && $temp_selected_question->get_form() === $form_id) {
                $selected_question_obj = $temp_selected_question;
                $statistics = Answer::get_statistics_for_question($question_id); // Devrait être $selected_question_obj->get_id()
                $total_responses = Answer::get_total_responses_for_question($selected_question_obj->get_id());
            }
        }

        if ($question_id) {
            $selected_question = Question::get_question($question_id);
            $statistics = Answer::get_statistics_for_question($question_id);
            $total = Answer::get_total_responses_for_question($question_id);

            // Préparer les données pour JSON
            $data = [
                'labels' => [],
                'values' => [],
                'ratios' => [],
                'rows' => [],
                'total' => $total
            ];

            foreach ($statistics as $stat) {
                $data['labels'][] = $stat['value'];
                $data['values'][] = $stat['count'];
                $data['ratios'][] = $stat['ratio'];
                $data['rows'][] = $stat;
            }

            if ($this->is_ajax_request()) {
                header('Content-Type: application/json');
                echo json_encode($data);
                exit;
            }
        }

        // Rendu normal pour les requêtes non-AJAX
        (new View("analyze"))->show([
            'form' => $form,
            'questions' => $questions,
            'statistics' => $statistics ?? [],
            'total_responses' => $total ?? 0,
            'selected_question' => $selected_question ?? null,
            'errors' => [],
            'search_form' => $search_form
        ]);
    }

    private function is_ajax_request(): bool {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}