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
        //DB::exec("DROP TABLE users");
        return true;
    }
}