<?php
namespace CustoDesk\Page\Register;

use CustoDesk\DB;
use CustoDesk\Page\Common\Alert;
use CustoDesk\Page\Common\AlertType;
use CustoDesk\Page\Common\PageController;
use CustoDesk\RequestMetadata;
use CustoDesk\ServerConfig;
use CustoDesk\Session;
use CustoDesk\Util\UserUtils;
use CustoDesk\Util\TimeUtils;

class RegisterController extends PageController
{
    public string $template = "register";
    public string $title = "Register";

    public function onGet(RequestMetadata $request): bool
    {
        if (Session::isLoggedIn())
        {
            $this->redirect("/");
        }

        $this->data->requireInviteKey = ServerConfig::requireInviteKeys();

        return true;
    }

    public function onPost(RequestMetadata $request): bool
    {
        if (Session::isLoggedIn())
        {
            $this->redirect("/");
        }
        
        $this->data->requireInviteKey = ServerConfig::requireInviteKeys();

        $username = @$_POST["username"] ?? "";
        $username = trim($username);
        if (strlen($username) < 3)
        {
            $this->addAlert(AlertType::ERROR, "Username must be at least 3 characters long.");
            goto fail;
        }
        else if (strlen($username) > 20)
        {
            $this->addAlert(AlertType::ERROR, "Username must be at most 20 characters long.");
            goto fail;
        }
        
        if (!preg_match(UserUtils::USERNAME_REGEX, $username))
        {
            $this->addAlert(AlertType::ERROR, "Username contains invalid characters.");
            goto fail;
        }

        $password = @$_POST["password"] ?? "";
        $password = trim($password);
        if (strlen($password) < 8)
        {
            $this->addAlert(AlertType::ERROR, "Password must be at least 8 characters long.");
            goto fail;
        }
        else if (strlen($password) > 100)
        {
            $this->addAlert(AlertType::ERROR, "Password must be at most 100 characters long.");
            goto fail;
        }

        $confirmPassword = @$_POST["confirm_password"] ?? "";
        if ($password != $confirmPassword)
        {
            $this->addAlert(AlertType::ERROR, "Passwords do not match.");
            goto fail;
        }

        if (-1 != UserUtils::idFromUsername($username))
        {
            $this->addAlert(AlertType::ERROR, "That username is already in use.");
            goto fail;
        }

        if (ServerConfig::requireInviteKeys())
        {
            $key = @$_POST["invite_key"] ?? "";
            $result = DB::querySingle("SELECT user_id FROM invite_keys WHERE key=:key", [
                "key" => $key
            ]);
            if ($result == null || $result->user_id != null)
            {
                $this->addAlert(AlertType::ERROR, "Bad invite key.");
                goto fail;
            }
        }
        
        $hashedPass = UserUtils::hashPassword($password);
        $createdAt = TimeUtils::now();
        try
        {
            DB::exec("INSERT INTO users (username, password, created_at) VALUES (:username, :password, :created_at)", [
                "username" => $username,
                "password" => $hashedPass,
                "created_at" => $createdAt
            ]);
        }
        catch (\Throwable $e)
        {
            $this->addAlert(AlertType::ERROR, "Failed to create the account.");
            goto fail;
        }

        $id = UserUtils::idFromUsername($username);
        if (!Session::createSession($id, $password, (isset($_POST["remember"]) && $_POST["remember"] == "on")))
        {
            $this->addAlert(AlertType::ERROR, "The account was created, but could not be logged into.");
            goto fail;
        }

        if (ServerConfig::requireInviteKeys())
        {
            $key = @$_POST["invite_key"] ?? "";
            DB::exec("UPDATE invite_keys SET user_id=:user_id, used_at=:used_at WHERE key=:key", [
                "user_id" => $id,
                "used_at" => $createdAt,
                "key" => $key
            ]);
        }

        $this->redirect("/");
        return true;
    
fail:
        $this->data->username = @$_POST["username"] ?? "";
        $this->data->remember = (isset($_POST["remember"]) && $_POST["remember"] == "on");
        return true;
    }
}