<?php
namespace CustoDesk\Page\Debug;

use CustoDesk\Page\Common\PageController;
use CustoDesk\RequestMetadata;
use CustoDesk\ServerConfig;

class DebugPageController extends PageController
{
    public function get(RequestMetadata $request): void
    {
        if (!ServerConfig::isDebug())
        {
            $this->dontEvenTry = true;
        }
        PageController::get($request);
    }

    public function post(RequestMetadata $request): void
    {
        if (!ServerConfig::isDebug())
        {
            $this->dontEvenTry = true;
        }
        PageController::post($request);
    }
}