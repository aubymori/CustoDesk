<?php
namespace CustoDesk\Page\Admin;

use CustoDesk\RequestMetadata;

class InviteKeysController extends AdminPageController
{
    public string $template = "admin/invite_keys";

    public function onGet(RequestMetadata $request): bool
    {
        $this->data->subpage = "invite_keys";
        return true;
    }

    public function onPost(RequestMetadata $request): bool
    {
        $this->data->subpage = "invite_keys";
        return true;
    }
}