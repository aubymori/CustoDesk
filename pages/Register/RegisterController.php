<?php
namespace CustoDesk\Page\Register;

use CustoDesk\Page\Common\AlertType;
use CustoDesk\Page\Common\PageWithPostController;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;
use CustoDesk\User;

class RegisterController extends PageWithPostController
{
    public string $template = "register";
    public string $title = "Register";

    public function onGet(RequestMetadata $request): bool
    {
        if (Session::isLoggedIn())
        {
            $this->redirect("/");
        }
        return true;
    }

    public function onPost(RequestMetadata $request): bool
    {
        $username = @$_POST["username"] ?? "";
        if (strlen($username) < 3)
        {
            $this->addAlert(AlertType::ERROR, "Username must be at least 3 characters long");
        }
        else if (strlen($username) > 20)
        {
            $this->addAlert(AlertType::ERROR, "Username must be at most 20 characters long");
        }
        
        if (!preg_match(User::USERNAME_REGEX, $username))
        {
            $this->addAlert(AlertType::ERROR, "Username contains invalid characters");
        }

        $password = @$_POST["password"] ?? "";
        if (strlen($password) < 8)
        {
            $this->addAlert(AlertType::ERROR, "Password must be at least 8 characters long");
        }
        else if (strlen($password) > 100)
        {
            $this->addAlert(AlertType::ERROR, "Password must be at most 100 characters long");
        }

        $confirmPassword = @$_POST["confirm_password"] ?? "";
        if ($password != $confirmPassword)
        {
            $this->addAlert(AlertType::ERROR, "Passwords do not match");
        }
        
        $succeeded = (count($this->data->alerts) == 0);
        if ($succeeded)
        {
            $this->redirect("/");
        }
        else
        {
            $this->data->username = @$_POST["username"] ?? "";
            $this->data->remember = (isset($_POST["remember"]) && $_POST["remember"] == "on");
        }
        return true;
    }
}