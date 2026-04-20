<?php
namespace CustoDesk\Page\Register;

use CustoDesk\Page\Common\AlertType;
use CustoDesk\Page\Common\PageWithPostController;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;

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
        $this->addAlert(AlertType::ERROR, "Not implemented");
        $this->data->username = @$_POST["username"] ?? "";
        $this->data->remember = @$_POST["remember"] == "on";
        return true;
    }
}