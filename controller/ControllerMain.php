<?php
require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
class ControllerMain extends Controller {
    public function index() : void {
        if ($this->user_logged()) {
            $this->redirect("form");
        } else {
            (new View("login"))->show();
        }
    }
    public function login() : void {
        $user = $this->get_user_or_false();
        if ($user){
            $this->redirect("form");
        }
        $email= '';
        $password = '';
        $errors = [];
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $errors = User::validate_login($email, $password);
            if (empty($errors)) {
                $this->log_user(User::get_member_by_email($email), "form");
            }
        }
        (new View("login"))->show(["pseudo" => $email, "password" => $password, "errors" => $errors]);
    }

    public function signup() : void
    {

        $email = '';
        $fullName='';
        $errors = [];

        if($this->get_user_or_false() ){
            if($this->get_user_or_false()->get_role() == "guest" ) {
                $_SESSION = array();
                session_destroy();
            }else{
                $this->redirect("form");
            }
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST['email']) && isset($_POST['fullName']) && isset($_POST['password']) && isset($_POST['passwordConfirmed'])) {
                $email = Tools::sanitize($_POST['email']);
                $fullName = Tools::sanitize($_POST['fullName']);
                $password = $_POST['password'];
                $passwordConfirmed = $_POST['passwordConfirmed'];
                $user = new User(null, $fullName, password_hash($password, PASSWORD_BCRYPT), $email, "user");

                $errors = $user->validate_unicity();
                $errors = array_merge($errors, $user::validate_passwords($password, $passwordConfirmed));

                if (empty($errors)) {
                    $user->persist();
                    $this->log_user($user, "Form");
                }
            }
        }
        (new View("signup"))->show(["errors" => $errors, "fullName" => $fullName, "email" => $email]);
    }

    public function settings() : void {
        $user=  $this->get_user_or_redirect();
        if(!$user || $user->is_guest()){
            $this->redirect('main', 'error','does_not_exist');
        }
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->redirect("main",'error','does_not_exist');
        }
        if(isset($_GET['param1']) ) {
            $this->redirect("main",'error','does_not_exist');
        }

        (new View("settings"))->show(["user" => $user]);
    }


    public function edit_profile() : void {

        $user=  $this->get_user_or_redirect();
        if(!$user || $user->is_guest()){
            $this->redirect('main', 'error','somthing_went_wrong');
        }
        $msg='';
        $errors = [];
        if(isset($_GET['param1'])){
            $msg = $_GET['param1'];
            if($msg!=='profile_updated'){
                $this->redirect('main', 'error','somthing_went_wrong');
                exit();
            }

        }

        if (isset($_POST['fname']) && isset($_POST['email'])) {
            $email = Tools::sanitize($_POST['email']);
            $fname = Tools::sanitize($_POST['fname']);
            $errors =$user->update($fname, $email);

            if(empty($errors)){
                $updated_user = User::get_member_by_email($email);
                if ($updated_user) {
                    $_SESSION['user'] = $updated_user;
                    $this->redirect('main','edit_profile', 'profile_updated');
                }else {
                    $errors[] = 'Error updating profile.'; // Ajouter cette gestion d'erreur
                }
            }
        }

        (new View("edit_profile"))->show(["user" => $user, "msg" => $msg, "errors" => $errors ]);
    }

    public function change_password() : void {
        $errors =[];
        $msg="";
        $user=  $this->get_user_or_false();
        if(!$user || $user->is_guest()){
            $this->redirect('main', 'error','guest_cannot_change_password');
        }
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST['password']) && isset($_POST['nouveau_password']) && isset($_POST['Confirme_nouveau_password'])) {
                $password = Tools::sanitize($_POST['password']);
                $new_password = Tools::sanitize($_POST['nouveau_password']);
                $password_confirmed = Tools::sanitize($_POST['Confirme_nouveau_password']);

                $errors = User::validate_login($user->get_email(), $password);
                $errors = array_merge($errors, $user::validate_passwords($new_password, $password_confirmed));

                if(empty($errors)){
                    $user->set_password($new_password);
                    $user->persist();
                    $_SESSION['user'] = User::get_user_by_id($user->get_id());
                    $msg='password changed !';
                }

            }
        }else{
            if(isset($_GET['param1'])){
                $this->redirect('main', 'error','guest_cannot_change_password');
                exit();
            }
        }


        (new View("change_password"))->show(["errors" => $errors, "msg" => $msg]);
    }

    public function guest() : void {
        $this->log_user(User::get_member_by_email("guest@epfc.eu"), "form");
    }
    public function loginAsBepenelle() : void {
        $this->log_user(User::get_member_by_email("bepenelle@epfc.eu"), "form");
    }
    public function loginAsmamichel() : void {
        $this->log_user(User::get_member_by_email("mamichel@epfc.eu"), "form");
    }
    public function loginAsboverhaegen() : void {
        $this->log_user(User::get_member_by_email("boverhaegen@epfc.eu"), "form");
    }
    public function loginAsxapigeolet() : void {
        $this->log_user(User::get_member_by_email("xapigeolet@epfc.eu"), "form");
    }
    public function logoff() : void {
        $this->logout();
    }

    public function error() : void {
        $error_message_from_url = $_GET["param1"] ?? "An unknown error occurred.";
        // Tu pourrais aussi passer un code d'erreur si l'URL le contient
         $error_code = $_GET["param2"] ?? null;
        (new View("error"))->show([
            "error" => $error_message_from_url
            // , "error_code" => $error_code // Optionnel
        ]);
    }
}