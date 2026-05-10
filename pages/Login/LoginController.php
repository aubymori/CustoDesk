<?php
namespace CustoDesk\Page\Login;

use CustoDesk\Page\Common\AlertType;
use CustoDesk\Page\Common\PageWithPostController;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;
use CustoDesk\Util\UserUtils;

class LoginController extends PageWithPostController
{
    public string $template = "login";
    public string $title = "Log in";

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
        if (Session::isLoggedIn())
        {
            $this->redirect("/");
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
        
        $this->redirect("/");
        return true;

fail:
        $this->data->username = $username;
        $this->data->remember = $remember;
        return true;
    }
}