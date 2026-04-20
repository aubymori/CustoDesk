<?php
namespace CustoDesk\Page;
$GLOBALS["start_time"] = microtime(true);

require "vendor/autoload.php";
require "include/autoload.php";

use CustoDesk\Controller;


Controller::route([
    "get" => [
        "/" => Home\HomeController::class,
        "/about" => About\AboutController::class,
        "/login" => Login\LoginController::class,
        "/register" => Register\RegisterController::class,
        "default" => Common\PageController::class,
    ],
    "post" => [
        "/login" => Login\LoginController::class,
        "/register" => Register\RegisterController::class,
    ],
]);

Controller::run();