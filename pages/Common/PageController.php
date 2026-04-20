<?php
namespace CustoDesk\Page\Common;

use CustoDesk\Controller;
use CustoDesk\ErrorHandler;
use CustoDesk\RateLimit;
use CustoDesk\RequestMetadata;
use CustoDesk\TemplateUtils\TemplateUtilsDelegate;

class PageController
{
    public string $title = "";
    public string $template = "404";
    protected object $data;

    protected function redirect(string $url): void
    {
        header("Location: " . $url);
        exit();
    }

    protected function addAlert(string $type, string $text): void
    {
        $this->data->alerts[] = new Alert($type, $text);
    }

    public function get(RequestMetadata $request): void
    {
        $this->data = (object)[];
        $this->data->alerts = [];
        if (RateLimit::check())
        {
            if (!$this->onGet($request))
            {
                $this->template = "404";
                http_response_code(404);
            }
            $this->data->title = $this->title;
        }
        else
        {
            http_response_code(429);
            $this->template = "rate_limit";
            $this->data->title = "Too Many Requests";
        }
        
        $this->data->errors = ErrorHandler::$errors;
        Controller::$twig->addGlobal("data", $this->data);
        Controller::$twig->addGlobal("custodesk", new TemplateUtilsDelegate());
        echo Controller::$twig->render($this->template . ".twig", []);
        //exit();
    }

    public function onGet(RequestMetadata $request): bool
    {
        return false;
    }
}