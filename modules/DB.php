<?php
namespace CustoDesk;

use SQLite3;
use SQLite3Stmt;

class DB
{
    private static SQLite3 $db;

    private const INIT_FILE = "include/init.sql";
    private const DB_FILE   = "custodesk.db";

    public static function init(): void
    {
        $shouldInit = !file_exists(self::DB_FILE);
        self::$db = new SQLite3(self::DB_FILE);
        if ($shouldInit)
        {
            $exec = file_get_contents(self::INIT_FILE);
            self::exec($exec);
        }
        register_shutdown_function(self::class . "::closeConnection");
    }

    public static function exec(string $query): bool
    {
        return self::$db->exec($query);
    }

    public static function prepare(string $query): SQLite3Stmt
    {
        return self::$db->prepare($query);
    }

    public static function closeConnection(): void
    {
        self::$db->close();
    }
}