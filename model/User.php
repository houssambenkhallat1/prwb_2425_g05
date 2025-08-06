<?php

require_once "framework/Model.php";
require_once 'model/Form.php';
require_once 'utils/QuestionUtils.php';

class User extends Model {
    private ?int $id;
    private string $full_name;
    private string $password;
    private string $email;
    private string $role;

    public function __construct(?int $id, string $full_name, string $password, string $email, string $role) {
        $this->id = $id;
        $this->full_name = $full_name;
        $this->password = $password;
        $this->email = $email;
        $this->role = $role;
    }

    public function get_full_name(): string {
        return $this->full_name;
    }

    public function get_password(): string {
        return $this->password;
    }

    public function get_email(): string {
        return $this->email;
    }

    public function get_role(): string {
        return $this->role;
    }
    public function is_guest(): bool
    {
        return $this->get_role() === "guest";

    }
    public function set_password(string $password):void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    private static function check_password(string $clear_password, string $hash) : bool {
        return password_verify($clear_password, $hash);
    }

    public function persist() : User {
        if(self::get_member_by_email($this->email))
            self::execute(
                "UPDATE users 
                        SET password=:password, role=:role, full_name=:full_name 
                        WHERE email=:email ",
                ["full_name"=>$this->full_name,
                    "password"=>$this->password,
                    "email"=>$this->email,
                    "role"=>$this->role]);
        else
            self::execute(
                "INSERT INTO users(full_name,email,password,role)
                        VALUES(:full_name,:email,:password,:role)",
                ["full_name"=>$this->full_name,
                    "email"=>$this->email,
                    "password"=>$this->password,
                    "role"=>$this->role]);
        return $this;
    }

    public function get_forms() : array|null {
        $id = $this->get_id();
        return Form::get_forms($id);
    }

    public function get_form(int $form_id): Form|null {
        $forms = $this->get_forms();
        foreach ($forms as $form) {
            if($form->get_id() == $form_id) {
                return $form;
            }
        }

        return null;
    }

    public function get_instances(): array|null {
        return $this->id ? Instance::get_instances($this->id) : null;
    }

    public function validate_unicity() : array {
        $errors = [];
        $query = self::execute(
            "SELECT * FROM users 
                    where full_name = :full_name",
            ["full_name"=>$this->full_name]);
        $data = $query->fetch();
        if ($data) {
            $errors[] = "This full name already exists.";
        }
        if(!QuestionUtils::is_valid_email($this->get_email())){
            $errors[] = "Invalid email.";
        }
        $member = self::get_member_by_email($this->get_email());
        if ($member) {
            $errors[] = "This email already exists.";
        }

        return $errors;
    }

    private static function validate_password(string $password) : array {
        $errors = [];
        $user_password_min_length = Configuration::get("user_password_min_length");
        $user_password_max_length = Configuration::get("user_password_max_length");
        if (strlen($password) < $user_password_min_length || strlen($password) > $user_password_max_length) {
            $errors[] = "Password length must be between 8 and 16.";
        } if (!((preg_match("/[A-Z]/", $password)) &&
            preg_match("/\d/", $password) &&
            preg_match("/['\";:,.\/?!\\-]/", $password)))

        {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }

    public static function validate_passwords(string $password, string $password_confirm) : array {
        $errors = User::validate_password($password);
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }

    public static function get_member_by_email(string $email) : User|false {
        $query = self::execute(
            "SELECT * FROM users where email = :email",
                    ["email"=>$email]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["id"], $data["full_name"], $data["password"], $data["email"], $data["role"]);
        }
    }

    public static function validate_login(string $email, string $password) : array {
        $errors = [];
        $user = User::get_member_by_email($email);
        if ($user) {
            if (!self::check_password($password, $user->password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a member with the email '$email'. Please sign up.";
        }
        return $errors;
    }


    public function get_id(): int{
        $email = $this->email;
        $query = self::execute(
            "SELECT id FROM users where email = :email",
                    ["email"=>$email]);
        $data = $query->fetch();
        return $data["id"];
    }

    public static function get_user_by_id(int $id) : User|false
    {
        $query = self::execute(
            "SELECT * FROM users where id = :id",
                    ["id"=>$id]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User( $data["id"],$data["full_name"], $data["password"], $data["email"], $data["role"]);
        }
    }

    public function update( string $full_name,  string $email):array {

        $errors = [];
        $user_fullname_min_length = Configuration::get("user_fullname_min_length");
        $user_fullname_max_length = Configuration::get("user_fullname_max_length");
        $user_email_min_length = Configuration::get("user_email_min_length");
        $user_email_max_length = Configuration::get("user_email_max_length");
        if(strlen($full_name) < $user_fullname_min_length || strlen($full_name) > $user_fullname_max_length) {
            $errors[] = "Full name must be between 3 and 20 characters.";
        }

        if(strlen($email) < $user_email_min_length || strlen($email) > $user_email_max_length) {
            $errors[] = " Email must be between 3 and 30 characters.";
        }

        if ($this->get_full_name() !== $full_name){
            $unicity_error = self::validate_full_name($full_name);
            if (!empty($unicity_error)) {
                $errors = array_merge($errors, $unicity_error);
            }
        }
        if($this->get_email() !== $email){
            if(!QuestionUtils::is_valid_email($email)){
                $errors[] = "Invalid email.";
            }
            $member = self::get_member_by_email($email);
            if ($member) {
                $errors[] = "This email already exists.";
            }

        }

        if(empty($errors)) {
                    self::execute("UPDATE users SET full_name=:full_name,  email=:email WHERE id=:id ",
                        ["full_name"=>$full_name, "email"=>$email,  "id"=>self::get_id() ]);
        }
        return $errors;
    }

    public function set_full_name(string $full_name): void
    {
        $this->full_name = $full_name;
    }

    public function set_email(string $email): void
    {
        $this->email = $email;
    }

    public function set_role(string $role): void
    {
        $this->role = $role;
    }
    public function validate_full_name($fname) : array {
        $errors = [];
        $query = self::execute(
            "SELECT * FROM users 
                    where full_name = :full_name",
            ["full_name"=>$fname]);
        $data = $query->fetch();
        if ($data) {
            $errors[] = "This full name already exists.";
        }

        return $errors;
    }

}
