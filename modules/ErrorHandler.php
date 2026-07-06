<?php
namespace CustoDesk;

use CustoDesk\Page\Common\AlertType;
use CustoDesk\TemplateUtils\TemplateUtilsDelegate;
use CustoDesk\TemplateUtils\VFL;
use DateTime;
use DateTimeZone;

use function CustoDesk\rootpath;

class ErrorHandler
{
    private static bool $disabled = false;
    public static array $errors = [];

    public static function init()
    {
        set_error_handler(self::class . "::handleNonFatal");
        register_shutdown_function(self::class . "::handleFatal");
    }

    private static function errorTypeStr(int $type): string
    {
        return match($type)
        {
            E_ERROR => "Fatal error",
            E_WARNING => "Warning",
            E_PARSE => "Parse error",
            E_NOTICE => "Notice",
            E_CORE_ERROR => "Core error",
            E_CORE_WARNING => "Core warning",
            E_COMPILE_ERROR => "Compile error",
            E_COMPILE_WARNING => "Compile warning",
            E_DEPRECATED => "Deprecated",
            E_USER_ERROR => "Fatal error",
            E_USER_WARNING => "Warning",
            E_USER_NOTICE => "Notice",
            E_USER_DEPRECATED => "Deprecated",
        };
    }

    private static function errorAlertType(int $type): AlertType
    {
        return match($type)
        {
            E_ERROR => AlertType::ERROR,
            E_WARNING => AlertType::WARNING,
            E_PARSE => AlertType::ERROR,
            E_NOTICE => AlertType::NORMAL,
            E_CORE_ERROR => AlertType::ERROR,
            E_CORE_WARNING => AlertType::WARNING,
            E_COMPILE_ERROR => AlertType::ERROR,
            E_COMPILE_WARNING => AlertType::WARNING,
            E_DEPRECATED => AlertType::NORMAL,
            E_USER_ERROR => AlertType::ERROR,
            E_USER_WARNING => AlertType::WARNING,
            E_USER_NOTICE => AlertType::NORMAL,
            E_USER_DEPRECATED => AlertType::NORMAL,
        };
    }   

    public static function handleFatal(): void
    {
        $err = error_get_last();
        if ($err === null || self::errorAlertType($err["type"]) != AlertType::ERROR)
        {
            ob_end_flush();
            return;
        }

        http_response_code(500);
        
        try
        {
            ob_end_clean();
            ob_start();

            $data = [
                "title" => "Fatal error"
            ];
            
            $debug = ServerConfig::isDebug();
            if ($debug)
            {
                $data += [
                    "file" => $err["file"],
                    "line" => $err["line"],
                    "message" => $err["message"]
                ];
            }
            else
            {
                $d = new DateTime(timezone: new DateTimeZone("GMT+0"));
                $log  = "CustoDesk Error Log\n";
                $log .= $d->format("Y-m-d H:i:s e") . "\n";
                $log .= "In " . $err["file"] . " (line " . $err["line"] . "):\n";
                $log .= "\n";
                $log .= $err["message"];

                $failureId = $d->format("YmdHisu");
                $dir = rootpath("logs");
                if (!is_dir($dir))
                    mkdir($dir);
                $filename = rootpath("logs/custodesk-" . $failureId . ".log");
                file_put_contents($filename, $log);

                $data += [
                    "failureId" => $failureId
                ];
            }

            Controller::$twig->addGlobal("data", (object)$data);
            Controller::$twig->addGlobal("custodesk", new TemplateUtilsDelegate());
            echo Controller::$twig->render("500.twig", []);
            ob_end_flush();
            exit();
        }
        catch (\Throwable $e)
        {
            $html  = "<h1>Internal Server Error</h1>";
            $html .= "<p>The server was unable to process your request.</p>";
            if (ServerConfig::isDebug())
            {
                $html .= "<p>Details:</p>";
                $html .= "<pre>";
                $html .= $e->__toString();
                $html .= "</pre>";
            }
            echo $html;
        }
    }

    public static function handleNonFatal(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ): bool
    {
        if (!self::$disabled && ServerConfig::isDebug())
        {
            self::$errors[] = (object)[
                "message" => $errstr,
                "file" => $errfile,
                "line" => $errline,
                "typeStr" => self::errorTypeStr($errno),
                "alertType" => self::errorAlertType($errno)
            ];
        }
        return true;
    }

    public static function disable(): void
    {
        self::$disabled = true;
    }

    public static function enable(): void
    {
        self::$disabled = false;
    }
}