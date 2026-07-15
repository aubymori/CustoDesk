<?php
namespace CustoDesk\Page\Admin;

use CustoDesk\Page\Common\PageController;
use CustoDesk\Page\Common\User;
use CustoDesk\Page\Common\UserRole;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;

class AdminPageController extends PageController
{
    protected UserRole $requiredRole = UserRole::ADMIN;

    private function setUp(): void
    {
        if (!Session::getRole()->isAtLeast($this->requiredRole))
        {
            $this->dontEvenTry = true;
        }
        else
        {
            $this->data = (object)[];
            $this->data->user = User::fromId(Session::getUserId());
        }
    }

    public function get(RequestMetadata $request): void
    {
        $this->setUp();
        PageController::get($request);
    }

    public function post(RequestMetadata $request): void
    {
        $this->setUp();
        PageController::post($request);
    }
}