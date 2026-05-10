<?php
namespace CustoDesk\Page\Home;

use CustoDesk\DB;
use CustoDesk\Page\Common\PageController;
use CustoDesk\Page\Common\User;
use CustoDesk\RateLimit;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;

class HomeController extends PageController
{
    public string $template = "home";

    public function onGet(RequestMetadata $request): bool
    {
        if (Session::isLoggedIn())
        {
            $this->data->user = User::fromId(Session::getUserId());
        }
        return true;
    }
}