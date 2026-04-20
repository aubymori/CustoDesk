<?php
namespace CustoDesk\Page\Home;

use CustoDesk\DB;
use CustoDesk\Page\Common\PageController;
use CustoDesk\RequestMetadata;

class HomeController extends PageController
{
    public string $template = "home";

    public function onGet(RequestMetadata $request): bool
    {
        trigger_error("Hi", E_USER_WARNING);
        //throw new \Error("Hi");
        return true;
    }
}