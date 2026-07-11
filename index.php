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
        /* Regular pages */
        "/" => Home\HomeController::class,
        "/about" => About\AboutController::class,
        "/login" => Login\LoginController::class,
        "/register" => Register\RegisterController::class,
        "/cpanel" => ControlPanel\ControlPanelController::class,
        "/cpanel/description" => ControlPanel\ControlPanelController::class,
        "/user/*" => Profile\ProfileController::class,

        /* Admin pages */
        "/admin/setup" => Setup\SetupController::class,
        "/admin" => Admin\DashboardController::class,
        "/admin/alerts" => Admin\AlertsController::class,
        "/admin/invite_keys" => Admin\InviteKeysController::class,
        "/admin/update_tables" => Admin\UpdateTablesController::class,

        /* Debug pages */
        "/debug/rich_edit" => Debug\RichEditController::class,

        /* 404 */
        "default" => Common\PageController::class,
    ],
    "post" => [
        /* Regular pages */
        "/login" => Login\LoginController::class,
        "/register" => Register\RegisterController::class,
        "/cpanel" => ControlPanel\ControlPanelController::class,
        "/cpanel/description" => ControlPanel\ControlPanelController::class,

        /* Admin pages */
        "/admin/setup" => Setup\SetupController::class,
        "/admin/alerts" => Admin\AlertsController::class,
        "/admin/invite_keys" => Admin\InviteKeysController::class,

        /* Debug pages */
        "/debug/rich_edit" => Debug\RichEditController::class,

        /* 404 */
        "default" => Common\PageController::class,
    ],
]);

Controller::run();

// Output buffer is either cleaned or flushed by ErrorHandler::handleFatal.
// It must be done this way because that function is a shutdown function and
// if we call ob_end_flush here it will execute *before* that is called.