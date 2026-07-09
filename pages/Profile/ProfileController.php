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
        $id = $request->path[1];
        if ((int)$id != $id || $id == 0)
        {
            return false;
        }

        $user = User::fromId((int)$id);
        if ($user == null)
        {
            return false;
        }

        $this->data->user = $user;
        $this->title = $user->username;

        return true;
    }
}