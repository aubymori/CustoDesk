<?php
namespace CustoDesk\TemplateUtils;

use CustoDesk\Session;

class TemplateUtilsDelegate
{
    public VFL $vfl;
    public TimeUtilsDelegate $time;

    public function __construct()
    {
        $this->vfl = VFL::getInstance();
        $this->time = new TimeUtilsDelegate();
    }

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
}