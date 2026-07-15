<?php
namespace CustoDesk\Page\Admin;

use CustoDesk\Page\Common\User;
use CustoDesk\Page\Common\UserRole;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;

class DashboardController extends AdminPageController
{
    public UserRole $requiredRole = UserRole::MOD;
    public string $template = "admin/dashboard";
    public string $subpage = "dashboard";
    public string $title = "Dashboard";

    public function onGet(RequestMetadata $request): bool
    {
        return true;
    }
}