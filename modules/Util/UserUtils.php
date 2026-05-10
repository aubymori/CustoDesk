<?php
namespace CustoDesk\Util;

use CustoDesk\ServerConfig;
use CustoDesk\DB;

class UserUtils
{
    public const USERNAME_REGEX = "/^[a-zA-Z0-9_]+$/";

    public static function saltPassword(#[\SensitiveParameter] string $password): string
    {
        $salt = ServerConfig::getSalt();
        return "$password" . "$" . "$salt";
    }

    public static function idFromUsername(string $username): int
    {
        $result = DB::querySingle("SELECT id FROM users WHERE username=:username COLLATE NOCASE", [
            "username" => $username
        ]);
        if (!$result)
            return -1;

        return $result->id;
    }

    public static function usernameFromId(int $id): string
    {
        $result = DB::querySingle("SELECT username FROM users WHERE id=:id", [
            "id" => $id
        ]);
        if (!$result)
            return "";

        return $result->username;
    }
}