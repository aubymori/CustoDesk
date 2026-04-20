<?php
namespace CustoDesk;

use CustoDesk\Util\TimeUtils;

class RateLimit
{
    private const MAX_REQUESTS = 200;
    private const PERIOD = 600; // 10 minutes

    private static function setExpireTime(string $ip, int $expire): void
    {
        $stmt = DB::prepare("UPDATE rate_limits SET expire_at = :expire WHERE ip = :ip");
        $stmt->bindValue(":expire", $expire, SQLITE3_INTEGER);
        $stmt->bindValue(":ip", $ip);
        $stmt->execute();
    }

    private static function setLastTime(string $ip, int $time): void
    {
        $stmt = DB::prepare("UPDATE rate_limits SET last_time = :last_time WHERE ip = :ip");
        $stmt->bindValue(":last_time", $time, SQLITE3_INTEGER);
        $stmt->bindValue(":ip", $ip);
        $stmt->execute();
    }

    private static function setAllowance(string $ip, int $allowance): void
    {
        $stmt = DB::prepare("UPDATE rate_limits SET allowance = :allowance WHERE ip = :ip");
        $stmt->bindValue(":allowance", $allowance, SQLITE3_INTEGER);
        $stmt->bindValue(":ip", $ip);
        $stmt->execute();
    }

    private static function getLastTime(string $ip): string
    {
        $stmt = DB::prepare("SELECT last_time FROM rate_limits WHERE ip = :ip");
        $stmt->bindValue(":ip", $ip);
        return $stmt->execute()->fetchArray(SQLITE3_ASSOC)["last_time"];
    }

    private static function getAllowance(string $ip): int
    {
        $stmt = DB::prepare("SELECT allowance FROM rate_limits WHERE ip = :ip");
        $stmt->bindValue(":ip", $ip);
        return $stmt->execute()->fetchArray(SQLITE3_ASSOC)["allowance"];
    }

    private static function purge(int $time): void
    {
        $stmt = DB::prepare("DELETE FROM rate_limits WHERE expire_at <= :time");
        $stmt->bindValue(":time", $time, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public static function check(): bool
    {
        if (!ServerConfig::shouldRateLimit())
            return true;

        $time = TimeUtils::now();
        $expire = $time + self::PERIOD;
        $ip = $_SERVER["REMOTE_ADDR"];
        self::purge($time);

        $stmt = DB::prepare("SELECT * FROM rate_limits WHERE ip=:ip");
        $stmt->bindValue(":ip", $ip);
        $res = $stmt->execute();
        $arr = $res->fetchArray(SQLITE3_ASSOC);
        if ($arr === false || count($arr) == 0)
        {
            $stmt = DB::prepare("INSERT INTO rate_limits VALUES (:ip, :last_time, :expire_at, :allowance)");
            $stmt->bindValue(":ip", $ip);
            $stmt->bindValue(":last_time", $time, SQLITE3_INTEGER);
            $stmt->bindValue(":expire_at", $expire, SQLITE3_INTEGER);
            $stmt->bindValue(":allowance", self::MAX_REQUESTS, SQLITE3_INTEGER);
            $stmt->execute();
        }

        $timePassed = $time - self::getLastTime($ip);
        self::setLastTime($ip, $time);
        $allowance = self::getAllowance($ip);
        $allowance += $timePassed * (int)(self::MAX_REQUESTS / self::PERIOD);

        if ($allowance > self::MAX_REQUESTS)
        {
            $allowance = self::MAX_REQUESTS;
        }

        if ($allowance < 1)
        {
            self::setAllowance($ip, $allowance);
            self::setExpireTime($ip, $expire);
            return false;
        }
        
        self::setAllowance($ip, $allowance - 1);
        self::setExpireTime($ip, $expire);
        return true;
    }
}