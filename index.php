<?php
namespace CustoDesk\Page;
error_reporting(0);
$GLOBALS["start_time"] = microtime(true);

require "vendor/autoload.php";
require "include/autoload.php";

use CustoDesk\Controller;
use CustoDesk\ServerConfig;

ServerConfig::init();

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