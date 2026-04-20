<?php
namespace CustoDesk;

class ServerConfig
{
    private static string $salt;
    private static bool $debug;
    private static bool $rateLimit;

    private static function error(string $err): void
    {
        echo "<h1>Server Misconfiguration</h1>";
        echo "<p>" . $err . " See config.schema.jsonc for a rundown on how it should go.</p>";
        exit();
    }

    public static function init(): void
    {
        $content = file_get_contents("config.json");
        if ($content === false)
        {
            self::error("Config file does not exist.");
        }

        $json = json_decode($content);
        if ($json === null || !is_object($json))
        {
            self::error("Config file is not valid JSON.");
        }

        if (!isset($json->salt))
        {
            self::error("Config file does not contain salt.");
        }

        self::$salt = (string)$json->salt;
        self::$debug = @$json->debug ?? false;
        if (self::$debug)
        {
            self::$rateLimit = @$json->debugRateLimit ?? false;
        }
        else
        {
            self::$rateLimit = true;
        }
    }

    public static function getSalt(): string
    {
        return self::$salt;
    }

    public static function isDebug(): bool
    {
        return self::$debug;
    }

    public static function shouldRateLimit(): bool
    {
        return self::$rateLimit;
    }
}