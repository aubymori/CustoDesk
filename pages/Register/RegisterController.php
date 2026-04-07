<?php
namespace CustoDesk\Page\Register;

use CustoDesk\Page\Common\PageWithPostController;
use CustoDesk\RequestMetadata;

class RegisterController extends PageWithPostController
{
    public string $template = "register";
    public string $title = "Register";

    public function onGet(RequestMetadata $request): bool
    {
        return true;
    }

    public function onPost(RequestMetadata $request): bool
    {
        $this->data->error = "Not implemented";
        return true;
    }
}