<?php
namespace CustoDesk\Page\ControlPanel;

use CustoDesk\Page\Common\PageController;
use CustoDesk\Page\Common\User;
use CustoDesk\RequestMetadata;
use CustoDesk\Session;

class ControlPanelController extends PageController
{
    public string $template = "cpanel";
    public string $title = "Control Panel";

    public function onGet(RequestMetadata $request): bool
    {
        if (!Session::isLoggedIn())
        {
            $this->redirectToLogin();
        }

        $this->data->user = User::fromId(Session::getUserId());
        return true;
    }
}