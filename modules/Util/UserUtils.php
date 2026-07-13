<?php
namespace CustoDesk\Util;

use CustoDesk\ServerConfig;
use CustoDesk\DB;
use CustoDesk\Page\Common\UserRole;
use CustoDesk\Session;
use CustoDesk\TemplateUtils\VFL;
use function CustoDesk\rootpath;

class UserUtils
{
    public const USERNAME_REGEX = "/^[a-zA-Z0-9_]+$/";

    public static function saltPassword(#[\SensitiveParameter] string $password): string
    {
        $salt = ServerConfig::getSalt();
        return "$password" . "$" . "$salt";
    }

    public static function hashPassword(#[\SensitiveParameter] string $password): string
    {
        return password_hash(self::saltPassword($password), PASSWORD_BCRYPT);
    }

    public static function verifyPassword(int $userId, #[\SensitiveParameter] string $password): bool
    {
        $result = DB::querySingle("SELECT password FROM users WHERE id=:id", [
            "id" => $userId
        ]);
        if (!$result)
            return false;

        return password_verify(self::hashPassword($password), $result->password);
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

    public static function getRole(int $id): UserRole
    {
        $result = DB::querySingle("SELECT role FROM users WHERE id=:id", [
            "id" => $id
        ]);
        if (!$result)
            return UserRole::MEMBER;

        return UserRole::from($result->role);
    }

    public static function getAvatarUrl(int $id): string
    {
        $result = DB::querySingle("SELECT fname FROM user_avatars WHERE user_id=:id", [
            "id" => $id
        ]);
        if ($result)
        {
            return "/user_avatars/{$result->fname}.png";
        }
        return VFL::getInstance()->resolveImage("userIcon");
    }

    public static function removeAvatar(int $id): void
    {
        if (Session::getRole()->value < UserRole::MOD->value && $id != Session::getUserId())
        {
            return;   
        }

        $result = DB::querySingle("SELECT fname FROM user_avatars WHERE user_id=:id", [
            "id" => $id
        ]);
        if ($result)
        {
            unlink(rootpath("user_avatars/{$result->fname}.png"));
            DB::exec("DELETE from user_avatars WHERE user_id=:id", [
                "id" => $id
            ]);
        }
    }
}