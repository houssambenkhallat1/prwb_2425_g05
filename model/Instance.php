<?php
require_once 'framework/tools.php';

class Instance extends Model {
    private ?int $id;
    private int $form;
    private int $user;
    private DateTime $started;
    private ?DateTime $completed;

    public function __construct(?int $id, int $form, int $user, DateTime $started, ?DateTime $completed = null) {
        $this->id = $id;
        $this->form = $form;
        $this->user = $user;
        $this->started = $started;
        $this->completed = $completed;
    }

    public static function get_instances(int $form_id ): array{

        $query = self::execute("SELECT * FROM instances where form = :form and completed IS NOT NULL ",
            ["form" => $form_id]);
        $data = $query->fetchAll(PDO::FETCH_ASSOC);
        $instances = [];
        foreach ($data as $instance) {
            $started = $instance['started'] ? new DateTime($instance['started']) : null;
            $completed = $instance['completed'] ? new DateTime($instance['completed']) : null;
            $instances[] = new Instance($instance["id"], $instance["form"], $instance["user"], $started, $completed);
        }
        return $instances;
    }

    public function get_owner_name(): string
    {
        $user= User::get_user_by_id($this->user);

        return $user->get_full_name();
    }

    public static function get_instance(int $id) : ?Instance {
        $query = self::execute("select * from instances where id = :id", ["id" => $id]);
        $data = $query->fetch();
        if(!$data) {
            return null;
        }
        $started = $data['started'] ? new DateTime($data['started']) : null;
        $completed = $data['completed'] ? new DateTime($data['completed']) : null;

        return new Instance($data["id"], $data["form"], $data["user"], $started, $completed);
    }

    public static function get_instances_by_form_and_user(int $formId, int $userId) : array {
        $query = self::execute("SELECT * FROM Instances WHERE form = :form AND user = :user", ["form" => $formId, "user" => $userId]);
        $data = $query->fetchAll();
        $instances = [];
        foreach ($data as $instance) {
            $started = $instance["started"] ? new DateTime($instance["started"]) : null;
            $completed = $instance["completed"] ? new DateTime($instance["completed"]) : null;
            $instances[] = new Instance($instance["id"], $instance["form"], $instance["user"], $started, $completed);
        }
        return $instances;
    }

    public static function get_instances_by_form_id(int $formId) : array {
        $query = self::execute("SELECT * FROM Instances WHERE form = :form and completed IS NOT NULL" , ["form" => $formId]);
        $data = $query->fetchAll();
        $instances = [];
        foreach ($data as $instance) {
//            $started = $instance["started"] ? new DateTime($instance["started"]) : null;
//            $completed = $instance["completed"] ? new DateTime($instance["completed"]) : null;
            $instances[] = new Instance($instance["id"], $instance["form"], $instance["user"], new DateTime($instance["started"]), new DateTime($instance["completed"]));
        }
        return $instances;
    }

    public function get_user_role(): string {
        $user= User::get_user_by_id($this->user);

        return $user->get_role();
    }

    public function persist() : Instance|null {
        self::execute('INSERT INTO instances (form, user, started, completed) 
                            VALUES (:form, :user, :started, :completed)',
            [
                'form' => $this->form,
                'user' => $this->user,
                'started' => $this->started ? $this->started->format("Y-m-d H:i:s") : null,
                'completed' => $this->completed ? $this->completed->format("Y-m-d H:i:s") : null
            ]
        );
        return $this;
    }

    public function update(): Instance|null {
        self::execute('UPDATE instances SET completed =:completed WHERE id = :id', ["id" => $this->id, "completed" => $this->completed->format("Y-m-d H:i:s")]);
        return $this;
    }

    public function update_owner(): Instance|null {
        self::execute('UPDATE instances SET user =:user_id WHERE id = :instance_id',
            ["instance_id" => $this->id, "user_id" => $this->user]
        );
        return $this;
    }

    public function delete() : void {
        self::execute('DELETE FROM instances WHERE id = :id', ["id" => $this->id]);
    }

    public function start($start): string
    {
        return Tools::start($start);
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

    public function set_form(int $form): void  {
        $this->form = $form;
    }

    public function get_user(): int {
        return $this->user;
    }

    public function set_user(int $user): void {
        $this->user = $user;
    }

    public function get_started(): DateTime {
        return $this->started;
    }

    public function get_completed(): ?DateTime {
        return $this->completed;
    }

    public function is_completed(): bool {
        return $this->completed != null;
    }

    public function setCompleted(?DateTime $completed): void {
        $this->completed = $completed;
    }
}