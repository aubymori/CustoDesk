<?php
namespace CustoDesk;

use SQLite3;
use SQLite3Result;
use SQLite3Stmt;

class DB
{
    private static SQLite3 $db;

    private const INIT_FILE = "include/init.sql";
    private const DB_FILE   = "custodesk.db";

    public static function update(): void
    {
        $exec = file_get_contents(self::INIT_FILE);
        self::$db->exec($exec);
    }

    public static function init(): void
    {
        $shouldInit = !file_exists(self::DB_FILE);
        self::$db = new SQLite3(self::DB_FILE);
        if ($shouldInit)
        {
            self::update();
        }
        register_shutdown_function(self::class . "::closeConnection");
    }

    public static function exec(string $query, array $args = []): SQLite3Result
    {
        $stmt = self::$db->prepare($query);
        foreach ($args as $name => $value)
        {
            $type = SQLITE3_TEXT;
            switch (gettype($value))
            {
                case "boolean":
                case "integer":
                    $type = SQLITE3_INTEGER;
                    break;
                case "double":
                    $type = SQLITE3_FLOAT;
                    break;
            }

            $stmt->bindValue(":$name", $value, $type);
        }
        
        $res = $stmt->execute();
        if (false === $res)
        {
            $sql = $stmt->getSQL(true);
            throw new \Error("SQL query '$sql' failed");
        }
        $res->finalize();
        return $res;
    }

    public static function query(string $query, array $args = []): array
    {
        $res = self::exec($query, $args);
        $result = [];
        while (false !== ($arr = $res->fetchArray(SQLITE3_ASSOC)))
        {
            $result[] = (object)$arr;
        }
        return $result;
    }

    public static function querySingle(string $query, array $args = []): ?object
    {
        $result = self::query($query, $args);
        $size = count($result);
        if ($size > 1)
        {
            throw new \Error("Expected size of 0 or 1, got $size");
        }
        return $size == 1 ? $result[0] : null;
    }

    public static function prepare(string $query): SQLite3Stmt|false
    {
        return self::$db->prepare($query);
    }

    public static function closeConnection(): void
    {
        self::$db->close();
    }
}