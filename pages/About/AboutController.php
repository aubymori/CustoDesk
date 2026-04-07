<?php
namespace CustoDesk\Page\About;

use CustoDesk\Page\Common\PageController;
use CustoDesk\RequestMetadata;

class AboutController extends PageController
{
    public string $template = "about";
    public string $title = "About";

    public function onGet(RequestMetadata $request): bool
    {
        return true;
    }
}