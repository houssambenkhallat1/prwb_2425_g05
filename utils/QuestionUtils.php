<?php
class QuestionUtils {
    public static function is_valid_date_format(string $date): bool {
        $dateTime = DateTime::createFromFormat('d/m/Y', $date);
        return $dateTime && $dateTime->format('d/m/Y') === $date;
    }

    public static function is_valid_email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function find_by_id(array $questions, int $id): ?Question {
        foreach ($questions as $question) {
            if ($question->get_id() === $id) {
                return $question;
            }
        }
        return null;
    }
}