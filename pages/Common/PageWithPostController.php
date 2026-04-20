<?php
namespace CustoDesk\Page\Common;

use CustoDesk\Controller;
use CustoDesk\RequestMetadata;
use CustoDesk\TemplateUtils\TemplateUtilsDelegate;

class PageWithPostController extends PageController
{
    public string $template = "404";
    protected object $data;

    public function post(RequestMetadata $request): void
    {
        $this->data = (object)[];
        if (!$this->onPost($request))
        {
            $this->template = "404";
            http_response_code(404);
        }
        
        Controller::$twig->addGlobal("data", $this->data);
        Controller::$twig->addGlobal("custodesk", new TemplateUtilsDelegate());
        echo Controller::$twig->render($this->template . ".twig", []);
        exit();
    }

    public function onPost(RequestMetadata $request): bool
    {
        return false;
    }
}