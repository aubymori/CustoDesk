<?php
namespace CustoDesk\TemplateUtils;

use CustoDesk\ServerConfig;
use CustoDesk\Session;

class TemplateUtilsDelegate
{
    public VFL $vfl;
    public TimeUtilsDelegate $time;
    public SessionDelegate $session;

    public function __construct()
    {
        $this->vfl = VFL::getInstance();
        $this->time = new TimeUtilsDelegate();
        $this->session = new SessionDelegate();
    }

    /* TODO(aubymori): Remove and replace with a delegate for the Session class */
    public function isLoggedIn(): bool
    {
        return Session::isLoggedIn();
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
}