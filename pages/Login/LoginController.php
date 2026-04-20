<?php
namespace CustoDesk\Page\Login;

use CustoDesk\Page\Common\AlertType;
use CustoDesk\Page\Common\PageWithPostController;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;

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
        $this->addAlert(AlertType::ERROR, "Not implemented");
        $this->data->username = @$_POST["username"] ?? "";
        return true;
    }
}