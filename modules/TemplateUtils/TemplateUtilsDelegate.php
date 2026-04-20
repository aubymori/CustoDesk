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
}