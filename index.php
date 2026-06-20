<?php
namespace CustoDesk\Page;
error_reporting(0);
ob_start();
$GLOBALS["start_time"] = microtime(true);

require "vendor/autoload.php";
require "include/autoload.php";

use CustoDesk\Controller;
use CustoDesk\Cookie;
use CustoDesk\DB;
use CustoDesk\ServerConfig;
use CustoDesk\ErrorHandler;
use CustoDesk\Session;

DB::init();
ServerConfig::init();
ErrorHandler::init();
Session::init();

Controller::redirect([
    "/logout" => function()
    {
        Cookie::delete("session");
        return "/";
    }
]);

Controller::route([
    "get" => [
        "/" => Home\HomeController::class,
        "/about" => About\AboutController::class,
        "/login" => Login\LoginController::class,
        "/register" => Register\RegisterController::class,
        "/cpanel" => ControlPanel\ControlPanelController::class,
        "/setup" => Setup\SetupController::class,
        "default" => Common\PageController::class,
    ],
    "post" => [
        "/login" => Login\LoginController::class,
        "/register" => Register\RegisterController::class,
        "/setup" => Setup\SetupController::class,
    ],
]);

Controller::run();

// Output buffer is either cleaned or flushed by ErrorHandler::handleFatal.
// It must be done this way because that function is a shutdown function and
// if we call ob_end_flush here it will execute *before* that is called.