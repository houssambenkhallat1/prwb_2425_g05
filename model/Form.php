<?php
require_once 'framework/Model.php';
require_once 'model/Instance.php';
require_once 'model/Question.php';
require_once 'model/FormColor.php';
require_once 'model/User.php';

class Form extends Model {

    private ?int $id;
    private string $title;
    private ?string $description;
    private int $owner_id;
    private bool $public;

    public function __construct(?int $id, string $title, ?string $description, int $owner, bool $public = false) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->owner_id = $owner;
        $this->public = $public;
    }

    public function get_questions(): array {
        return $this->id ? Question::get_questions($this->id) : [];
    }

    public function get_question_by_idx(int $idx): Question|null {
        return Question::get_question_by_idx($this->id, $idx);
    }

    public function persist() : Form|null {
            self::execute('INSERT INTO forms (id, title, description, owner, is_public) VALUES (:id, :title, :description, :owner, :is_public)',
            [
                'id' => $this->id,
                 'title' => $this->title,
                 'description' => $this->description,
                 'owner' => $this->owner_id,
                 'is_public' => $this->public ? 1 : 0
            ]
        );
            $this->id = self::lastInsertId();
        return $this;
    }
    public function update() : Form|null {
        $errors = $this->validate();

        if ($errors === []) {
            try {
                self::execute('UPDATE forms SET title = :title, description = :description, is_public = :is_public WHERE id = :id and owner = :owner_id',
                    ['id' => $this->id, 'title' => $this->title, 'description' => $this->description, 'is_public' => $this->public ? 1 : 0, 'owner_id' => $this->owner_id]);

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $this;
    }
//    public function update() : Form|null {
//        $errors = $this->validate();
//        if ($errors === []) {
//            self::execute('UPDATE forms SET title = :title, description = :description, is_public = :is_public
//                            WHERE id = :id',
//                ['id' => $this->id, 'title' => $this->title, 'description' => $this->description, 'is_public' => $this->public ? 1 : 0, 'owner_id' => $this->owner_id]);
//        }
//        return $this;
//    }

    public static function get_form(int $formId) :?Form {
        $query = self::execute("SELECT * FROM forms WHERE id = :id ", ["id"=>$formId]);
        $data = $query->fetch();
        if (!$data) {
            return null;
        }

        return new Form($data["id"], $data["title"], $data["description"], $data["owner"], (bool)$data["is_public"]);
    }

    public static function get_public_forms() : array | null {

        $query = self::execute("SELECT * FROM forms WHERE is_public = :is_public", ["is_public"=>1]);
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        return array_map(fn($form) => new Form(
            $form['id'],
            $form['title'],
            $form['description'],
            $form['owner'],
            (bool)$form['is_public']
        ), $data);
    }

    public function validate() : array {
        $error = [];
        $form_title_min_length = Configuration::get("form_title_min_length");
        $form_title_max_length = Configuration::get("form_title_max_length");
        $form_description_min_length = Configuration::get("form_description_min_length");
        $form_description_max_length = Configuration::get("form_description_max_length");

        if (strlen($this->title) < $form_title_min_length || strlen($this->title) > $form_title_max_length) {
            $error['title'] = "Title must be between 3 and 60 characters";
        }
        if (strlen($this->description)!==0 && (strlen($this->description) < $form_description_min_length || strlen($this->description) > $form_description_max_length)) {
            $error['description'] = "Description must be between 3 and 255 characters";
        }

        // Check if a form with the same title already exists
        $query = self::execute(
            "SELECT COUNT(*) as count FROM forms WHERE title = :title AND owner = :owner_id AND (:id IS NULL OR id != :id)",
            [
                "title" => $this->title,
                "owner_id" => $this->owner_id,
                "id" => $this->id
            ]
        );
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            $error['title'] = "A form with this title already exists. Please choose a different title.";
        }

        return $error;
    }

    public function get_instances() {
        return Instance::get_instances($this->id);
    }

    public function get_instances_by_form_and_user(int $userId): array|null {
        return $userId ? Instance::get_instances_by_form_and_user($this->id, $userId) : null;
    }



    // error de owner id doit etre user id , je suis guest je ne suis pas owner de this form
    public function get_last_instance($user_id): ?Instance {
        $instances = Instance::get_instances_by_form_and_user($this->id, $user_id);
        if (!empty($instances)) {
            $last_position = count($instances) - 1;
            return $instances[$last_position];
        }
        return null;
    }

    public function get_last_instance_status($user_id) : string {
        $process = "";
        $instance = $this->get_last_instance($user_id);
        if ($instance !== null) {
            $process = ($instance->get_completed() !== null) ? "Completed" : "In progress...";
        }
        return $process;
    }

    public function has_question(): bool
    {
        $question = Question::get_questions($this->id);
        return !empty($question);
    }
    public function is_editor(int $user): bool
    {
        // 1. Le propriétaire du formulaire peut toujours éditer.
        if ($this->owner_id === $user) {
            return true;
        }
        return false;
    }

//    public function get_started_at($user_id) : string {
//        $instance = $this->get_last_instance($user_id);
//
//        return $startedTime = $instance->start($instance->get_started());
//    }

    public function get_id(){
        return $this->id;
    }

    public function get_title(): string {
        return $this->title;
    }

    public function set_title(string $title): void {
        $this->title = $title;
    }

    public function get_description(): ?string {
        return $this->description;
    }

    public function set_description(string $description): void {
        $this->description = $description;
    }

    public function get_owner_id(): int {
        return $this->owner_id;
    }

    public function set_owner_id($owner_id): void {
        $this->owner_id = $owner_id;
    }

    public function is_public(): bool {
        return $this->public;
    }

    public function set_public(bool $public): void {
        $this->public = $public;
    }

    public function get_count_questions(): int {
        $questions = $this->get_questions();
        return $questions ? count($this->get_questions()) : 0;
    }

    public function delete(): bool {
        try {

            // Supprimer toutes les réponses liées aux instances de ce formulaire
            self::execute(
                "DELETE FROM answers
             WHERE instance IN (
                SELECT id FROM instances WHERE form = :form_id
             )",
                ["form_id" => $this->id]
            );

            // Supprimer toutes les instances du formulaire
            self::execute(
                "DELETE FROM instances WHERE form = :form_id",
                ["form_id" => $this->id]
            );

            // Supprimer toutes les questions du formulaire
            self::execute(
                "DELETE FROM questions WHERE form = :form_id",
                ["form_id" => $this->id]
            );

            // Enfin, supprimer le formulaire lui-même
            $result = self::execute(
                "DELETE FROM forms WHERE id = :form_id",
                ["form_id" => $this->id]
            );
            return $result->rowCount() > 0;

        } catch (PDOException $e) {
            // En cas d'erreur, annuler la transaction
            self::execute("ROLLBACK");
            error_log("Error in Form::delete(): " . $e->getMessage());
            return false;
        }
    }

    public function get_owner_name(): string {
        $user= User::get_user_by_id($this->owner_id);
        return $user->get_full_name();

    }

    public static function get_forms(int $user_id): array {
        $query = self::execute(
            "SELECT DISTINCT f.* 
         FROM forms f
         LEFT JOIN user_form_accesses ufa ON f.id = ufa.form
         WHERE f.owner = :user_id 
            OR f.is_public = 1 
            OR ufa.user = :user_id
         ORDER BY f.title ASC",
            ["user_id" => $user_id]
        );
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($form) => new Form(
            $form['id'],
            $form['title'],
            $form['description'],
            $form['owner'],
            (bool)$form['is_public']
        ), $data);
    }

    public static function search_forms(int $user_id, string $searchTerm): array {
        $query = self::execute(
            "SELECT DISTINCT f.* 
         FROM forms f
         JOIN users u ON f.owner = u.id
         LEFT JOIN questions q ON f.id = q.form
         LEFT JOIN user_form_accesses ufa ON f.id = ufa.form
         WHERE (
            f.title LIKE :term OR
            f.description LIKE :term OR
            u.full_name LIKE :term OR
            q.title LIKE :term OR
            q.description LIKE :term
         ) AND (
            f.owner = :user_id OR
            f.is_public = 1 OR
            ufa.user = :user_id
         )
         ORDER BY f.title",
            ['term' => $searchTerm, 'user_id' => $user_id]
        );

        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($form) => new Form(
            $form['id'],
            $form['title'],
            $form['description'],
            $form['owner'],
            (bool)$form['is_public']
        ), $data);
    }

//    public static function get_forms_by_key_search(User $user, string $search_query): array {
//        $form_ids = [];
//        if (!empty($search_query)) {
//            $forms = $user->get_forms();
//
//            foreach ($forms as $form) {
//                $questions = $form->get_questions();
//
//                // Build searchable text
//                $search_text = $form->get_title().' '.$form->get_description().' '.$user->get_full_name().' ';
//
//                foreach ($questions as $question) {
//                    $search_text .= $question->get_title().' '.$question->get_description() . ' ';
//                }
//
//                // Perform fulltext search
//                if (self::matchSearchQuery($search_query, $search_text)) {
//                    $form_ids[] = $form->get_id();
//                }
//            }
//        }
//
//        return $form_ids;
//    }

    private static function matchSearchQuery(string $query, string $text): bool
    {
        // Normalize text and query
        $normalizedText = mb_strtolower(preg_replace('/\s+/', ' ', $text));
        $normalizedQuery = mb_strtolower(preg_replace('/\s+/', ' ', trim($query)));

        // Handle empty query case
        if ($normalizedQuery === '') {
            return false;
        }

        // Split into words considering multiple spaces
        $terms = preg_split('/\s+/', $normalizedQuery, -1, PREG_SPLIT_NO_EMPTY);

        // Check if all terms exist in the text
        foreach ($terms as $term) {
            if ($term === '') continue;
            if (mb_strpos($normalizedText, $term) === false) {
                return false;
            }
        }
        return true;
    }
    public static function get_forms_by_search_and_colors(User $user, string $search_query = '', array $colors = []): array {
        $form_ids = [];
        $searchTerm = !empty($search_query) ? "%$search_query%" : '';
        $forms = self::search_forms_with_colors($user->get_id(), $searchTerm, $colors);

        foreach ($forms as $form) {
            $form_ids[] = $form->get_id();
        }

        return $form_ids;
    }


    public static function get_forms_by_key_search(User $user, string $search_query): array {
        $form_ids = [];
        if (!empty($search_query)) {
            $forms = self::get_forms($user->get_id()); // Utilisez get_forms au lieu de $user->get_forms()

            foreach ($forms as $form) {
                $questions = $form->get_questions();

                // Build searchable text
                $search_text = $form->get_title().' '.$form->get_description().' '.$form->get_owner_name().' ';

                foreach ($questions as $question) {
                    $search_text .= $question->get_title().' '.$question->get_description() . ' ';
                }

                // Perform fulltext search
                if (self::matchSearchQuery($search_query, $search_text)) {
                    $form_ids[] = $form->get_id();
                }
            }
        }

        return $form_ids;
    }



    public function get_colors(): array {
        if (!$this->id) return [];

        $query = self::execute(
            "SELECT color FROM form_colors WHERE form = :form_id ORDER BY color",
            ["form_id" => $this->id]
        );
        $data = $query->fetchAll(PDO::FETCH_COLUMN);

        return array_map(fn($color) => FormColor::from($color), $data);
    }
    /**
     * Recherche de formulaires avec filtres de couleur et texte
     */
    public static function search_forms_with_colors(int $user_id, string $searchTerm = '', array $colors = []): array {
        $params = ['user_id' => $user_id];

        // Construction de la requête de base
        $sql = "SELECT DISTINCT f.* 
            FROM forms f
            JOIN users u ON f.owner = u.id
            LEFT JOIN questions q ON f.id = q.form
            LEFT JOIN user_form_accesses ufa ON f.id = ufa.form";

        // Ajout du filtre couleur si nécessaire
        if (!empty($colors)) {
            $sql .= " LEFT JOIN form_colors fc ON f.id = fc.form";
        }

        $sql .= " WHERE (f.owner = :user_id OR f.is_public = 1 OR ufa.user = :user_id)";

        // Filtre par couleur
        if (!empty($colors)) {
            $colorPlaceholders = [];
            foreach ($colors as $i => $color) {
                $colorKey = "color_$i";
                $colorPlaceholders[] = ":$colorKey";
                $params[$colorKey] = $color;
            }
            $sql .= " AND fc.color IN (" . implode(',', $colorPlaceholders) . ")";
        }

        // Filtre textuel
        if (!empty($searchTerm)) {
            $sql .= " AND (f.title LIKE :term OR f.description LIKE :term OR u.full_name LIKE :term OR q.title LIKE :term OR q.description LIKE :term)";
            $params['term'] = $searchTerm;
        }

        $sql .= " ORDER BY f.title";

        $query = self::execute($sql, $params);
        $data = $query->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($form) => new Form(
            $form['id'],
            $form['title'],
            $form['description'],
            $form['owner'],
            (bool)$form['is_public']
        ), $data);
    }

    /**
     * Texte recherchable pour un formulaire
     */
    private function get_searchable_text(User $user): string {
        $questions = $this->get_questions();
        $search_text = $this->get_title() . ' ' . $this->get_description() . ' ' . $this->get_owner_name() . ' ';

        foreach ($questions as $question) {
            $search_text .= $question->get_title() . ' ' . $question->get_description() . ' ';
        }

        return $search_text;
    }
}