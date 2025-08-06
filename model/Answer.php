<?php
require_once 'framework/Model.php';
require_once 'utils/QuestionUtils.php';

class Answer extends Model {
    private int $instance;
    private int $question;
    private string $value;

    public function __construct(int $instance, int $question, string $value) {
        $this->instance = $instance;
        $this->question = $question;
        $this->value = $value;
    }

    public static function get_answer(int $instance, int $question): Answer|null {
        $query = self::execute("SELECT * FROM Answers WHERE instance = :instance_id AND question = :question_id",
            ["instance_id" => $instance, "question_id" => $question]);
        $data = $query->fetch(PDO::FETCH_ASSOC);

        return $data ? new Answer($data["instance"], $data["question"], $data["value"]) : null;
    }

    public static function get_answers(int $instance): array {
        $query = self::execute("SELECT * FROM Answers WHERE instance = :instance_id",
            ["instance_id" => $instance]);
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        $answers = [];
        foreach ($data as $answer) {
            $answers[] = new Answer($answer["instance"], $answer["question"], $answer["value"]);
        }
        return $answers;
    }
    public static function get_statistics_for_question(int $question_id): array {
        $max_ratio_precision = Configuration::get("max_ratio_precision");
        try {
            $query = "SELECT 
                        a.value,
                        COUNT(*) as count,
                        ROUND((COUNT(*) * 100.0 / (
                            SELECT COUNT(*) 
                            FROM answers 
                            WHERE question = :qid1
                        )), $max_ratio_precision) as ratio
                    FROM answers a
                    WHERE a.question = :qid2
                    GROUP BY a.value
                    ORDER BY count DESC, value ASC";

            $statement = self::execute($query, [
                'qid1' => $question_id,
                'qid2' => $question_id
            ]);

            return $statement->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error in getStatisticsForQuestion: " . $e->getMessage());
            return [];
        }
    }

    public static function get_total_responses_for_question(int $question_id): int {
        try {
            $query = "SELECT COUNT(*) as total 
                     FROM answers 
                     WHERE question = :question_id";

            $statement = self::execute($query, ['question_id' => $question_id]);
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            return (int)$result['total'];

        } catch (PDOException $e) {
            error_log("Error in getTotalResponsesForQuestion: " . $e->getMessage());
            return 0;
        }
    }

    public function update() : Answer|null {
        self::execute('UPDATE Answers SET value = :value WHERE instance = :instance_id and question = :question_id',
            [
                'value' => $this->value != "" ? $this->value : null,
                'instance_id' => $this->instance,
                'question_id' => $this->question
            ]
        );
        return $this;
    }
    public function validate(Question $question) : array {
        $errors = [];
        if ($question->is_required() && $this->value == "") {
            $errors['required'] = "Field is required.";
        }
        if ($question->get_type() === QuestionType::DATE && !QuestionUtils::is_valid_date_format($this->get_value())) {
            if($question->is_required() || $this->get_value()!==""){
                $errors['date'] = "Field date must be in format dd/mm/yyyy.";
            }

        }
        if ($question->get_type() === QuestionType::EMAIL && !QuestionUtils::is_valid_email($this->get_value())) {
            $errors['email'] = "Field must contain an email.";
        }

        return $errors;
    }

    public function persist(): Answer|null {
        self::execute("INSERT INTO Answers(instance, question, value) values(:instance_id, :question_id, :value)",
            [
                "instance_id" => $this->instance,
                "question_id" => $this->question,
                "value" => $this->value
            ]
        );
        return $this;
    }

    public function delete() : bool {
        self::execute("DELETE FROM Answers WHERE instance_id = :instance_id and :question_id",
            ["instance_id" => $this->instance, "question_id" => $this->question]);
        return true;
    }

    public function get_instance(): int {
        return $this->instance;
    }

    public function get_question(): int {
        return $this->question;
    }

    public function get_value(): string {
        return $this->value;
    }

    public function set_value(string $value): void {
        $this->value = $value;
    }
}