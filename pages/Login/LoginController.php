<?php
namespace CustoDesk\Page\Login;

use CustoDesk\Page\Common\AlertType;
use CustoDesk\Page\Common\PageController;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;
use CustoDesk\Util\UserUtils;

class LoginController extends PageController
{
    public string $template = "login";
    public string $title = "Log in";

    public function onGet(RequestMetadata $request): bool
    {
        if (Session::isLoggedIn())
        {
            $this->redirect("/");
        }

        if (isset($_GET["next"]))
        {
            $this->addAlert(AlertType::ERROR, "Please log in.");
        }

        return true;
    }

    public function onPost(RequestMetadata $request): bool
    {
        if (Session::isLoggedIn())
        {
            $this->redirect("/");
        }

        if (isset($_GET["next"]))
        {
            $this->addAlert(AlertType::ERROR, "Please log in.");
        }

        $username = @$_POST["username"] ?? "";
        $password = @$_POST["password"] ?? "";
        $remember = (isset($_POST["remember"]) && $_POST["remember"] == "on");

        $userId = UserUtils::idFromUsername(trim($username));
        if ($userId == -1 || !Session::createSession($userId, trim($password), $remember))
        {
            $this->addAlert(AlertType::ERROR, "Incorrect username or password.");
            goto fail;
        }
        
        $this->redirect(isset($_GET["next"]) ? $_GET["next"] : "/");
        return true;

fail:
        $this->data->username = $username;
        $this->data->remember = $remember;
        return true;
    }
}