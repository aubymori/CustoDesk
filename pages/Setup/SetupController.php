<?php
namespace CustoDesk\Page\Setup;

use CustoDesk\DB;
use CustoDesk\Page\Common\AlertType;
use CustoDesk\Page\Common\PageWithPostController;
use CustoDesk\Page\Common\UserRole;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;
use CustoDesk\Util\UserUtils;
use CustoDesk\Util\TimeUtils;

class SetupController extends PageWithPostController
{
    public string $template = "setup";
    public string $title = "Setup";

    private function needsSetup(): bool
    {
        return null == DB::querySingle("SELECT id FROM users WHERE id=1");
    }

    public function onGet(RequestMetadata $request): bool
    {
        return $this->needsSetup();
    }

    public function onPost(RequestMetadata $request): bool
    {
        if (!$this->needsSetup())
            return false;
        
        $username = @$_POST["username"] ?? "";
        $username = trim($username);
        if (strlen($username) < 3)
        {
            $this->addAlert(AlertType::ERROR, "Username must be at least 3 characters long.");
            return true;
        }
        else if (strlen($username) > 15)
        {
            $this->addAlert(AlertType::ERROR, "Username must be at most 15 characters long.");
            return true;
        }

        if (!preg_match(UserUtils::USERNAME_REGEX, $username))
        {
            $this->addAlert(AlertType::ERROR, "Username contains invalid characters.");
            return true;
        }

        $password = @$_POST["password"] ?? "";
        $password = trim($password);
        if (strlen($password) < 8)
        {
            $this->addAlert(AlertType::ERROR, "Password must be at least 8 characters long.");
            return true;
        }
        else if (strlen($password) > 100)
        {
            $this->addAlert(AlertType::ERROR, "Password must be at most 100 characters long.");
            return true;
        }

        $confirmPassword = @$_POST["confirm_password"] ?? "";
        if ($password != $confirmPassword)
        {
            $this->addAlert(AlertType::ERROR, "Passwords do not match");
            return true;
        }

        $hashedPass = UserUtils::hashPassword($password);
        try
        {
            DB::exec("INSERT INTO users (username, password, created_at, role) VALUES (:username, :password, :created_at, :role)", [
                "username" => $username,
                "password" => $hashedPass,
                "created_at" => TimeUtils::now(),
                "role" => UserRole::ADMIN->value
            ]);
        }
        catch (\Throwable $e)
        {
            $this->addAlert(AlertType::ERROR, "Failed to create the account.");
            return true;
        }

        $id = UserUtils::idFromUsername($username);
        if (!Session::createSession($id, $password, (isset($_POST["remember"]) && $_POST["remember"] == "on")))
        {
            $this->addAlert(AlertType::ERROR, "The account was created, but could not be logged into.");
            return true;
        }

        $this->redirect("/");
        return true;
    }
}