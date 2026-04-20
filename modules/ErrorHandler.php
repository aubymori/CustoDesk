<?php
namespace CustoDesk;

class ErrorHandler
{
    public static array $errors = [];

    public static function init()
    {
        set_error_handler(self::class . "::handle");
    }

    private static function handleFatal(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ): void
    {
        http_response_code(500);
        try
        {
            Controller::$twig->addGlobal("data", (object)[
                "title" => "Fatal error",
                "type" => match($errno)
                {
                    E_ERROR => "Error",
                    E_PARSE => "Parse error",
                    E_CORE_ERROR => "Core error",
                    E_COMPILE_ERROR => "Compile error",
                    E_USER_ERROR => "User error",
                },
                "string" => $errstr,
                "file" => $errfile,
                "line" => $errline
            ]);
            echo Controller::$twig->render("500.twig");
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
        }
    }

    private static function handleNonFatal(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ): void
    {

    }

    public static function handle(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ): bool
    {
        switch ($errno)
        {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                self::handleFatal($errno, $errstr, $errfile, $errline);
                break;
            case E_WARNING:
            case E_NOTICE:
            case E_COMPILE_WARNING:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
            case E_USER_NOTICE:
                self::handleNonFatal($errno, $errstr, $errfile, $errline);
                break;
        }
        return true;
    }
}