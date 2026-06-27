<?php
namespace CustoDesk\TemplateUtils;

use CustoDesk\ServerConfig;
use CustoDesk\Session;

class TemplateUtilsDelegate
{
    public VFL $vfl;
    public TimeUtilsDelegate $time;
    public SessionDelegate $session;
    public UserUtilsDelegate $users;
    public Modules $modules;

    public function __construct()
    {
        $this->vfl = VFL::getInstance();
        $this->time = new TimeUtilsDelegate();
        $this->session = new SessionDelegate();
        $this->users = new UserUtilsDelegate();
        $this->modules = new Modules();
    }

    public function getRenderTime(): string
    {
        $diff = microtime(true) - $GLOBALS["start_time"];
        if ($diff < 0.001)
        {
            return "less than 0.001";
        }
        else
        {
            return number_format($diff, 3);
        }
    }

    public function isDebug(): bool
    {
        return ServerConfig::isDebug();
    }

    public function getRichEditChoice(): string
    {
        $cookie = @$_COOKIE["rich_edit_choice"] ?? "bbcode";
        if (!in_array($cookie, ["bbcode", "markdown"], true))
            return "bbcode";
        return $cookie;
    }
}