<?php
namespace CustoDesk\Page\Home;

use CustoDesk\Page\Common\PageController;
use CustoDesk\RequestMetadata;

class HomeController extends PageController
{
    public string $template = "home";

    public function onGet(RequestMetadata $request): bool
    {
        // $db = new \SQLite3("custodesk.db");
        // $exec = file_get_contents("include/init.sql");
        // $db->exec($exec);
        // $db->close();
        return true;
    }
}