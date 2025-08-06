<?php
require_once 'framework/Model.php';
require_once 'model/Form.php';
require_once 'model/QuestionType.php';

class Question extends Model {
    private ?int $id;
    private int $form;
    private int $idx;
    private string $title;
    private ?string $description;
    private QuestionType $type;
    private bool $required;


    public function __construct(?int $id, int $formId, int $idx, string $title, ?string $description, QuestionType $type, $required) {
        $this->id = $id;
        $this->form = $formId;
        $this->idx = $idx;
        $this->title = $title;
        $this->description = $description;
        $this->type = $type;
        $this->required = $required == 1;
    }
    public function get_last_question_idx(): int
{
    $query = self::execute("SELECT idx FROM questions WHERE form = :form ORDER BY idx DESC LIMIT 1 ", ["form" => $this->form]);
    $data = $query->fetchAll();
    return (int) $data[0]["idx"];
}

    public static function get_questions(int $form_id): array {
        $query = self::execute("SELECT * from questions where form = :form_id order by idx", ["form_id" => $form_id]);
        $data = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$data) {
            return [];
        }
        $questions = [];
        foreach ($data as $question) {
            $questions[] = new Question($question['id'], $question['form'], $question['idx'], $question['title'], $question['description'], QuestionType::from($question['type']), $question['required']);
        }
        return $questions;
    }

    public static function get_question_by_idx(int $form_id, int $idx): Question|null {
        $query = self::execute("SELECT * FROM Questions WHERE form = :form_id AND idx = :idx", ["form_id" => $form_id, "idx" => $idx]);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }

        return new Question($data["id"], $data["form"], $data["idx"], $data["title"], $data["description"], QuestionType::from($data["type"]), $data["required"]);
    }

    public function get_answer(int $instanceId): Answer|null {
        return $instanceId && $this->id ? Answer::get_answer($instanceId, $this->id) : null;
    }

    public static function get_question(int $id): Question|null {
        $query = self::execute("Select * from questions where id = :id", ["id" => $id]);
        $data = $query->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new Question($data['id'], $data['form'], $data['idx'], $data['title'], $data['description'], QuestionType::from($data['type']), $data['required']);
        }
        return null;
    }
//    public function save(): bool {
//        $errors = $this->validate();
//
//        if (!empty($errors)) {
//            throw new Exception("Validation failed: " . implode(", ", $errors));
//        }
//
//        try {
//            if ($this->id === 0) {
//                // Insertion
//                self::execute(
//                    "INSERT INTO questions (form, idx, title, description, type, required)
//                     VALUES (:form, :idx, :title, :description, :type, :required)",
//                    [
//                        "form" => $this->form,
//                        "idx" => $this->idx,
//                        "title" => $this->title,
//                        "description" => $this->description,
//                        "type" => $this->type->value,
//                        "required" => $this->required ? 1 : 0
//                    ]
//                );
//                $this->id = self::lastInsertId();
//            } else {
//                // Mise à jour
//                self::execute(
//                    "UPDATE questions
//                     SET idx=:idx, title = :title, description = :description,
//                         type = :type, required = :required
//                     WHERE id = :id",
//                    [
//                        "id" => $this->id,
//                        "idx" => $this->idx,
//                        "title" => $this->title,
//                        "description" => $this->description,
//                        "type" => $this->type->value,
//                        "required" => $this->required ? 1 : 0
//                    ]
//                );
//            }
//            return true;
//        } catch (PDOException $e) {
//            error_log("Error in Question::save(): " . $e->getMessage());
//            return false;
//        }
//    }
    public static function get_next_available_idx(int $form_id): int {
        // Récupérer l'index le plus élevé actuellement utilisé dans ce formulaire
        $query = self::execute(
            "SELECT MAX(idx) as max_idx FROM questions WHERE form = :form_id",
            ["form_id" => $form_id]
        );
        $result = $query->fetch(PDO::FETCH_ASSOC);

        // Si aucune question n'existe encore, commencer à 1, sinon incrémenter
        return ($result && $result['max_idx']) ? ((int)$result['max_idx'] + 1) : 1;
    }

    /**
     * Méthode améliorée pour persister une nouvelle question
     */
    public function persist(): Question|array {
        $errors = $this->validate();
        if ($errors === []){
            try {
                // Si l'index est -1 (temporaire) ou 0, déterminer le prochain index disponible
                if ($this->idx <= 0) {
                    $this->idx = self::get_next_available_idx($this->form);
                } else {
                    // Vérifier si l'index est déjà utilisé
                    $existingQuestion = self::get_question_by_form_and_idx($this->form, $this->idx);
                    if ($existingQuestion) {
                        // Décaler tous les index supérieurs ou égaux pour faire de la place
                        self::execute(
                            "UPDATE questions SET idx = idx + 1 
                         WHERE form = :form_id AND idx >= :idx 
                         ORDER BY idx DESC",
                            ["form_id" => $this->form, "idx" => $this->idx]
                        );
                    }
                }

                // Insérer la nouvelle question avec l'index correct
                self::execute(
                    "INSERT INTO questions (form, idx, title, description, type, required) 
                 VALUES (:form, :idx, :title, :description, :type, :required)",
                    [
                        'form' => $this->form,
                        'idx' => $this->idx,
                        'title' => $this->title,
                        'description' => $this->description,
                        'type' => $this->type->value,
                        'required' => $this->required ? 1 : 0
                    ]
                );

                $this->id = self::lastInsertId();
                return $this;
            } catch (PDOException $e) {
                error_log("Error in Question::persist(): " . $e->getMessage());
                $errors['general'] = "Une erreur est survenue lors de la sauvegarde de la question: " . $e->getMessage();
                return $errors;
            }
        }
        return $errors;
    }

    public function update() : Question|array {
        $errors = $this->validate();
        if ($errors === []) {
            self::execute('UPDATE questions SET idx = :idx, title = :title, description = :description, type = :type, required = :required 
                                WHERE id = :id',
                [
                 'id' => $this->id,
                 'idx' => $this->idx,
                 'title' => $this->title,
                 'description' => $this->description,
                 'type' => $this->type->value,
                 'required' => $this->required ? 1 : 0
                ]
            );
        }else{
            return $errors;
        }
        return $this;
    }

    public function update_idx(): bool {
        $query = self::execute(
            "UPDATE questions SET idx = :idx WHERE id = :id AND form = :form",
            [
                'id' => $this->id,
                'idx' => $this->idx,
                'form' => $this->form
            ]
        );
        return $query->rowCount() === 1;
    }

    public function delete(): bool {
        if ($this->id === null) {
            return false;
        }

        $pdo = self::getPDO();
        $pdo->beginTransaction();

        try {
            // Capturer l'index de la question avant suppression
            $deletedIdx = $this->idx;

            // Supprimer la question
            $stmt = $pdo->prepare("DELETE FROM questions WHERE id = :id");
            $stmt->execute(['id' => $this->id]);
            $deleted = $stmt->rowCount() > 0;

            if ($deleted) {
                // Décrémenter les indices des questions suivantes
                $stmt = $pdo->prepare(
                    "UPDATE questions 
                 SET idx = idx - 1 
                 WHERE form = :form AND idx > :idx"
                );
                $stmt->execute([
                    'form' => $this->form,
                    'idx' => $deletedIdx
                ]);
            }

            $pdo->commit();
            return $deleted;
        } catch (PDOException $e) {
            $pdo->rollBack();
            error_log("Question deletion error: " . $e->getMessage());
            return false;
        }
    }
    public static function get_question_by_form_and_idx(int $form_id, int $idx): ?Question
    {
        $query = self::execute(
            "SELECT * FROM questions WHERE form = :form_id AND idx = :idx LIMIT 1",
            ["form_id" => $form_id, "idx" => $idx]
        );
        $data = $query->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }
        $type = QuestionType::tryFrom($data["type"]) ?? QuestionType::SHORT;

        return new Question(
            (int)$data["id"],
            (int)$data["form"],
            (int)$data["idx"],
            $data["title"],
            $data["description"],
            $type,
            (bool)$data["required"]
        );
    }


    public function get_id(): ?int {
        return $this->id;
    }

    public function set_id(?int $id): void {
        $this->id = $id;
    }

    public function get_form(): int {
        return $this->form;
    }

    public function set_form(int $form): void {
        $this->form = $form;
    }

    public function get_idx(): int {
        return $this->idx;
    }

    public function set_idx(int $idx): void {
        $this->idx = $idx;
    }

    public function get_title(): string {
        return $this->title;
    }

    public function set_title(string $title): void {
        $this->title = $title;
    }

    public function get_description(): string|null {
        return $this->description;
    }

    public function set_description(string $description): void {
        $this->description = $description;
    }

    public function get_type(): QuestionType {
        return $this->type;
    }

    public function set_type(QuestionType $type): void {
        $this->type = $type;
    }

    public function is_required(): bool {
        return $this->required;
    }

    public function set_required(bool $required): void {
        $this->required = $required;
    }

    public function get_previous_question_idx(): string {
        return $previous_question_idx = $this->idx > 1 ? $this->idx -1 : "";
    }

    public function has_previous_question_idx(): bool {
        return $this->idx > 1;
    }

    public function has_next_question_idx(int $count_questions): bool {
        return $this->idx < $count_questions;
    }

    public function validate(): array {
        $errors = [];
        $question_title_min_length = Configuration::get("question_title_min_length");
        $question_title_max_length = Configuration::get("question_title_max_length");
        $question_description_max_length = Configuration::get("question_description_max_length");

        // Validation du titre
        if (empty($this->title)) {
            $errors['title'] = "Title is required";
        } elseif (strlen($this->title) < $question_title_min_length) {
            $errors['title'] = "Title must be at least 3 characters long";
        } elseif (strlen($this->title) > $question_title_max_length) {
            $errors['title'] = "Title must not exceed 255 characters";
        }elseif($this->has_title($this->title)){
            $errors['title'] = "This title already exists";
        };

        // Validation du type
        if (!$this->type) {
            $errors['type'] = "Type is required";
        }

        // Validation de la description (si présente)
        if (!empty($this->description) && strlen($this->description) > $question_description_max_length) {
            $errors['description'] = "Description must not exceed 1000 characters";
        }

        // Validation de l'index
        if ($this->idx <= 0 && $this->idx !== -1) {
            $errors['idx'] = "Index must be > 0 or == -1 (temp)";
        }

        // Validation de l'ID du formulaire
        if ($this->form <= 0) {
            $errors['form'] = "Form ID must be valid";
        }

        return $errors;
    }
    public static function is_title_unique(int $form_id, string $title, ?int $question_id = null): bool {
        $sql = "SELECT COUNT(*) FROM questions WHERE form = :form_id AND title = :title";
        $params = ['form_id' => $form_id, 'title' => $title];
        if ($question_id !== null) {
            $sql .= " AND id != :question_id";
            $params['question_id'] = $question_id;
        }
        $query = self::execute($sql, $params);
        return $query->fetchColumn() === 0;
    }

    public function has_title(string $title): bool
    {
        $query = self::execute("SELECT * from questions where title = :title ", ["title" => $title]);
        $data = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($data) {
            return true;
        }
        return false;
    }
}