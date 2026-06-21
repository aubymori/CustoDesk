<?php
namespace CustoDesk;

class Cookie
{
    // 400 days
    // https://developer.chrome.com/blog/cookie-max-age-expires
    private const EXPIRE_TIME = 34560000;

    public static function set(string $name, mixed $value, bool $remember = true): void
    {
        setcookie(
            name: $name,
            value: (string)$value,
            expires_or_options: $remember ? (time() + self::EXPIRE_TIME) : 0,
            path: "/");
    }

    public static function delete(string $name): void
    {
        setcookie(
            name: $name,
            value: "",
            expires_or_options: 1,
            path: "/");
    }

    public static function get(string $name): ?string
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }
}