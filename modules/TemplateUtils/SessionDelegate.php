<?php
namespace CustoDesk\TemplateUtils;

use CustoDesk\Page\Common\UserRole;
use CustoDesk\Session;

class SessionDelegate
{
    public function isLoggedIn(): bool
    {
        return Session::isLoggedIn();
    }

    public function getUserId(): int
    {
        return Session::getUserId();
    }

    public function getUsername(): string
    {
        return Session::getUsername();
    }

    public function getRole(): UserRole
    {
        return Session::getRole();
    }
}