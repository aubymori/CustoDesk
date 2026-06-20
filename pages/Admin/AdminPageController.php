<?php
namespace CustoDesk\Page\Admin;

use CustoDesk\Page\Common\PageController;
use CustoDesk\Page\Common\UserRole;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;

class AdminPageController extends PageController
{
    protected UserRole $requiredRole = UserRole::ADMIN;

    public function get(RequestMetadata $request): void
    {
        if (!Session::getRole()->isAtLeast($this->requiredRole))
        {
            $this->dontEvenTry = true;
        }
        PageController::get($request);
    }
}