<?php
namespace CustoDesk\Page\Profile;

use CustoDesk\Page\Common\PageController;
use CustoDesk\Page\Common\User;
use CustoDesk\RequestMetadata;

class ProfileController extends PageController
{
    public string $template = "profile";

    public function onGet(RequestMetadata $request): bool
    {
        $username = $request->path[1];
        $user = User::fromUsername($username);
        if ($user == null)
        {
            return false;
        }

        $this->data->user = $user;
        $this->title = $user->username;

        return true;
    }
}