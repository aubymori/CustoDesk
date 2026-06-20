<?php
namespace CustoDesk;

use CustoDesk\Page\Common\UserRole;
use CustoDesk\Util\TimeUtils;
use CustoDesk\Util\UserUtils;

class Session
{
    private static int $userId = -1;
    private static string $username = "";
    private static UserRole $role = UserRole::MEMBER;
    private static bool $loggedIn = false;

    public static function init(): void
    {
        $session = Cookie::get("session");
        if (!$session)
            return;

        $result = DB::querySingle("SELECT user_id FROM sessions WHERE secret=:secret", [
            "secret" => $session
        ]);
        if (!$result)
            return;

        self::$userId = $result->user_id;
        self::$username = UserUtils::usernameFromId($result->user_id);
        self::$role = UserUtils::getRole($result->user_id);
        self::$loggedIn = true;
    }

    public static function createSession(int $userId, #[\SensitiveParameter] string $password, bool $remember): bool
    {
        do
        {
            $newId = rand();
            $result = DB::querySingle("SELECT id FROM sessions WHERE id=:id", [
                "id" => $newId
            ]);
        }
        while ($result != null);

        $result = DB::querySingle("SELECT password FROM users WHERE id=:id", [
            "id" => $userId
        ]);
        if (!$result)
            return false;

        $saltedPass = UserUtils::saltPassword($password);
        if (!password_verify($saltedPass, $result->password))
            return false;

        $secret = password_hash("$newId:{$result->password}", PASSWORD_BCRYPT);
        DB::exec("INSERT INTO sessions VALUES (:id, :secret, :user_id, :created_at, :user_agent)", [
            "secret" => $secret,
            "user_id" => $userId,
            "created_at" => TimeUtils::now(),
            "user_agent" => $_SERVER["HTTP_USER_AGENT"]
        ]);

        Cookie::set("session", $secret);

        return true;
    }

    public static function isLoggedIn(): bool
    {
        return self::$loggedIn;
    }

    public static function getUserId(): int
    {
        return self::$userId;
    }

    public static function getUsername(): string
    {
        return self::$username;
    }

    public static function getRole(): UserRole
    {
        return self::$role;
    }
}