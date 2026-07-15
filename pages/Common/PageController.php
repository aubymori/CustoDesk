<?php
namespace CustoDesk\Page\Common;

use CustoDesk\Controller;
use CustoDesk\DB;
use CustoDesk\ErrorHandler;
use CustoDesk\Page\Login\LoginController;
use CustoDesk\Page\Register\RegisterController;
use CustoDesk\RateLimit;
use CustoDesk\RequestMetadata;
use CustoDesk\ServerConfig;
use CustoDesk\Session;
use CustoDesk\TemplateUtils\TemplateUtilsDelegate;

class PageController
{
    public string $title = "";
    public string $template = "404";
    public string $subpage = "";
    protected ?object $data = null;
    protected bool $dontEvenTry = false;

    protected function redirect(string $url): void
    {
        header("Location: " . $url);
        exit();
    }

    protected function redirectToLogin(): void
    {
        $this->redirect("/login?next=" . urlencode($_SERVER["REQUEST_URI"]));
    }

    protected function addAlert(AlertType $type, string $text, bool $raw = false, bool $dismissible = false): void
    {
        if (!$raw)
            $text = htmlspecialchars($text);
        $this->data->alerts[] = new Alert($type, $text, $dismissible);
    }

    private function doRequest(RequestMetadata $request, string $method): void
    {
        if (!Session::isLoggedIn() && ServerConfig::requireInviteKeys())
        {
            $class = get_class($this);
            if ($class != LoginController::class && $class != RegisterController::class)
            {
                $this->redirectToLogin();
            }
        }

        if (!$this->data)
            $this->data = (object)[];
        $this->data->alerts = [];
        if (RateLimit::check())
        {
            if ($this->dontEvenTry || !$this->{$method}($request))
            {
                $this->template = "404";
                http_response_code(404);
            }
            $this->data->title = $this->title;
            if ($this->subpage)
            {
                $this->data->subpage = $this->subpage;
            }
        }
        else
        {
            http_response_code(429);
            $this->template = "rate_limit";
            $this->data->title = "Too Many Requests";
        }

        $alerts = DB::query("SELECT type, text FROM alerts");
        foreach ($alerts as $alert)
        {
            $type = AlertType::tryFrom($alert->type) ?? AlertType::NORMAL;
            $this->addAlert($type, $alert->text, true);
        }
        
        $this->data->errors = ErrorHandler::$errors;
        Controller::$twig->addGlobal("data", $this->data);
        Controller::$twig->addGlobal("custodesk", new TemplateUtilsDelegate());
        echo Controller::$twig->render($this->template . ".twig", []);
    }

    public function get(RequestMetadata $request): void
    {
        $this->doRequest($request, "onGet");
    }

    public function onGet(RequestMetadata $request): bool
    {
        return false;
    }

    public function post(RequestMetadata $request): void
    {
        $this->doRequest($request, "onPost");
    }

    public function onPost(RequestMetadata $request): bool
    {
        return false;
    }
}