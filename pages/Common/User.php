<?php
namespace CustoDesk\Page\Common;

use CustoDesk\DB;
use CustoDesk\Util\UserUtils;

class User
{
    public string $username;
    public int $id;
    public string $avatarUrl;
    public int $createdAt = 0;
    public UserRole $role = UserRole::MEMBER;

    private static function fromDBResult(object $result, bool $minimalInfo): self
    {
        $new = new self;
        $new->id = $result->id;
        $new->username = $result->username;
        $new->avatarUrl = UserUtils::getAvatarUrl($new->id);
        if (!$minimalInfo)
        {
            $new->createdAt = $result->created_at;
            $new->role = UserRole::from($result->role);
        }
        return $new;
    }

    public static function fromQuery(string $condition, array $args, bool $minimalInfo = false): ?self
    {
        $vars = "username, id";
        if (!$minimalInfo)
            $vars .= ", created_at, role";
        $result = DB::querySingle("SELECT $vars FROM users $condition", $args);
        if (!$result)
            return null;
        return self::fromDBResult($result, $minimalInfo);
    }

    public static function fromQueryMultiple(string $condition, array $args, bool $minimalInfo = false): array
    {
        $vars = "username, id";
        if (!$minimalInfo)
            $vars .= ", created_at, role";
        $results = DB::query("SELECT $vars FROM users $condition", $args);
        $users = [];
        foreach ($results as $result)
        {
            $users[] = self::fromDBResult($result, $minimalInfo);
        }
        return $users;
    }

    public static function fromId(int $id, bool $minimalInfo = false): ?self
    {
        return self::fromQuery("WHERE id=:id", [ "id" => $id ], $minimalInfo);
    }

    public static function fromIds(array $ids, bool $minimalInfo = false): array
    {
        $result = [];
        foreach ($ids as $id)
        {
            $user = self::fromId($id, $minimalInfo);
            if ($user != null)
                $result[] = $user;
        }
        return $result;
    }

    public static function fromUsername(string $username, bool $minimalInfo = false): ?self
    {
        return self::fromQuery("WHERE username=:username COLLATE NOCASE", [ "username" => $username ], $minimalInfo);
    }
}