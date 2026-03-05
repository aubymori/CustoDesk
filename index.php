<?php
namespace CustoDesk\Page;

require "vendor/autoload.php";
require "include/autoload.php";

use CustoDesk\Controller;

Controller::route([
    "get" => [
        "/" => Home\HomeController::class,
        "/about" => About\AboutController::class,
        "/login" => Login\LoginController::class,
        "default" => Common\PageController::class,
    ],
    "post" => [
        "/login" => Login\LoginController::class,
    ],
]);

Controller::run();