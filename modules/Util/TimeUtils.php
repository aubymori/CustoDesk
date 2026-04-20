<?php
namespace CustoDesk\Util;

use DateTime;
use DateTimeZone;

class TimeUtils
{
    private static DateTimeZone $tz;
    private static DateTimeZone $tzUTC;

    public static function __initStatic()
    {
        $tz = "GMT+0";
        if (isset($_COOKIE["tz"]))
        {
            $tzOffset = (int)$_COOKIE["tz"];
            $tzHours = -((int)($tzOffset / 60));
            $tz = "GMT" . ($tzHours >= 0 ? "+" : "") . $tzHours . ":" . abs($tzOffset % 60);
        }
        self::$tz = new DateTimeZone($tz);
        self::$tzUTC = new DateTimeZone("GMT+0");
    }

    public static function format(int $timestamp, string $format): string
    {
        $dt = new DateTime("now", self::$tzUTC);
        $dt->setTimestamp($timestamp);
        $dt->setTimezone(self::$tz);
        return $dt->format($format);
    }

    public static function now(): int
    {
        $dt = new DateTime("now", self::$tzUTC);
        return $dt->getTimestamp();
    }

    public static function formatDate(int $timestamp): string
    {
        return self::format($timestamp, "n/j/Y");
    }

    public static function formatTime(int $timestamp): string
    {
        return self::format($timestamp, "g:i A");
    }

    public static function formatDateTime(int $timestamp): string
    {
        return self::format($timestamp, "n/j/Y g:i A");
    }
}